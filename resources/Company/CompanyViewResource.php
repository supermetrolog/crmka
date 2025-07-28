<?php

declare(strict_types=1);

namespace app\resources\Company;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Company;
use app\resources\Call\CallResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Company\ActivityGroup\CompanyActivityGroupResource;
use app\resources\Company\ActivityProfile\CompanyActivityProfileResource;
use app\resources\Company\Category\CompanyCategoryResource;
use app\resources\Company\Contact\CompanyGeneralContactResource;
use app\resources\Company\File\CompanyFileResource;
use app\resources\Company\Group\CompanyGroupResource;
use app\resources\Company\ProductRange\CompanyProductRangeResource;
use app\resources\Contact\ContactResource;
use app\resources\Media\MediaShortResource;
use yii\base\ErrorException;

class CompanyViewResource extends JsonResource
{
	private Company $resource;

	public function __construct(Company $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @throws ErrorException
	 */
	public function toArray(): array
	{
		return ArrayHelper::merge(
			CompanyBaseResource::make($this->resource)->toArray(),
			[
				'contacts'          => ContactResource::collection($this->resource->contacts),
				'generalContact'    => CompanyGeneralContactResource::tryMakeArray($this->resource->generalContact),
				'categories'        => CompanyCategoryResource::collection($this->resource->categories),
				'productRanges'     => CompanyProductRangeResource::collection($this->resource->productRanges),
				'companyGroup'      => CompanyGroupResource::tryMakeArray($this->resource->companyGroup),
				'files'             => CompanyFileResource::collection($this->resource->files),
				'logo'              => MediaShortResource::tryMakeArray($this->resource->logo),
				'consultant'        => UserShortResource::tryMakeArray($this->resource->consultant),
				'last_call'         => CallResource::tryMakeArray($this->resource->lastCall),
				'activity_groups'   => CompanyActivityGroupResource::collection($this->resource->companyActivityGroups),
				'activity_profiles' => CompanyActivityProfileResource::collection($this->resource->companyActivityProfiles),

				'comments_count' => $this->resource->getComments()->count(),

				'dealsRequestEmpty'     => $this->resource->dealsRequestEmpty,
				'objects_count'         => $this->resource->objects_count,
				'requests_count'        => $this->resource->requests_count,
				'active_requests_count' => $this->resource->active_requests_count,
				'contacts_count'        => $this->resource->contacts_count,
				'active_contacts_count' => $this->resource->active_contacts_count,
			]
		);
	}
}