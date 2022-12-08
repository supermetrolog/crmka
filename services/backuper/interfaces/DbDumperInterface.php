<?php

namespace app\services\backuper\interfaces;

interface DbDumperInterface
{
    public function dump(): void;
    public function getDbName(): string;
    public function getFilename(): string;
    public function getContent(): string;
}
