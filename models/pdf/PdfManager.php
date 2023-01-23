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
    public function __construct(Options $options, $name = null, $savePath)
    {
        $this->setSavePath($savePath);
        $this->filename = $name ? $name : $this->generateFilename();
        parent::__construct($options);
    }
    private function setSavePath($savePath)
    {
        $this->savePath = $savePath;
        if (is_dir($this->savePath)) return;
        mkdir($this->savePath, 0700);
    }
    public function save()
    {
        return file_put_contents($this->getPdfPath(), $this->output());
    }

    public function removeFile()
    {
        return unlink($this->getPdfPath());
    }
    private function generateFilename()
    {
        $randomString = Yii::$app->getSecurity()->generateRandomString(15);
        return $randomString . '.pdf';
    }
    public function getPdfPath()
    {
        if (!$this->savePath || !$this->filename) {
            throw new Exception('File not found');
        }

        return $this->savePath . "/" . $this->filename;
    }
}
