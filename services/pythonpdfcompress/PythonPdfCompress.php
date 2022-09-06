<?php

namespace app\services\pythonpdfcompress;

use Exception;
use Google\Service\AIPlatformNotebooks\Expr;

class PythonPdfCompress
{
    public const COMPRESS_TYPE_VERY_HIGH = 4;
    public const COMPRESS_TYPE_HIGH = 3;
    public const COMPRESS_TYPE_AVERAGE = 2;
    public const COMPRESS_TYPE_LOW = 1;


    private $outpath;
    private $inpath;
    private $compressLevel;
    private $pythonScriptPath;
    private $pythonPath;
    private $error;
    public function __construct($python_path, $python_script_path, $inpath, $outpath, $compress_level = self::COMPRESS_TYPE_HIGH)
    {
        $this->pythonPath = $python_path;
        $this->pythonScriptPath = $python_script_path;
        $this->outpath = $outpath;
        $this->inpath = $inpath;
        $this->compressLevel = $compress_level;
    }
    private function validate()
    {
        if (!$this->pythonScriptPath || !$this->outpath || !$this->inpath) {
            $this->error = "properties cannot be null";
            return false;
        }

        if (!file_exists($this->inpath)) {
            $this->error = "inpath file not exist";
            return false;
        }
        if ($this->inpath == $this->outpath) {
            $this->error = "inpath and outpath cannot be equal";
            return false;
        }
        return true;
    }
    public function Compress()
    {
        if (!$this->validate()) {
            throw new Exception("Validate error: " . $this->error);
        }
        $cmd = $this->getCmdString();
        $output = null;
        $return_code = null;
        exec($cmd, $output, $return_code);
        if ($return_code) {
            throw new Exception(implode(', ', $output));
        }
    }

    // Т.к не получается сохранить пдф с тем же именем, приходится удалять оригинал и заменять его на уменьшенную версию
    public function deleteOriginalFileAndChangeFileName()
    {
        $this->removeOriginalFile();
        $this->renameFile();
    }
    public function renameFile()
    {
        return rename($this->outpath, $this->inpath);
    }
    public function removeOriginalFile()
    {
        return unlink($this->inpath);
    }
    private function getCmdString()
    {
        return "{$this->pythonPath} {$this->pythonScriptPath} -o {$this->outpath} -c {$this->compressLevel} {$this->inpath}";
    }
}
