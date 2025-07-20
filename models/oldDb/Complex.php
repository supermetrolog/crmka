<?php

namespace app\models\oldDb;

use app\kernel\common\models\AR\AR;
use app\models\oldDb\location\Location;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Connection;

/**
 * This is the model class for table "c_industry_complex".
 *
 * @property int            $id                     Идентификатор
 * @property string         $title
 * @property int            $title_novalue
 * @property int|null       $location_id            Айди местоположения
 * @property int            $last_update
 * @property int            $area_buildings_full
 * @property int            $buildings_admin_num
 * @property int            $buildings_industry_num
 * @property int            $buildings_help_num
 * @property int            $owners_num
 * @property int            $managment_company
 * @property int            $managment_company_id
 * @property int|null       $contact_id             Собственник
 * @property int|null       $company_id             Компания собственник
 * @property int|null       $author_id              Автор поста(кто создал внес)
 * @property int|null       $firefighting_type      Пожаротушение система
 * @property int|null       $from_mkad              От МКАД
 * @property int|null       $from_mkad_novalue
 * @property int            $metro                  Метро
 * @property string|null    $address                Адрес
 * @property string|null    $cadastral_number       Кадастровый номер
 * @property int|null       $area_field_full        Площадь участка
 * @property int|null       $area_building          Общая площадь
 * @property int|null       $area_floor_full        Общая площадь пола
 * @property int|null       $area_office_full       Офисные помещения
 * @property int|null       $area_tech_full
 * @property int|null       $land                   Участок (вспомогательное)
 * @property int|null       $land_length            Длина участка
 * @property int|null       $land_width             Ширина участка
 * @property string|null    $dsection               Габариты участка
 * @property int|null       $barrier                Шлагбаум
 * @property int|null       $fence_around_perimeter Забор по периметру
 * @property int|null       $finishing              Готовность к въезду
 * @property int|null       $l_category             Категория земли
 * @property int|null       $l_function             Разрешенное использование
 * @property int|null       $l_property             Вид права
 * @property string|null    $cranes_gantry          Козловые краны
 * @property string|null    $cranes_railway         Железнодорожные краны
 * @property int|null       $cranes_runways         Подкрановые пути
 * @property int            $railway                Ж/д ветка
 * @property int|null       $railway_value          Ж/д ветка протяженность
 * @property int            $phone_line             Есть ли Телефония
 * @property string         $telecommunications     Телекоммуникации
 * @property int            $guard                  Охрана
 * @property string         $guard_type
 * @property int|null       $entry_territory        Въезд на территорию (Тип)
 * @property int|null       $entry_territory_type
 * @property string|null    $parking_car_type       Парковка легковая какая
 * @property string|null    $parking_lorry_type     Парковка грузовичка какая
 * @property string|null    $parking_truck_type     Парковка грузовая какая
 * @property string|null    $comments               Комментарии
 * @property string|null    $description            Описание
 * @property string|null    $description_auto       Описание авто
 * @property string|null    $infrastructure         Инфраструктура
 * @property int            $gas                    Газ
 * @property int            $gas_type
 * @property int|null       $gas_value              Газ сколько кубов
 * @property int            $ttk_mkad
 * @property string         $parking                Парковка
 * @property int            $parking_car            Парковка легковая
 * @property int            $parking_car_value      Парковка легковая цена
 * @property int            $parking_lorry          Парковка грузовичка
 * @property int            $parking_lorry_value    Парковка грузовичка цена
 * @property int            $parking_truck          Парковка грузовая
 * @property int            $parking_truck_value    Парковка грузовая цена
 * @property int            $steam                  Пар
 * @property int            $is_prepay
 * @property int            $agent_visited          Брокер был на объекте
 * @property int            $agent_visited_sale
 * @property int            $agent_visited_safe
 * @property int            $agent_visited_subrent
 * @property int|null       $power                  Электричество доступно
 * @property float|null     $power_value
 * @property float|null     $power_all              Электричество всего
 * @property float|null     $power_available
 * @property int            $steam_value
 * @property int            $water                  Водоснабжение
 * @property int            $water_value            Водоснабжение объем
 * @property int            $sewage_old             Канализация
 * @property int|null       $sewage                 Канализация центральная
 * @property int|null       $sewage_value           Канализация центральная объем
 * @property int|null       $sewage_rain            Канализация ливневая
 * @property int            $heating                Отопление
 * @property int            $heating_autonomous
 * @property int            $heating_autonomous_value
 * @property string         $heating_autonomous_type
 * @property int            $ventilation            Вентиляция/кондиционирование
 * @property string         $internet_type          Интернет
 * @property int            $internet
 * @property string|null    $safety_systems         Системы безопасности
 * @property float          $longitude              Долгота
 * @property float          $latitude               Широта
 * @property int            $agent_id               Агент
 * @property int            $agent_sale             Агент по продаже
 * @property int            $agent_safe             Агент по ответ хр
 * @property int            $agent_subrent          Агент по субаренде
 * @property int            $onsite                 На сайте
 * @property int            $contract               Договор подписан
 * @property int            $onsite_top             Спецпредложение
 * @property int            $electricity_included   Электричество и вода отдельно
 * @property int            $deleted                Удален
 * @property string|null    $slcomments             Служебный комментарий
 * @property int|null       $openstage              Открытые площадки
 * @property string|null    $contract_date          Действие договора до
 * @property int|null       $from_metro             От метро сколько
 * @property int            $from_metro_value       От метро как
 * @property int            $railway_station        Ближайшая железнодорожная станция
 * @property int            $from_station           От станции  на чем
 * @property int            $from_station_value     От станции
 * @property int            $from_busstop
 * @property int            $from_busstop_value
 * @property int            $entrance_type
 * @property int            $plain_type             Вид права
 * @property string         $area_mezzanine_full
 * @property int            $safe_price_rack
 * @property int            $safe_price_rack_oversized
 * @property int            $safe_price_cell
 * @property int            $safe_price_floor_oversized
 * @property string         $photo
 * @property string         $videos
 * @property int            $publ_time
 * @property int            $activity
 * @property int            $order_row
 * @property int|null       $video_control
 * @property int|null       $access_control
 * @property int|null       $security_alert
 * @property int|null       $fire_alert
 * @property int|null       $smoke_exhaust
 * @property int|null       $canteen
 * @property int|null       $hostel
 * @property int|null       $street_area
 * @property int|null       $own_type
 * @property string|null    $building_layouts
 * @property string|null    $building_presentations
 * @property string|null    $building_contracts
 * @property string|null    $building_property_documents
 * @property string|null    $photos_360
 * @property int|null       $fence
 * @property string|null    $field_allow_usage
 * @property int|null       $land_category
 * @property int|null       $status_reason
 * @property string|null    $status_description
 * @property int|null       $own_type_land
 * @property int|null       $area_outside
 * @property int|null       $description_complex
 * @property int|null       $description_manual_use
 * @property int|null       $gas_near
 * @property int|null       $mkad_ttk_between
 * @property int|null       $empty_line
 * @property int|null       $title_empty_main
 * @property int|null       $title_empty_communications
 * @property int|null       $title_empty_security
 * @property int|null       $title_empty_railway
 * @property int|null       $title_empty_infrastructure
 * @property int|null       $landscape_type
 * @property int|null       $land_use_restrictions
 * @property string|null    $cadastral_number_land
 * @property int|null       $documents_old
 * @property string|null    $water_type
 * @property string|null    $mixer_parts
 * @property int|null       $heating_central
 *
 * @property-read ?Location $location
 */
