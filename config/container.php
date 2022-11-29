<?php

use League\Flysystem\Ftp\FtpConnectionOptions;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem\FtpFilesystem;
use Supermetrolog\Synchronizer\lib\repositories\onefile\interfaces\RepositoryInterface;
use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
use Supermetrolog\Synchronizer\services\sync\interfaces\AlreadySynchronizedRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\BaseRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\TargetRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;

return [
    'definitions' => [
        Synchronizer::class => function ($container, $params) {
            $baseRepository = $container->get(BaseRepositoryInterface::class, $params['baseRepository']);
            $targetRepository = $container->get(TargetRepositoryInterface::class, $params['targetRepository']);
            $alreadySynchronizedRepository = $container->get(AlreadySynchronizedRepositoryInterface::class, $params['alreadySynchronizedRepository']);
            $logger = $container->get(LoggerInterface::class);

            return new Synchronizer($baseRepository, $targetRepository, $alreadySynchronizedRepository, $logger);
        },

        BaseRepositoryInterface::class => function ($container, $params) {
            return Filesystem::getInstance($params['dirpath']);
        },
        TargetRepositoryInterface::class => function ($container, $params) {
            $ftpConnOpt = FtpConnectionOptions::fromArray($params['ftp']);
            return FtpFilesystem::getInstance($params['dirpath'], $ftpConnOpt);
        },
        AlreadySynchronizedRepositoryInterface::class => function ($container, $params) {
            $repo = $container->get(RepositoryInterface::class, $params['repository']);
            return new OneFile($repo, $params['filename']);
        },
        RepositoryInterface::class => function ($container, $params) {
            return $container->get(TargetRepositoryInterface::class, $params);
        },
        LoggerInterface::class => function () {
            $logger = new Logger("app");
            $logger->pushHandler(new StreamHandler(STDOUT, Logger::INFO));
            return $logger;
        },
    ]
];
