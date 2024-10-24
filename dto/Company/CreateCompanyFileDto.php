<?php

namespace app\dto\Company;

use yii\base\BaseObject;
use yii\web\UploadedFile;

class CreateCompanyFileDto extends BaseObject
{
	public int $company_id;

	public UploadedFile $file;
}