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
                $this->filename = Yii::$app->getSecurity()->generateRandomString(15) . '.' . $file->extension;
                $filepath = '@/app/public_html/uploads/' . $this->filename;
                if (!$file->saveAs($filepath)) {
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
            $this->filename = Yii::$app->getSecurity()->generateRandomString(15) . '.' . $file->extension;

            $filepath = 'uploads/' . $this->filename;

            if (!$file->saveAs($filepath, false)) {
                $this->addError('UploadFile', 'Ошибка загрузки файлов!');
            }
            return true;
        } else {
            return false;
        }
    }
}
