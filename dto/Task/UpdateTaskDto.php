<?php

declare(strict_types=1);

namespace app\dto\Task;

use app\models\User;
use yii\base\BaseObject;

class UpdateTaskDto extends BaseObject
{
	public User    $user;
	public ?string $message;
	public string  $title;
	public int     $status;
	public ?int    $created_by_id;
	public ?string $start = null;
	public ?string $end   = null;
	public array   $tagIds;
	public array   $observerIds;
	public array   $currentFiles;
}