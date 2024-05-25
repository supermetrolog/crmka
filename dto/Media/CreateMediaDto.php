<?php

declare(strict_types=1);

namespace app\dto\Media;

use yii\base\BaseObject;

class CreateMediaDto extends BaseObject
{
	public string $name;
	public string $original_name;
	public string $extension;
	public string $path;
	public string $category;
	public string $model_type;
	public int    $model_id;
}
