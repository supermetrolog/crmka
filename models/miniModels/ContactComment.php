<?php

namespace app\models\miniModels;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\Contact;
use app\models\User;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "contact_comment".
 *
 * @property int      $id
 * @property int|null $contact_id
 * @property int      $author_id
 * @property string   $comment
 * @property string   $created_at
 * @property ?string  $updated_at
 * @property ?string  $deleted_at
 *
 * @property User     $author
 * @property Contact  $contact
 */
class ContactComment extends AR
{
	public bool $useSoftCreate = true;
	public bool $useSoftUpdate = true;
	public bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'contact_comment';
	}

	public function rules(): array
	{
		return [
			[['contact_id', 'author_id'], 'integer'],
			[['author_id', 'comment'], 'required'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['comment'], 'string', 'max' => 255],
			[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'contact_id' => 'Contact ID',
			'author_id'  => 'Author ID',
			'comment'    => 'Comment',
			'created_at' => 'Created At',
		];
	}

	public function getAuthor(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'author_id']);
	}

	public function getContact(): ActiveQuery
	{
		return $this->hasOne(Contact::class, ['id' => 'contact_id']);
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}
}
