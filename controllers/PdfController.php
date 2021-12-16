<?php

namespace app\controllers;

use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Yii;
// require_once 'dompdf/autoload.inc.php';
class PdfController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['*'],
                'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login', 'create', 'index', 'options', 'fuck'],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $options = new Options();
        // $options->set('defaultFont', 'helvetica');
        $options->set('isRemoteEnabled', true);
        $options->set('isJavascriptEnabled', true);
        $dompdf = new Dompdf($options);
        $html = $this->renderPartial('index');

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("pdf.pdf", ['Attachment' => false]);

        return $html;
    }
    public function actionFuck()
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isJavascriptEnabled', true);
        $dompdf = new Dompdf($options);
        $html = $this->renderPartial('index');

        // $dompdf->loadHtml($html);
        // $dompdf->setPaper('A4');
        // $dompdf->render();
        // $dompdf->stream("pdf.pdf", ['Attachment' => false]);

        return $html;
    }
}
