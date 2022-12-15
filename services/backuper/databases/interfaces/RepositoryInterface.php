<?php

namespace app\services\backuper\databases\interfaces;

use Generator;

interface RepositoryInterface
{
    public function createFile(string $filename, string $content): void;
    public function removeFile(string $filename): void;
    /** @return string[] */
    public function getStream(): Generator;
}
