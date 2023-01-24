<?php

namespace app\commands;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Supermetrolog\SynchronizerLocalToFTPBuilder\Builder;
use Yii;
use yii\console\Controller;

class SynchronizerController extends Controller
{
    private Builder $syncBuilder;
    private Logger $logger;
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->syncBuilder = new Builder();
        $this->logger = new Logger("synchronizer");
        $this->logger->pushHandler(new StreamHandler(STDOUT));

        $this->logger->info("SYNCHRONIZER START");
    }

    public function __destruct()
    {
        $this->logger->info("SYNCHRONIZER END");
    }

    public function actionIndex()
    {
        $this->logger->info("THIS PROJECT SYNC START...");
        $this->actionThisProject();
        $this->logger->info("OBJECTS PROJECT SYNC START...");
        $this->actionObjectsProject();
        $this->logger->info("FRONTEND SYNC START...");
        $this->actionFrontendProject();
    }
    public function actionThisProject()
    {
        $params = Yii::$app->params['synchronizer']['this_project'];

        $builder = $this->syncBuilder->create();
        $builder
            ->setLogger($this->logger)
            ->setSourceRepo($params['source_repo_dir_path'], ['runtime'])
            ->setTargetRepo(
                $params['target_repo_ftp_params']['host'],
                $params['target_repo_ftp_params']['root'],
                $params['target_repo_ftp_params']['username'],
                $params['target_repo_ftp_params']['password'],
            )
            ->setAlreadyRepo($builder->getTargetRepo(), $params['already_sync_repo_filename']);

        $synchronizer = $builder->build();
        $synchronizer->load();
        $synchronizer->sync();
    }
    public function actionObjectsProject()
    {
        $params = Yii::$app->params['synchronizer']['objects_project'];

        $builder = $this->syncBuilder->create();
        $builder
            ->setLogger($this->logger)
            ->setSourceRepo($params['source_repo_dir_path'])
            ->setTargetRepo(
                $params['target_repo_ftp_params']['host'],
                $params['target_repo_ftp_params']['root'],
                $params['target_repo_ftp_params']['username'],
                $params['target_repo_ftp_params']['password'],
            )
            ->setAlreadyRepo($builder->getTargetRepo(), $params['already_sync_repo_filename']);

        $synchronizer = $builder->build();
        $synchronizer->load();
        $synchronizer->sync();
    }
    public function actionFrontendProject()
    {
        $params = Yii::$app->params['synchronizer']['frontend_project'];

        $builder = $this->syncBuilder->create();
        $builder
            ->setLogger($this->logger)
            ->setSourceRepo($params['source_repo_dir_path'])
            ->setTargetRepo(
                $params['target_repo_ftp_params']['host'],
                $params['target_repo_ftp_params']['root'],
                $params['target_repo_ftp_params']['username'],
                $params['target_repo_ftp_params']['password'],
            )
            ->setAlreadyRepo($builder->getTargetRepo(), $params['already_sync_repo_filename']);

        $synchronizer = $builder->build();
        $synchronizer->load();
        $synchronizer->sync();
    }
    // public function actionThisProject()
    // {
    //     $synchronizer = Yii::$container->get(Synchronizer::class, Yii::$app->params['synchronizer']['this_project']);
    //     $synchronizer->load();
    //     $synchronizer->sync();
    // }

    // public function actionObjectsProject()
    // {
    //     $synchronizer = Yii::$container->get(Synchronizer::class, Yii::$app->params['synchronizer']['objects_project']);
    //     $synchronizer->load();
    //     $synchronizer->sync();
    // }

    // public function actionFrontendProject()
    // {
    //     $synchronizer = Yii::$container->get(Synchronizer::class, Yii::$app->params['synchronizer']['frontend_project']);
    //     $synchronizer->load();
    //     $synchronizer->sync();
    // }
}
