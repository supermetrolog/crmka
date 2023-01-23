<?php


namespace app\services\backuper\databases\interfaces;

interface DbImporterInterface
{
    public function import(string $filename): bool;
}
