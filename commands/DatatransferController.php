<?php

declare(strict_types=1);

namespace app\commands;

use app\models\oldDb\OfferMix;
use app\models\oldDb\User;
use app\models\User as ModelsUser;
use yii\console\Controller;

class DatatransferController extends Controller
{
    public function actionSetUsers(): void
    {
        $oldUsers = User::find()->where(['is', 'user_id_new', null])->andWhere(['id' => array_keys(OfferMix::USERS)])->all();

        foreach ($oldUsers as $user) {
            $user->user_id_new = OfferMix::USERS[$user->id];
            $user->save(false);
        }
    }
}
