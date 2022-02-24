<?php

namespace app\models\miniModels;

use Yii;
use app\models\User;
use app\models\Contact;
use app\exceptions\ValidationErrorHttpException;

/**
 * This is the model class for table "contact_comment".
 *
 * @property int $id
 * @property int|null $contact_id
 * @property int $author_id
 * @property string $comment
 * @property string|null $created_at
 *
 * @property User $author
 * @property Contact $contact
 */
class ContactComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contact_id', 'author_id'], 'integer'],
            [['author_id', 'comment'], 'required'],
            [['created_at'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_id' => 'Contact ID',
            'author_id' => 'Author ID',
            'comment' => 'Comment',
            'created_at' => 'Created At',
        ];
    }

    public static function createComment($post_data)
    {
        $model = new ContactComment();
        if ($model->load($post_data, '') && $model->save()) {
            return ['message' => 'Комментарий добавлен', 'data' => self::find()->with(['author' => function ($query) {
                $query->with('userProfile');
            }])->where(['contact_comment.id' => $model->id])->limit(1)->one()];
        } else {
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Contact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }
}
