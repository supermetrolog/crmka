<?php

namespace app\models\timeline;

use app\exceptions\ValidationErrorHttpException;
use app\models\miniModels\TimelineActionComment;
use yii\base\Model;
use Yii;

class AddActionComments extends Model
{
    public array $comments;

    public function __construct(array $comments)
    {
        $this->comments = $comments;
    }
    public function rules()
    {
        return [
            ['comments', 'required'],
        ];
    }
    public function add()
    {
        if (!$this->validate())
            throw new ValidationErrorHttpException($this->getErrorSummary(false));

        $tx = Yii::$app->db->beginTransaction();
        try {
            foreach ($this->comments as $comment) {
                $model = new TimelineActionComment($comment);
                if (!$model->save())
                    throw new ValidationErrorHttpException($model->getErrorSummary(false));
            }
            $tx->commit();
        } catch (\Throwable $th) {
            $tx->rollBack();
            throw $th;
        }
    }
}
