<?php

namespace app\controllers\pdf;

use yii\web\Controller;
use app\behaviors\BaseControllerBehaviors;
use app\models\pdf\OffersPdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Yii;

class PresentationController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ['*']);
    }

    public function actionIndex()
    {
        $model = new OffersPdf(Yii::$app->request->queryParams);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isJavascriptEnabled', true);
        $dompdf = new Dompdf($options);
        $html = $this->renderPartial('index', ['model' => $model]);
        // $html = $this->renderFile(Yii::getAlias('@app') . '/views/pdf/presentation/index.php', ['model' => $model]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("pdf.pdf", ['Attachment' => false]);

        return $html;
    }
    public function actionFuck()
    {
        $model = new OffersPdf(Yii::$app->request->queryParams);
        // echo "<pre>";
        // print_r($model->data);
        // var_dump($model->data->miniOffersMix[0]->id);
        $html = $this->renderPartial('index', ['model' => $model]);
        return $html;
    }
}
