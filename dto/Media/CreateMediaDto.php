<?php

declare(strict_types=1);

namespace app\dto\Media;

use yii\base\BaseObject;
use yii\web\UploadedFile;

class CreateMediaDto extends BaseObject
{
	public string       $path;
	public string       $category;
	public string       $model_type;
	public int          $model_id;
	public UploadedFile $uploadedFile;
	public string       $mime_type;
}
