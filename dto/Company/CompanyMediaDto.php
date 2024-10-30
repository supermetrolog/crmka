<?php

namespace app\dto\Company;

use yii\base\BaseObject;
use yii\web\UploadedFile;

class CompanyMediaDto extends BaseObject
{
	/** @var UploadedFile[] */
	public array $files = [];

	public ?UploadedFile $logo;
}