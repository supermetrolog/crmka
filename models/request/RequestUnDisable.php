<?php

declare(strict_types=1);

namespace app\models\request;

use Yii;
use yii\base\Model;
use app\models\Request;
use app\models\Timeline;
use app\exceptions\ValidationErrorHttpException;

class RequestUnDisable extends Model
{
    private Request $request;
    private Timeline $timeline;

    public function __construct(Request $request, Timeline $timeline)
    {
        $this->request = $request;
        $this->timeline = $timeline;
    }
    public function unDisableRequestAndTimeline()
    {
        $tx = Yii::$app->db->beginTransaction();
        try {
            $this->unDisableRequest();
            $this->unDisableTimeline();
            $tx->commit();
        } catch (\Throwable $th) {
            $tx->rollBack();
            throw $th;
        }
    }
    private function unDisableTimeline()
    {
        $this->timeline->status = Timeline::STATUS_ACTIVE;
        if (!$this->timeline->save())
            throw new ValidationErrorHttpException("fuck" . $this->timeline->getErrorSummary(false));
    }
    private function unDisableRequest()
    {
        $this->request->status = Request::STATUS_ACTIVE;
        $this->request->passive_why = null;
        $this->request->passive_why_comment = null;

        if (!$this->request->save(false))
            throw new ValidationErrorHttpException($this->request->getErrorSummary(false));
    }
}
