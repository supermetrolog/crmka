<?php

namespace app\models\pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Yii;

class PdfManager extends Dompdf
{
    public string $savePath;
    public string $filename;

    public function __construct(Options $options, string $name, string $savePath)
    {
        $this->setSavePath($savePath);
        $this->filename = $name ?: $this->generateFilename();
        parent::__construct($options);
    }
    private function setSavePath($savePath)
    {
        $this->savePath = $savePath;
        if (is_dir($this->savePath)) return;
        mkdir($this->savePath, 0700, true);
    }

	/**
	 * @throws Exception
	 */
	public function save()
    {
        return file_put_contents($this->getPdfPath(), $this->output());
    }

	/**
	 * @throws Exception
	 */
	public function removeFile(): bool
    {
        return unlink($this->getPdfPath());
    }
    private function generateFilename(): string
    {
        $randomString = Yii::$app->getSecurity()->generateRandomString(15);
        return $randomString . '.pdf';
    }

	/**
	 * @throws Exception
	 */
	public function getPdfPath(): string
    {
        if (!$this->savePath || !$this->filename) {
            throw new Exception('File not found');
        }

        return $this->savePath . "/" . $this->filename;
    }
}
