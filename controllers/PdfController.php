<?php

namespace app\controllers;

use app\models\pdf\Presentation;
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
        $model = new Presentation();
        $model->fetchData(Yii::$app->request->getQueryParam('original_id'), Yii::$app->request->getQueryParam('type_id'));
        $data = $model->getResponse();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isJavascriptEnabled', true);
        $dompdf = new Dompdf($options);
        $html = $this->renderPartial('index', ['data' => $data, 'model' => $model]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("pdf.pdf", ['Attachment' => false]);

        return $html;
    }
    public function actionFuck()
    {
        // $model = new Presentation();
        // $model->fetchData(Yii::$app->request->getQueryParam('original_id'), Yii::$app->request->getQueryParam('type_id'));
        // $data = $model->getResponse();
        // return $this->renderPartial('suck', [
        //     'data' => $data
        // ]);
        $model = new Presentation();
        $model->fetchData(Yii::$app->request->getQueryParam('original_id'), Yii::$app->request->getQueryParam('type_id'));
        $data = $model->getResponse();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isJavascriptEnabled', true);
        $dompdf = new Dompdf($options);
        $html = $this->renderPartial('index', ['data' => $data, 'model' => $model]);

        // $dompdf->loadHtml($html);
        // $dompdf->setPaper('A4');
        // $dompdf->render();
        // $dompdf->stream("pdf.pdf", ['Attachment' => false]);

        return $html;
    }
}
