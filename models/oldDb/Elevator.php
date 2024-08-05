<?php

namespace app\models\oldDb;

use app\helpers\JsonFieldNormalizer;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "l_elevators".
 *
 * @property int $id
 * @property int $object_id
 * @property string $title
 * @property string $description
 * @property string $photo
 * @property int $elevator
 * @property int $elevator_width
 * @property int $elevator_length
 * @property int $elevator_type
 * @property int $elevator_location
 * @property float $elevator_capacity
 * @property int $elevator_volume
 * @property string $elevator_controls
 * @property int $elevator_condition
 * @property int $elevator_supervision
 * @property int $elevator_documents
 * @property int $activity
 * @property int $order_row
 * @property int $deleted
 * @property int $publ_time
 * @property int $last_update
 */
class Elevator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'l_elevators';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_old');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id', 'title', 'description', 'photo', 'elevator', 'elevator_width', 'elevator_length', 'elevator_type', 'elevator_location', 'elevator_capacity', 'elevator_volume', 'elevator_controls', 'elevator_condition', 'elevator_supervision', 'elevator_documents', 'order_row', 'publ_time', 'last_update'], 'required'],
            [['object_id', 'elevator', 'elevator_width', 'elevator_length', 'elevator_type', 'elevator_location', 'elevator_volume', 'elevator_condition', 'elevator_supervision', 'elevator_documents', 'activity', 'order_row', 'deleted', 'publ_time', 'last_update'], 'integer'],
            [['elevator_capacity'], 'number'],
            [['title'], 'string', 'max' => 10],
            [['description'], 'string', 'max' => 300],
            [['photo'], 'string', 'max' => 2000],
            [['elevator_controls'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_id' => 'Object ID',
            'title' => 'Title',
            'description' => 'Description',
            'photo' => 'Photo',
            'elevator' => 'Elevator',
            'elevator_width' => 'Elevator Width',
            'elevator_length' => 'Elevator Length',
            'elevator_type' => 'Elevator Type',
            'elevator_location' => 'Elevator Location',
            'elevator_capacity' => 'Elevator Capacity',
            'elevator_volume' => 'Elevator Volume',
            'elevator_controls' => 'Elevator Controls',
            'elevator_condition' => 'Elevator Condition',
            'elevator_supervision' => 'Elevator Supervision',
            'elevator_documents' => 'Elevator Documents',
            'activity' => 'Activity',
            'order_row' => 'Order Row',
            'deleted' => 'Deleted',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
        ];
    }

	public function getElevatorControls(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->elevator_controls);
	}

	public function getPhoto(): array
	{
		return Json::decode($this->photo) ?? [];
	}

	public function fields()
	{
		$f = parent::fields();

		$f['elevator_controls'] = function () {
			return $this->getElevatorControls();
		};

		$f['photo'] = function () {
			return $this->getPhoto();
		};

		return $f;
	}
}