class Complex extends AR
{
	public static function tableName(): string
	{
		return 'c_industry_complex';
	}

	/**
	 * @throws InvalidConfigException
	 */
	public static function getDb(): Connection
	{
		return Yii::$app->get('db_old');
	}

	public function rules(): array
	{
		return [
			[['title', 'title_novalue', 'last_update', 'area_buildings_full', 'buildings_admin_num', 'buildings_industry_num', 'buildings_help_num', 'owners_num', 'managment_company', 'managment_company_id', 'telecommunications', 'guard_type', 'gas_type', 'parking', 'parking_car_value', 'parking_lorry', 'parking_lorry_value', 'parking_truck_value', 'steam_value', 'water_value', 'heating_autonomous', 'heating_autonomous_value', 'heating_autonomous_type', 'internet', 'from_station', 'from_station_value', 'from_busstop', 'from_busstop_value', 'entrance_type', 'photo', 'videos', 'publ_time', 'order_row'], 'required'],
			[['title_novalue', 'location_id', 'last_update', 'area_buildings_full', 'buildings_admin_num', 'buildings_industry_num', 'buildings_help_num', 'owners_num', 'managment_company', 'managment_company_id', 'contact_id', 'company_id', 'author_id', 'firefighting_type', 'from_mkad', 'from_mkad_novalue', 'metro', 'area_field_full', 'area_building', 'area_floor_full', 'area_office_full', 'area_tech_full', 'land', 'land_length', 'land_width', 'barrier', 'fence_around_perimeter', 'finishing', 'l_category', 'l_function', 'l_property', 'cranes_runways', 'railway', 'railway_value', 'phone_line', 'guard', 'entry_territory', 'entry_territory_type', 'gas', 'gas_type', 'gas_value', 'ttk_mkad', 'parking_car', 'parking_car_value', 'parking_lorry', 'parking_lorry_value', 'parking_truck', 'parking_truck_value', 'steam', 'is_prepay', 'agent_visited', 'agent_visited_sale', 'agent_visited_safe', 'agent_visited_subrent', 'power', 'steam_value', 'water', 'water_value', 'sewage_old', 'sewage', 'sewage_value', 'sewage_rain', 'heating', 'heating_autonomous', 'heating_autonomous_value', 'ventilation', 'internet', 'agent_id', 'agent_sale', 'agent_safe', 'agent_subrent', 'onsite', 'contract', 'onsite_top', 'electricity_included', 'deleted', 'openstage', 'from_metro', 'from_metro_value', 'railway_station', 'from_station', 'from_station_value', 'from_busstop', 'from_busstop_value', 'entrance_type', 'plain_type', 'safe_price_rack', 'safe_price_rack_oversized', 'safe_price_cell', 'safe_price_floor_oversized', 'publ_time', 'activity', 'order_row', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'smoke_exhaust', 'canteen', 'hostel', 'street_area', 'own_type', 'fence', 'land_category', 'status_reason', 'own_type_land', 'area_outside', 'description_complex', 'description_manual_use', 'gas_near', 'mkad_ttk_between', 'empty_line', 'title_empty_main', 'title_empty_communications', 'title_empty_security', 'title_empty_railway', 'title_empty_infrastructure', 'landscape_type', 'land_use_restrictions', 'documents_old', 'heating_central'], 'integer'],
			[['comments', 'description', 'description_auto', 'slcomments', 'photo', 'videos', 'building_layouts', 'building_presentations', 'building_contracts', 'building_property_documents', 'photos_360', 'field_allow_usage', 'cadastral_number_land'], 'string'],
			[['power_value', 'power_all', 'power_available', 'longitude', 'latitude'], 'number'],
			[['contract_date'], 'safe'],
			[['title', 'cranes_gantry', 'cranes_railway', 'status_description'], 'string', 'max' => 300],
			[['address', 'cadastral_number', 'dsection'], 'string', 'max' => 200],
			[['telecommunications', 'guard_type', 'parking_car_type', 'parking_lorry_type', 'parking_truck_type', 'infrastructure', 'parking', 'heating_autonomous_type', 'safety_systems'], 'string', 'max' => 100],
			[['internet_type'], 'string', 'max' => 50],
			[['area_mezzanine_full'], 'string', 'max' => 255],
			[['water_type'], 'string', 'max' => 30],
			[['mixer_parts'], 'string', 'max' => 500],
		];
	}

	public function getLocation(): ActiveQuery
	{
		return $this->hasOne(Location::class, ['id' => 'location_id']);
	}
}
