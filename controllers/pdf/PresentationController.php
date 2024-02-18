<?php

namespace app\controllers\pdf;

use app\helpers\TranslateHelper;
use app\models\pdf\PdfManager;
use app\services\pythonpdfcompress\PythonPdfCompress;
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


	private function compressPdf()
	{
		$pdfCompressor = new PdfManager();
	}


	/**
	 * @throws RangeNotSatisfiableHttpException
	 * @throws Exception
	 */
	public function actionIndex(): Response
	{
		$pdfTmpDir = Yii::$app->params['pdf']['tmp_dir'];

		$model = new OffersPdf($this->request->get(), 'https://api.pennylane.pro/');

		$options = new Options();
		$options->set('isRemoteEnabled', true);
		$options->set('isJavascriptEnabled', true);

		$pdfManager = new PdfManager($options, $this->translate($model->getPresentationName()), $pdfTmpDir);

		$html   = $this->renderPartial('index', ['model' => $model]);

		$pdfManager->loadHtml($html);
		$pdfManager->setPaper('A4');
		$pdfManager->render();
		$pdfManager->save();

		$pyScriptPath = Yii::$app->params['compressorPath'];
		$pythonPath = Yii::$app->params['pythonPath'];
		$inPath = $pdfManager->getPdfPath();
		$outPath = $pdfTmpDir . "/" . Yii::$app->security->generateRandomString() . ".pdf";
		$pythonCompressor = new PythonPdfCompress($pythonPath, $pyScriptPath, $inPath, $outPath);
		$pythonCompressor->Compress();
		// Т.к не получается сохранить пдф с тем же именем, приходится удалять оригинал и заменять его на уменьшенную версию
		$pythonCompressor->deleteOriginalFileAndChangeFileName();
		$pdfContent = file_get_contents($pdfManager->getPdfPath());

		$pdfManager->removeFile();

		return Yii::$app->response->sendContentAsFile(
			$pdfContent,
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
