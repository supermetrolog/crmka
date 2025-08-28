<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\BlockQuery;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\DealQuery;
use app\models\ActiveQuery\oldDb\OfferMixQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\Company\Company;
use app\models\oldDb\Complex;
use app\models\oldDb\OfferMix;
use Yii;

/**
 * This is the model class for table "deal".
 *
 * @property int           $id
 * @property int           $company_id            [СВЯЗЬ] с компанией
 * @property int           $competitor_company_id [СВЯЗЬ] с компанией (компания конкурент)
 * @property int|null      $request_id            [СВЯЗЬ] с запросом
 * @property int           $consultant_id         [СВЯЗЬ] с юзером
 * @property int|null      $area                  площадь сделки
 * @property int|null      $floorPrice            цена пола
 * @property int|null      $contractTerm          срок контракта
 * @property string|null   $dealDate              дата сделки
 * @property string|null   $clientLegalEntity     юр. лицо клиента в сделке
 * @property string|null   $description           описание
 * @property string|null   $name                  название сделки
 * @property int|null      $object_id             ID объекта из старой базы
 * @property int|null      $original_id           ID Block
 * @property int|null      $complex_id            ID комплекса из старой базы
 * @property int|null      $is_our                принадлежит ли сделка нашей компании
 * @property int|null      $is_competitor         принадлежит ли сделка  конкурентам
 * @property int           $type_id
 * @property string        $created_at
 * @property string|null   $updated_at
 * @property int|null      $formOfOrganization
 * @property int           $status
 * @property string        $visual_id
 *
 * @property-read Company  $company
 * @property-read Company  $competitor
 * @property-read User     $consultant
 * @property-read Request  $request
 * @property-read OfferMix $offer
 * @property-read Block    $block
 */
class Deal extends AR
{
	public const STATUS_DELETED = -1;

	public static function tableName(): string
	{
		return 'deal';
	}

	public function rules(): array
	{
		return [
			[['company_id', 'consultant_id', 'object_id', 'type_id', 'original_id', 'visual_id', 'complex_id'], 'required'],
			[['status', 'company_id', 'request_id', 'consultant_id', 'area', 'floorPrice', 'object_id', 'original_id', 'complex_id', 'competitor_company_id', 'is_our', 'is_competitor', 'contractTerm', 'formOfOrganization'], 'integer'],
			[['dealDate', 'created_at', 'updated_at'], 'safe'],
			[['clientLegalEntity', 'description', 'name', 'visual_id'], 'string', 'max' => 255],
			['company_id', 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
			['request_id', 'exist', 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
			['consultant_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
			['object_id', 'exist', 'targetClass' => Objects::class, 'targetAttribute' => ['object_id' => 'id']],
			['complex_id', 'exist', 'targetClass' => Complex::class, 'targetAttribute' => ['complex_id' => 'id']],
			['competitor_company_id', 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['competitor_company_id' => 'id']],
		];
	}

	public function fields(): array
	{
		$fields = parent::fields();

		$fields['dealDate_format'] = static function ($fields) {
			return $fields['dealDate'] ? Yii::$app->formatter->format($fields['dealDate'], 'date') : null;
		};

		$fields['dealDate'] = static function ($fields) {
			return $fields['dealDate'] ? date('Y-m-d', strtotime($fields['dealDate'])) : null;
		};

		$fields['clientLegalEntity_full_name'] = static function ($fields) {
			if ($fields['formOfOrganization'] !== null) {
				return Company::FORM_OF_ORGANIZATION_LIST[$fields['formOfOrganization']] . ' ' . $fields['clientLegalEntity'];
			}

			return $fields['clientLegalEntity'];
		};

		$fields['restOfTheTerm'] = static function ($fields) {
			if ($fields['contractTerm'] === null) {
				return null;
			}

			$currentTime = time();

			$contractTerm = $fields['contractTerm'];
			$startTime    = $fields['dealDate'];

			$endTime = strtotime("+ $contractTerm month", strtotime($startTime));

			$restOfTheTerm = $endTime - $currentTime;

			return round($restOfTheTerm / 60 / 60 / 24);
		};

		return $fields;
	}

	public function getCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	public function getCompetitor(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->hasOne(Company::class, ['id' => 'competitor_company_id']);
	}

	/**
	 * @deprecated Use getCompetitor() instead
	 */
	public function getCompetitorCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->getCompetitor();
	}

	public function getConsultant(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'consultant_id']);
	}

	public function getRequest(): RequestQuery
	{
		/** @var RequestQuery */
		return $this->hasOne(Request::class, ['id' => 'request_id']);
	}

	public function getOffer(): OfferMixQuery
	{
		/** @var OfferMixQuery */
		return $this->hasOne(OfferMix::class, ['object_id' => 'object_id', 'original_id' => 'original_id', 'type_id' => 'type_id']);
	}

	public function getBlock(): BlockQuery
	{
		/** @var BlockQuery */
		return $this->hasOne(Block::class, ['id' => 'original_id']);
	}

	public static function find(): DealQuery
	{
		return new DealQuery(static::class);
	}
}
