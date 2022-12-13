<?php

namespace app\models\request;

use app\exceptions\ValidationErrorHttpException;
use app\models\Request;
use app\models\Timeline;
use Yii;
use yii\base\Model;

class RequestDisable extends Model
{
    private Request $request;
    public Timeline $timeline;
    public $passive_why;
    public $passive_why_comment;
    public function __construct(Request $request, Timeline $timeline, array $config)
    {
        $this->request = $request;
        $this->timeline = $timeline;
        parent::__construct($config);
    }
    public function rules()
    {
        return [
            [['passive_why', 'passive_why_comment'], 'required'],
            [['passive_why_comment'], 'string'],
            [['passive_why'], 'integer'],
            [['timeline'], 'validateTimeline'],
        ];
    }
    public function validateTimeline()
    {
        if ($this->timeline->request_id != $this->request->id) {
            $this->addError("timeline", "timeline does not belong to the request");
        }
    }
    public function disableRequestAndTimeline()
    {
        if (!$this->validate())
            throw new ValidationErrorHttpException($this->getErrorSummary(false));

        $tx = Yii::$app->db->beginTransaction();
        try {
            $this->disableRequest();
            $this->disableTimeline();
            $tx->commit();
        } catch (\Throwable $th) {
            $tx->rollBack();
            throw $th;
        }
    }
    private function disableTimeline()
    {
        $this->timeline->status = Timeline::STATUS_INACTIVE;
        if (!$this->timeline->save())
            throw new ValidationErrorHttpException("fuck" . $this->timeline->getErrorSummary(false));
    }
    private function disableRequest()
    {
        $this->request->status = Request::STATUS_PASSIVE;
        $this->request->passive_why = $this->passive_why;
        $this->request->passive_why_comment = $this->passive_why_comment;

        if (!$this->request->save(false))
            throw new ValidationErrorHttpException($this->request->getErrorSummary(false));
    }
}
