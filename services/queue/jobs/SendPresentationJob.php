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
    public function execute($q)
    {
        try {
            /** @var User $user */
            $user = User::find()->with(['userProfile'])->where(['id' => $this->model->user_id])->limit(1)->one();
            $data = [
                'emails' => $this->getEmails($user),
                'from' => $user->getEmailForSend(),
                'view' => 'presentation/index',
                'viewArgv' => ['userMessage' => $this->model->comment],
                'subject' => $this->model->subject,
                'username' => $user->getEmailUsername(),
                'password' => $user->getEmailPassword(),
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
    private function generatePresentation($offer)
    {
        $model = new OffersPdf($offer);
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isJavascriptEnabled', true);

        $pdfTmpDir = Yii::$app->params['pdf']['tmp_dir'];

        $pdfManager = new PdfManager($options, date("His") . "_" .  $model->getPresentationName(), $pdfTmpDir);

        $appPath = Yii::getAlias('@app');
        $html = Yii::$app->controller->renderFile($appPath . '/views/pdf/presentation/index.php', ['model' => $model]);

        $pdfManager->loadHtml($html);
        $pdfManager->setPaper('A4');
        $pdfManager->render();
        $pdfManager->save();

        $pyScriptPath = Yii::$app->params['compressorPath'];
        $pythonpath = Yii::$app->params['pythonPath'];
        $inpath = $pdfManager->getPdfPath();
        $outpath = $pdfTmpDir . "/" . Yii::$app->security->generateRandomString() . ".pdf";
        $pythonCompresser = new PythonPdfCompress($pythonpath, $pyScriptPath, $inpath, $outpath);
        $pythonCompresser->Compress();
        // Т.к не получается сохранить пдф с тем же именем, приходится удалять оригинал и заменять его на уменьшенную версию
        $pythonCompresser->deleteOriginalFileAndChangeFileName();
        return $pdfManager;
    }
    private function getFiles($user)
    {
        $files = [];
        foreach ($this->model->offers as  $offer) {
            $offer['consultant'] = $user->userProfile->mediumName;
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
        if ($user->email) {
            return array_merge($this->model->emails, [$user->email]);
        }
        return $this->model->emails;
    }
}
