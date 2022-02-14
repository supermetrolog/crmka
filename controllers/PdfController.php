<?php

namespace app\controllers;

use app\models\pdf\Presentation;
use yii\web\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Yii;
use app\behaviors\BaseControllerBehaviors;
// require_once 'dompdf/autoload.inc.php';
class PdfController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ['*']);
    }

    public function actionIndex()
    {
        $model = new Presentation(Yii::$app->request->getQueryParam('consultant'));
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
        $model = new Presentation(Yii::$app->request->getQueryParam('consultant'));
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
