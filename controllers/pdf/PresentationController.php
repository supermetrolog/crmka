<?php

namespace app\controllers\pdf;

use app\behaviors\BaseControllerBehaviors;
use app\helpers\ArrayHelper;
use app\helpers\TranslateHelper;
use app\models\pdf\OffersPdf;
use app\models\pdf\PdfManager;
use app\services\pythonpdfcompress\PythonPdfCompress;
use Dompdf\Options;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\web\Controller;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Response;

class PresentationController extends Controller
{
	private const STRATEGY_PDF_MANAGER  = 'pdf-manager';
	private const STRATEGY_MICROSERVICE = 'microservice';

	private string $CURRENT_STRATEGY = self::STRATEGY_PDF_MANAGER;
	private bool   $useCompression   = false;

	public function behaviors(): array
	{
		$behaviors = parent::behaviors();

		return BaseControllerBehaviors::getBaseBehaviors($behaviors);
	}

	private function translate(string $text): string
	{
		return TranslateHelper::simpleTranslate($text);
	}

	/**
	 * @throws Exception
	 */
	private function compressPdf(string $inPath): void
	{
		$outPath = Yii::$app->params['pdf']['tmp_dir'] . '/' . Yii::$app->security->generateRandomString() . '.pdf';

		$pythonCompressor = new PythonPdfCompress(
			Yii::$app->params['pythonPath'],
			Yii::$app->params['compressorPath'],
			$inPath,
			$outPath
		);

		$pythonCompressor->Compress();

		// Т.к не получается сохранить пдф с тем же именем, приходится удалять оригинал и заменять его на уменьшенную версию

		$pythonCompressor->deleteOriginalFileAndChangeFileName();
	}

	/**
	 * @throws RangeNotSatisfiableHttpException
	 * @throws Exception
	 */
	public function actionIndex(): Response
	{
		$pdf = $this->generatePdfFromPayload($this->request->get());

		$path = ArrayHelper::getValue($pdf, 'path');
		$name = ArrayHelper::getValue($pdf, 'name');

		if ($this->useCompression) {
			$this->compressPdf($path);
		}

		return $this->sendPdf($path, $name);
	}

	/**
	 * @throws Exception
	 */
	public function actionHtml(): string
	{
		$model = new OffersPdf($this->request->get(), $this->request->getHostInfo());

		return $this->renderPartial('index', ['model' => $model]);
	}


	public function actionDownload(): Response
	{
		// TODO: Временное тестовое решение

		$consultantId = $this->request->post('consultant', 33);
		$isNew        = $this->request->post('is_new', 1);

		$objects = $this->request->post('objects', []);

		$payload = ArrayHelper::map(
			$objects,
			static fn($object) => [
				'object_id'   => ArrayHelper::getValue($object, 'object_id'),
				'original_id' => ArrayHelper::getValue($object, 'original_id'),
				'type_id'     => ArrayHelper::getValue($object, 'type_id'),
				'consultant'  => $consultantId,
				'is_new'      => $isNew
			]
		);

		$json = ArrayHelper::map($payload, [$this, 'generatePdfFromPayload']);

		return $this->asJson($json);
	}

	/**
	 * @throws Exception
	 */
	public function generatePdfFromPayload(array $payload): array
	{
		// TODO: Заменить на обращение к микросервису

		$model = new OffersPdf($payload, 'https://api.pennylane.pro/');

		$html = $this->renderPdfHtml($model);

		$name = $model->getPresentationName();

		return ArrayHelper::merge(
			['name' => $name],
			$this->generatePdf($html, $model->getPresentationName())
		);
	}

	private function renderPdfHtml(OffersPdf $offer): string
	{
		return $this->renderPartial('index', ['model' => $offer, 'title' => $offer->getPresentationTitle()]);
	}

	/**
	 * @throws InvalidConfigException
	 * @throws NotSupportedException
	 * @throws Exception
	 */
	private function generatePdf(string $html, string $filename): array
	{
		if ($this->CURRENT_STRATEGY === self::STRATEGY_PDF_MANAGER) {
			return $this->generatePdfUsingPdfManager($html, $filename);
		}

		if ($this->CURRENT_STRATEGY === self::STRATEGY_MICROSERVICE) {
			return $this->generatePdfUsingMicroservice($html, $filename);
		}

		throw new InvalidConfigException('Unknown pdf generation strategy');
	}

	/**
	 * @throws Exception
	 */
	private function generatePdfUsingPdfManager(string $html, string $filename): array
	{
		$pdfManager = new PdfManager(
			$this->createPdfManagerOptions(),
			$this->translate($filename),
			Yii::$app->params['pdf']['tmp_dir']
		);

		$pdfManager->loadHtml($html);

		$pdfManager->render();
		$pdfManager->save();

		$path = $pdfManager->getPdfPath();

		return [
			'path' => $path,
			'url'  => $this->request->getHostInfo() . '/runtime/pdf_tmp/' . basename($path)
		];
	}

	private function createPdfManagerOptions(): Options
	{
		return new Options([
			'isRemoteEnabled'     => true,
			'isJavascriptEnabled' => true,
			'defaultPaperSize'    => 'A4'
		]);
	}

	/**
	 * @throws NotSupportedException
	 */
	private function generatePdfUsingMicroservice(string $html, string $filename): array
	{
		// TODO: Описать логику работы с микросервисом

		throw new NotSupportedException('Not implemented');
	}

	/**
	 * @throws RangeNotSatisfiableHttpException
	 */
	private function sendPdf(string $path, string $name): Response
	{
		$content = file_get_contents($path);

		unlink($path);

		return $this->response->sendContentAsFile($content, $name, [
			'mimeType' => 'application/pdf',
			'inline'   => true,
		]);
	}
}
