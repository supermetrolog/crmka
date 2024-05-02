<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

use app\kernel\web\http\resources\JsonResource;
use app\models\Objects;
use app\models\Request;

class ObjectShortResource extends JsonResource
{
	private Objects $resource;

	public function __construct(Objects $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                  => $this->resource->id,
			'title'               => $this->resource->title,
			'location_id'         => $this->resource->location_id,
			'is_land'             => $this->resource->is_land,
			'complex_id'          => $this->resource->complex_id,
			'contact_id'          => $this->resource->contact_id,
			'company_id'          => $this->resource->company_id,
			'author_id'           => $this->resource->author_id,
			'object_class'        => $this->resource->object_class,
			'region'              => $this->resource->region,
			'district'            => $this->resource->district,
			'direction'           => $this->resource->direction,
			'village'             => $this->resource->village,
			'highway'             => $this->resource->highway,
			'highway_secondary'   => $this->resource->highway_secondary,
			'from_mkad'           => $this->resource->from_mkad,
			'metro'               => $this->resource->metro,
			'address'             => $this->resource->address,
			'cadastral_number'    => $this->resource->cadastral_number,
			'yandex_address'      => $this->resource->yandex_address,
			'area_building'       => $this->resource->area_building,
			'area_floor_full'     => $this->resource->area_floor_full,
			'area_office_full'    => $this->resource->area_office_full,
			'area_tech_full'      => $this->resource->area_tech_full,
			'floors'              => $this->resource->floors,
			'longitude'           => $this->resource->longitude,
			'latitude'            => $this->resource->latitude,
			'agent_id'            => $this->resource->agent_id,
			'from_metro'          => $this->resource->from_metro,
			'from_metro_value'    => $this->resource->from_metro_value,
			'railway_station'     => $this->resource->railway_station,
			'from_station'        => $this->resource->from_station,
			'from_station_value'  => $this->resource->from_station_value,
			'from_busstop'        => $this->resource->from_busstop,
			'from_busstop_value'  => $this->resource->from_busstop_value,
			'area_mezzanine_full' => $this->resource->area_mezzanine_full,
			'photos'              => $this->resource->getPhotos(),
			'publ_time'           => $this->resource->publ_time,
			'test_only'           => $this->resource->test_only,
			'company'             => CompanyShortResource::tryMakeArray($this->resource->company)
		];
	}
}