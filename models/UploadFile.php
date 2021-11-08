<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

class UploadFile extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;
    /**
     * @var String
     */
    public $filename;
    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, xls, xlsx, ppt, pptp, doc, docx, txt', 'maxFiles' => 10],
        ];
    }

    public function uploadAll()
    {
        if ($this->validate()) {
            foreach ($this->files as $file) {
                $this->filename = $this->generateFileName($file);
                $filepath = $this->getFullPathForSave();
                if (!$file->saveAs($filepath, false)) {
                    $this->addError('UploadFile', 'Ошибка загрузки файлов!');
                }
            }
            return true;
        } else {
            return false;
        }
    }
    public function uploadOne($file)
    {
        if ($this->validate()) {
            $this->filename = $this->generateFileName($file);
            $filepath = $this->getFullPathForSave();
            if (!$file->saveAs($filepath, false)) {
                $this->addError('UploadFile', 'Ошибка загрузки файлов!');
            }
            return true;
        } else {
            return false;
        }
    }
    public function getFullPathForSave()
    {
        return 'uploads/' . $this->filename;
    }
    public function generateFileName($file)
    {
        return $file->name . '-' . Yii::$app->getSecurity()->generateRandomString(15) . '.' . $file->extension;
    }
}
