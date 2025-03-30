<?php

declare(strict_types=1);

namespace app\dto\Request;

class UpdateRequestDto extends AbstractRequestDto
{
	public int     $status;
	public ?int    $passive_why;
	public ?string $passive_why_comment;
}