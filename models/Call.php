<?php

namespace app\models;

use app\models\ActiveQuery\CallQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "call".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $contact_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 *
 * @property User $user
 */
class Call extends \app\kernel\common\models\AR\AR
{
    public static function tableName(): string
    {
        return 'call';
    }

    public function rules(): array
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'contact_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'contact_id' => 'Contact ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

	/**
	 * @return ActiveQuery
	 */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    public static function find(): CallQuery
    {
        return new CallQuery(get_called_class());
    }
}
