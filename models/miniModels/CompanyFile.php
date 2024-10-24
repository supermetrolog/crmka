<?php

namespace app\models\miniModels;

use app\components\Media\Media;
use app\kernel\common\models\AR\AR;
use app\models\Company;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "company_file".
 *
 * @property int         $id
 * @property int         $company_id
 * @property string      $name
 * @property string      $filename
 * @property string      $size
 * @property string|null $type
 * @property string|null $created_at
 *
 * @property Company     $company
 */
class CompanyFile extends AR
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'company_file';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['company_id', 'name', 'filename', 'size'], 'required'],
			[['company_id'], 'integer'],
			[['created_at'], 'safe'],
			[['name', 'filename', 'size', 'type'], 'string', 'max' => 255],
			[['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'company_id' => 'Company ID',
			'name'       => 'Name',
			'filename'   => 'Filename',
			'size'       => 'Size',
			'type'       => 'Type',
			'created_at' => 'Created At',
		];
	}

	/**
	 * Gets query for [[Company]].
	 *
	 * @return ActiveQuery
	 */
	public function getCompany(): ActiveQuery
	{
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	/**
	 * @return string
	 * @throws InvalidConfigException
	 */
	public function getSrc(): string
	{
		return Yii::$container->get(Media::class)->getUrl($this->filename);
	}

	// TODO: 0_0 Удалить когда перейдем на ресурсы
	public function fields(): array
	{
		$fields = parent::fields();

		$fields['src'] = fn() => $this->getSrc();

		return $fields;
	}
}
