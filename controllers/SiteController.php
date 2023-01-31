<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\components\ConsoleLogger;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionIndex()
    {
        $queue = 'notifycations';
        $exchange = 'notify_topic';

        $conn = new AMQPStreamConnection(
            'localhost',
            5672,
            'guest',
            'guest'
        );
        $channel = $conn->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

        $channel->queue_bind($queue, $exchange);

        $toSend = new AMQPMessage("SUKA NAHUI", ['nigger' => 2]);
        $channel->basic_publish($toSend, $exchange);
        $channel->close();
        $conn->close();
    }
}
