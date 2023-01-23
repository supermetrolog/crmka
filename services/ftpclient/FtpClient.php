<?php

namespace app\services\ftpclient;

use app\services\backuper\databases\interfaces\RepositoryInterface;
use Generator;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\Ftp\FtpConnectionProvider;
use League\Flysystem\PathNormalizer;

class FtpClient extends Filesystem implements RepositoryInterface
{
    public function __construct(FilesystemAdapter $adapter, array $config = [], ?PathNormalizer $pathNormalizer = null)
    {
        parent::__construct($adapter, $config, $pathNormalizer);
    }
    public function createFile(string $filename, string $content): void
    {
        $this->write("./$filename", $content);
    }

    public static function getInstance(FtpConnectionOptions $options): self
    {
        $connProvider = new FtpConnectionProvider($options);
        $adapter = new FtpAdapter($options, $connProvider);
        return new self($adapter);
    }

    public function removeFile(string $filename): void
    {
        $this->delete("./$filename");
    }

    public function getStream(): Generator
    {
        $response = $this->listContents(".");
        /** @var  League\Flysystem\FileAttributes $file*/
        foreach ($response as $file) {
            yield $file->path();
        }
    }
}
