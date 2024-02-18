<?php

namespace app\controllers\pdf;

use app\helpers\TranslateHelper;
use Exception;
use yii\web\Controller;
use app\behaviors\BaseControllerBehaviors;
use app\models\pdf\OffersPdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Yii;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Response;

class PresentationController extends Controller
{
	public function behaviors()
	{
		$behaviors = parent::behaviors();

		return BaseControllerBehaviors::getBaseBehaviors($behaviors);
	}

	private function translate(string $text): string
	{
		return TranslateHelper::simpleTranslate($text);
	}

	/**
	 * @throws RangeNotSatisfiableHttpException
	 * @throws Exception
	 */
	public function actionIndex(): Response
	{
		$model = new OffersPdf($this->request->get(), $this->request->getHostInfo());

		$options = new Options();
		$options->set('isRemoteEnabled', true);
		$options->set('isJavascriptEnabled', true);
		$dompdf = new Dompdf($options);
		$html   = $this->renderPartial('index', ['model' => $model]);

		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4');
		$dompdf->render();

		return Yii::$app->response->sendContentAsFile(
			$dompdf->output(['Attachment' => false]),
			$this->translate($model->getPresentationName()),
			[
				'mimeType' => 'application/pdf',
				'inline'   => true,
			]
		);
	}

	/**
	 * @throws Exception
	 */
	public function actionHtml(): string
	{
		$model = new OffersPdf($this->request->get(), $this->request->getHostInfo());

		return $this->renderPartial('index', ['model' => $model]);
	}
}
