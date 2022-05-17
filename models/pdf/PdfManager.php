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

    public function __construct(Options $options)
    {
        $this->savePath = "tmp/";
        $this->filename = $this->generateFilename();
        parent::__construct($options);
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
        // $randomString = Yii::$app->getSecurity()->generateRandomString(15) . date('Y-m-d H:i:s') . time();
        $randomString = Yii::$app->getSecurity()->generateRandomString(15);
        // $hash = base64_encode(hash("sha256", $randomString, true));
        return $randomString . '.pdf';
    }
    public function getPdfPath()
    {
        if (!$this->savePath || !$this->filename) {
            throw new Exception('File not found');
        }

        return $this->savePath . $this->filename;
    }
}
