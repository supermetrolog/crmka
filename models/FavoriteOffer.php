<?php

namespace app\models;

use app\models\oldDb\OfferMix;
use app\models\User\User;

/**
 * This is the model class for table "favorite_offer".
 *
 * @property int         $id
 * @property int         $user_id
 * @property int         $complex_id
 * @property int         $object_id
 * @property int         $original_id
 * @property string|null $created_at
 *
 * @property User        $user
 */
class FavoriteOffer extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'favorite_offer';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['user_id', 'complex_id', 'object_id', 'original_id'], 'required'],
			[['user_id', 'complex_id', 'object_id', 'original_id'], 'integer'],
			[['created_at'], 'safe'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'          => 'ID',
			'user_id'     => 'User ID',
			'complex_id'  => 'Complex ID',
			'object_id'   => 'Object ID',
			'original_id' => 'Original ID',
			'created_at'  => 'Created At',
		];
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * Gets query for [[OfferMix]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getOffer()
	{
		return $this->hasOne(OfferMix::className(), ['original_id' => 'original_id']);
	}
}
