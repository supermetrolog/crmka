<?php

namespace app\services\queue\jobs;

use app\events\NotificationEvent;
use app\exceptions\ValidationErrorHttpException;
use app\models\letter\Letter;
use app\models\Notification;
use app\models\pdf\OffersPdf;
use app\models\pdf\PdfManager;
use app\models\SendPresentation;
use app\models\User;
use app\models\UserSendedData;
use app\services\emailsender\EmailSender;
use app\services\pythonpdfcompress\PythonPdfCompress;
use Dompdf\Options;
use Exception;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SendPresentationJob extends BaseObject implements JobInterface
{
    public SendPresentation $model;

    // Нужно, чтобы складывать сюда все сгенерированные пдфки и удалить их в конце
    private $pdfs = [];

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->model->validate();
        if ($this->model->hasErrors()) {
            throw new ValidationErrorHttpException($this->model->getErrorSummary(false));
        }
    }
    private function generatePresentation($offer)
    {
        $model = new OffersPdf($offer);
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isJavascriptEnabled', true);

        $appPath = Yii::getAlias('@app');

        $pdfManager = new PdfManager($options, date("His") . "_" .  $model->getPresentationName(), $appPath . "/public_html/tmp/");

        $html = Yii::$app->controller->renderFile($appPath . '/views/pdf/presentation/index.php', ['model' => $model]);

        $pdfManager->loadHtml($html);
        $pdfManager->setPaper('A4');
        $pdfManager->render();
        $pdfManager->save();

        $pyScriptPath = Yii::$app->params['compressorPath'];
        $pythonpath = Yii::$app->params['pythonPath'];
        $inpath = $pdfManager->getPdfPath();
        $outpath = $appPath . "/public_html/tmp/" . Yii::$app->security->generateRandomString() . ".pdf";
        $pythonCompresser = new PythonPdfCompress($pythonpath, $pyScriptPath, $inpath, $outpath);
        $pythonCompresser->Compress();
        // Т.к не получается сохранить пдф с тем же именем, приходится удалять оригинал и заменять его на уменьшенную версию
        $pythonCompresser->deleteOriginalFileAndChangeFileName();
        return $pdfManager;
    }
    private function getFrom($user)
    {
        $defaultFrom = [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
        if (!$user['email_username'] || !$user['email_password']) {
            return $defaultFrom;
        }
        if ($user['email']) {
            return [$user['email'] => $user['userProfile']['short_name']];
        }

        return $defaultFrom;
    }

    private function getUsername($user)
    {
        if (!$user['email_username'] || !$user['email_password']) {
            return Yii::$app->params['senderUsername'];
        }
        return $user['email_username'];
    }

    private function getPassword($user)
    {
        if (!$user['email_username'] || !$user['email_password']) {
            return Yii::$app->params['senderPassword'];
        }
        return $user['email_password'];
    }
    private function getFiles($user)
    {
        $files = [];
        foreach ($this->model->offers as  $offer) {
            $offer['consultant'] = $user['userProfile']['medium_name'];
            $pdf = $this->generatePresentation($offer);
            $files[] = $pdf->getPdfPath();
            $this->pdfs[] = $pdf;
        }
        return $files;
    }
    private function removeAllPdfs()
    {
        foreach ($this->pdfs as $pdf) {
            $pdf->removeFile();
        }
    }
    private function getEmails($user)
    {
        if ($user['email']) {
            return array_merge($this->model->emails, [$user['email']]);
        }
        return $this->model->emails;
    }
    public function execute($q)
    {
        try {
            $user = User::find()->with(['userProfile'])->where(['id' => $this->model->user_id])->limit(1)->one();
            $user = $user->toArray([], ['userProfile']);

            $data = [
                'emails' => $this->getEmails($user),
                'from' => $this->getFrom($user),
                'view' => 'presentation/index',
                'viewArgv' => ['userMessage' => $this->model->comment],
                'subject' => $this->model->subject,
                'username' => $this->getUsername($user),
                'password' => $this->getPassword($user),
                'files' => $this->getFiles($user)
            ];
            $emailSender = new EmailSender();
            $emailSender->load($data, '');
            $emailSender->validate();
            if ($emailSender->hasErrors()) {
                throw new Exception("EmailSender validation error: " . implode(', ', $emailSender->getErrorSummary(false)));
            }
            if (!$emailSender->send()) {
                throw new Exception("Email send error");
            }

            $this->removeAllPdfs();
        } catch (\Throwable $th) {
            $this->removeAllPdfs();
            $this->notifyUser($th->getMessage());
            $this->changeLetterStatus(Letter::STATUS_ERROR);
            throw $th;
        }
    }
    private function notifyUser($error)
    {
        Yii::$app->notify->notifyUser(new NotificationEvent([
            'consultant_id' => $this->model->user_id,
            'type' => Notification::TYPE_SYSTEM_DANGER,
            'title' => 'ошибка',
            'body' => "Ошибка отправки презентаций: {$error}. По контактам: " . implode(', ', $this->model->emails)
        ]));
    }
    private function changeLetterStatus($status)
    {
        $model = Letter::findOne($this->model->letter_id);
        $model->status = $status;
        $model->save(false);
    }
}
