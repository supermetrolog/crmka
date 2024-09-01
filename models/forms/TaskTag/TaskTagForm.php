<?php

declare(strict_types=1);

namespace app\models\forms\TaskTag;

use app\dto\TaskTag\TaskTagDto;
use app\kernel\common\models\Form\Form;
use Exception;

class TaskTagForm extends Form
{
	public string  $name;
	public ?string $description;
	public string  $color;

	public function rules(): array
	{
		return [
			[['name', 'color'], 'required'],
			[['name'], 'string', 'max' => 30],
			[['description'], 'string', 'max' => 255],
			[['color'], 'string', 'max' => 6],
		];
	}

	/**
	 * @return TaskTagDto
	 * @throws Exception
	 */
	public function getDto(): TaskTagDto
	{
		return new TaskTagDto([
			'name'        => $this->name,
			'description' => $this->description,
			'color'       => $this->color
		]);
	}
}