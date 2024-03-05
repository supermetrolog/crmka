<?php

namespace app\models\crane;

use app\helpers\JsonFieldNormalizer;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\Json;

/**
 * This is the model class for table "l_cranes".
 *
 * @property int $id
 * @property string|null $title
 * @property int|null $object_id
 * @property string|null $photo
 * @property float|null $crane_capacity
 * @property int|null $crane_type
 * @property int|null $crane_location
 * @property int|null $crane_beam
 * @property int|null $crane_beams_amount
 * @property int|null $crane_span
 * @property int|null $crane_hoisting
 * @property string|null $crane_controls
 * @property int|null $crane_hooks
 * @property int|null $crane_hook_height
 * @property int|null $crane_condition
 * @property int|null $crane_supervision
 * @property int|null $crane_documents
 * @property string|null $description
 * @property int|null $activity
 * @property int|null $order_row
 * @property int|null $deleted
 * @property int|null $publ_time
 * @property int|null $last_update
 */
class Crane extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_cranes';
    }

    /**
     * @return Connection
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('db_old');
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['object_id', 'crane_type', 'crane_location', 'crane_beam', 'crane_beams_amount', 'crane_span', 'crane_hoisting', 'crane_hooks', 'crane_hook_height', 'crane_condition', 'crane_supervision', 'crane_documents', 'activity', 'order_row', 'deleted', 'publ_time', 'last_update'], 'integer'],
            [['crane_capacity'], 'number'],
            [['title'], 'string', 'max' => 50],
            [['photo'], 'string', 'max' => 2000],
            [['crane_controls'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'object_id' => 'Object ID',
            'photo' => 'Photo',
            'crane_capacity' => 'Crane Capacity',
            'crane_type' => 'Crane Type',
            'crane_location' => 'Crane Location',
            'crane_beam' => 'Crane Beam',
            'crane_beams_amount' => 'Crane Beams Amount',
            'crane_span' => 'Crane Span',
            'crane_hoisting' => 'Crane Hoisting',
            'crane_controls' => 'Crane Controls',
            'crane_hooks' => 'Crane Hooks',
            'crane_hook_height' => 'Crane Hook Height',
            'crane_condition' => 'Crane Condition',
            'crane_supervision' => 'Crane Supervision',
            'crane_documents' => 'Crane Documents',
            'description' => 'Description',
            'activity' => 'Activity',
            'order_row' => 'Order Row',
            'deleted' => 'Deleted',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
        ];
    }

    /**
     * @return array
     */
    public function getControls(): array
    {
        return JsonFieldNormalizer::jsonToArrayIntElements($this->crane_controls);
    }

    /**
     * @return array
     */
    public function getPhotos(): array
    {
        return Json::decode($this->photo) ?? [];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $f = parent::fields();

        $f['crane_controls'] = function () { return $this->getControls(); };
        $f['photo'] = function () { return $this->getPhotos(); };

        return $f;
    }

    /**
     * @return array
     */
    public function extraFields(): array
    {
        $f = parent::extraFields();

        $f['controls'] = function () { CraneControll::find()->where(['id' => $this->getControls()])->all(); };

        return $f;
    }

    /**
     * @return ActiveQuery
     */
    public function getBeam(): ActiveQuery
    {
        return $this->hasOne(CraneBeam::class, ['id' => 'crane_beam']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBeamAmount(): ActiveQuery
    {
        return $this->hasOne(CraneBeamAmount::class, ['id' => 'crane_beam_amount']);
    }

    /**
     * @return ActiveQuery
     */
    public function getState(): ActiveQuery
    {
        return $this->hasOne(CraneState::class, ['id' => 'crane_condition']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(CraneLocation::class, ['id' => 'crane_location']);
    }

    /**
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(CraneType::class, ['id' => 'crane_type']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHoisting(): ActiveQuery
    {
        return $this->hasOne(CraneHoisting::class, ['id' => 'crane_hoisting']);
    }
}
