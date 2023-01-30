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
    private function translit($value)
    {
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya',

            'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
            'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
            'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
            'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
            'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
            'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
            'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
        );

        $value = strtr($value, $converter);
        return $value;
    }
    public function actionIndex()
    {
        $model = new OffersPdf(Yii::$app->request->queryParams);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isJavascriptEnabled', true);
        $dompdf = new Dompdf($options);
        $html = $this->renderPartial('index', ['model' => $model]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return Yii::$app->response->sendContentAsFile(
            $dompdf->output(['Attachment' => false]),
            $this->translit($model->getPresentationName()),
            [
                'mimeType' => 'application/pdf',
                'inline' => true,
            ]
        );
    }
    public function actionFuck()
    {
        $model = new OffersPdf(Yii::$app->request->queryParams);
        $html = $this->renderPartial('index', ['model' => $model]);
        return $html;
    }
}
