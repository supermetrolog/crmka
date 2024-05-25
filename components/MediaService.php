<?php

namespace app\components;

use yii\base\Component;

class MediaService extends Component
{
	public string $path = '';

	public function __construct($config = [])
	{
		$config['path'] = rtrim($config['path'], '\\/');

		parent::__construct($config);
	}
}
