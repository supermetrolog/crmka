<?php

namespace app\dto\ContactComment;

use yii\base\BaseObject;

class CreateContactCommentDto extends BaseObject
{
	public int    $contact_id;
	public int    $author_id;
	public string $comment;
}