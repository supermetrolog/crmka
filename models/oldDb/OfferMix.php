<?php

namespace app\models\oldDb;

use app\models\ActiveQuery\oldDb\OfferMixQuery;
use app\models\Company;
use app\models\Contact;
use app\models\miniModels\TimelineStepObjectComment;
use app\models\oldDb\User as OldDbUser;
use app\models\Request;
use app\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "c_industry_offers_mix".
 *
 * @property int $id id миксованного предложения
 * @property int|null $original_id реальный айди элемента в своей таблице
 * @property string|null $visual_id визуальный id для сайта
 * @property int|null $type_id тип миксованного предложения 1 - блок 2- предложение
 * @property int|null $deal_type Тип сделки
 * @property string|null $deal_type_name Имя типа сделки
 * @property string|null $title Название объекта
 * @property int|null $status статус актив пассив
 * @property int|null $object_id ID объекта
 * @property int|null $complex_id ID комплекса
 * @property int|null $parent_id
 * @property int|null $company_id ID  компании
 * @property int|null $contact_id ID  крнтакта
 * @property string|null $object_type Тип лота
 * @property string|null $purposes Назанчения
 * @property string|null $purposes_furl Назанчения ЧПУ
 * @property string|null $object_type_name Тип лота название
 * @property int|null $year_built Год постройки
 * @property int|null $agent_id id брокера
 * @property int|null $agent_visited был ли брокер на объекте
 * @property string|null $agent_name Имя брокера
 * @property int|null $is_land это земля
 * @property int|null $land_width ширина участка
 * @property int|null $land_length длина участка
 * @property string|null $landscape_type рельеф
 * @property int|null $land_use_restrictions ограничения по земле
 * @property string|null $address адрес
 * @property float|null $latitude широта
 * @property string|null $class класс
 * @property string|null $class_name имя класса
 * @property float|null $longitude долгота
 * @property int|null $from_mkad расстояние от мкад
 * @property string|null $region регион
 * @property string|null $region_name название региона
 * @property int|null $cian_region регион в циане
 * @property int|null $outside_mkad вне мккад
 * @property int|null $near_mo рядом с МО
 * @property string|null $town город
 * @property string|null $town_name название города
 * @property string|null $district Округ
 * @property string|null $district_name Название округа
 * @property string|null $district_moscow район Москвы
 * @property string|null $district_moscow_name название района
 * @property string|null $direction Направление
 * @property string|null $direction_name Название направления
 * @property string|null $highway шоссе
 * @property string|null $highway_name название шоссе
 * @property string|null $highway_moscow шоссе омсквы
 * @property string|null $highway_moscow_name название шоссе москвы
 * @property string|null $metro метро
 * @property string|null $metro_name название метро
 * @property int|null $from_metro_value от метро значение
 * @property int|null $from_metro от метро как
 * @property string|null $railway_station Ж/д станция
 * @property int|null $from_station_value от станции время
 * @property int|null $from_station от станции на чем
 * @property string|null $blocks Список блоков
 * @property int|null $blocks_amount Количество блоков
 * @property string|null $photos фото
 * @property string|null $videos видео
 * @property string|null $thumbs тамбы
 * @property int|null $last_update последнее обновление
 * @property int|null $commission_client коммиссия для клиента
 * @property int|null $commission_owner коммиссия от для собственника
 * @property int|null $deposit депозит
 * @property int|null $pledge залог
 * @property int|null $area_building площадь здания
 * @property int|null $area_floor_full полная площадь пола
 * @property int|null $area_mezzanine_full полная площадь мезанина
 * @property int|null $area_office_full полная площадб офисов
 * @property int|null $area_min
 * @property int|null $area_max
 * @property int|null $area_floor_min минимальная площадь полап
 * @property int|null $area_floor_max максимальная площадь пола
 * @property int|null $area_mezzanine_min мин площадь мезанина
 * @property int|null $area_mezzanine_max макс площадь мезанина
 * @property int|null $area_mezzanine_add
 * @property int|null $area_office_min мин площадь офисов
 * @property int|null $area_office_max макс площадь офисов
 * @property int|null $area_office_add
 * @property int|null $area_tech_min
 * @property int|null $area_tech_max
 * @property int|null $area_field_min мин площадь участка
 * @property int|null $area_field_max макс площадь участка
 * @property int|null $pallet_place_min мин кол-во палет мест
 * @property int|null $pallet_place_max макс колво паллет мест
 * @property int|null $cells_place_min
 * @property int|null $cells_place_max
 * @property string|null $tax_form налоги
 * @property int|null $inc_electricity
 * @property int|null $inc_heating
 * @property int|null $inc_water
 * @property int|null $price_opex_inc
 * @property int|null $price_opex
 * @property int|null $price_opex_min
 * @property int|null $price_opex_max
 * @property int|null $price_public_services_inc
 * @property int|null $price_public_services
 * @property int|null $public_services
 * @property int|null $price_public_services_min
 * @property int|null $price_public_services_max
 * @property int|null $price_floor_min
 * @property int|null $price_floor_max
 * @property int|null $price_floor_min_month
 * @property int|null $price_floor_max_month
 * @property int|null $price_min_month_all
 * @property int|null $price_max_month_all
 * @property int|null $price_floor_100_min
 * @property int|null $price_floor_100_max
 * @property int|null $price_mezzanine_min
 * @property int|null $price_mezzanine_max
 * @property int|null $price_office_min
 * @property int|null $price_office_max
 * @property int|null $price_sale_min
 * @property int|null $price_sale_max
 * @property int|null $price_safe_pallet_min
 * @property int|null $price_safe_pallet_max
 * @property int|null $price_safe_volume_min
 * @property int|null $price_safe_volume_max
 * @property int|null $price_safe_floor_min
 * @property int|null $price_safe_floor_max
 * @property int|null $price_safe_calc_min
 * @property int|null $price_safe_calc_max
 * @property int|null $price_safe_calc_month_min
 * @property int|null $price_safe_calc_month_max
 * @property int|null $price_sale_min_all
 * @property int|null $price_sale_max_all
 * @property float|null $ceiling_height_min
 * @property float|null $ceiling_height_max
 * @property int|null $temperature_min
 * @property int|null $temperature_max
 * @property float|null $load_floor_min
 * @property float|null $load_floor_max
 * @property float|null $load_mezzanine_min
 * @property float|null $load_mezzanine_max
 * @property string|null $safe_type
 * @property string|null $safe_type_furl
 * @property int|null $prepay
 * @property int|null $floor_min
 * @property int|null $floor_max
 * @property string|null $floor_type тип пола
 * @property string|null $floor_types типы пола
 * @property int|null $self_leveling антипыль
 * @property int|null $heated Отопление
 * @property string|null $gates ворота
 * @property string|null $gate_type Ворота тип
 * @property string|null $gate_num Ворота количество
 * @property string|null $column_grid Сетка колонн
 * @property int|null $elevators_min
 * @property int|null $elevators_max
 * @property int|null $elevators_num
 * @property int|null $has_cranes
 * @property float|null $cranes_min
 * @property float|null $cranes_max
 * @property int|null $cranes_num
 * @property float|null $cranes_railway_min
 * @property float|null $cranes_railway_max
 * @property int|null $cranes_railway_num
 * @property float|null $cranes_gantry_min
 * @property float|null $cranes_gantry_max
 * @property int|null $cranes_gantry_num
 * @property float|null $cranes_overhead_min
 * @property float|null $cranes_overhead_max
 * @property int|null $cranes_overhead_num
 * @property float|null $cranes_cathead_min
 * @property float|null $cranes_cathead_max
 * @property int|null $cranes_cathead_num
 * @property int|null $telphers_min
 * @property int|null $telphers_max
 * @property int|null $telphers_num
 * @property int|null $railway наличие жд
 * @property int|null $railway_value длина жд ветки
 * @property int|null $power мощности
 * @property int|null $power_value занчение мощности
 * @property int|null $steam пар
 * @property int|null $steam_value объем пара
 * @property int|null $gas газ
 * @property int|null $gas_value объем газа
 * @property int|null $phone телефон
 * @property string|null $internet интеренет
 * @property string|null $heating отопление
 * @property string|null $facing отделка
 * @property string|null $ventilation вентиляция
 * @property string|null $water Водоснабжение
 * @property int|null $water_value Объем водоснабжения
 * @property int|null $sewage_central центральная канализация
 * @property int|null $sewage_central_value объем для центральной канализации
 * @property int|null $sewage_rain ливневая канализация
 * @property string|null $guard охрана
 * @property int|null $firefighting пожаротушение
 * @property string|null $firefighting_name
 * @property int|null $video_control видео контроль
 * @property int|null $access_control контроль доступа
 * @property int|null $security_alert сигнализация
 * @property int|null $fire_alert пожарная сигнализация
 * @property int|null $smoke_exhaust думоудаление
 * @property int|null $canteen столовая
 * @property int|null $hostel общежитие
 * @property int|null $racks стеллажи
 * @property int|null $warehouse_equipment складская техника
 * @property int|null $charging_room зарядная комната
 * @property int|null $cross_docking кросс докинг
 * @property int|null $cranes_runways крановые пути
 * @property string|null $cadastral_number кадастровый номер
 * @property string|null $cadastral_number_land кадастровй номер земли
 * @property string|null $field_allow_usage вид разрешенного использования
 * @property string|null $available_from
 * @property string|null $own_type право на собственность
 * @property string|null $own_type_land право на собственность земли
 * @property string|null $land_category категория земли
 * @property string|null $entry_territory вьезд на территорию
 * @property int|null $parking_car парковка легковых
 * @property string|null $parking_car_value цена парковки легковых
 * @property int|null $parking_lorry парковка малотоннажек
 * @property string|null $parking_lorry_value  цена парковка малотоннажек
 * @property int|null $parking_truck парковка грузовиков
 * @property string|null $parking_truck_value цена парковки грузовика
 * @property int|null $built_to_suit built to suit
 * @property int|null $built_to_suit_time
 * @property int|null $built_to_suit_plan
 * @property int|null $rent_business с арендным бизнесом
 * @property int|null $rent_business_fill
 * @property int|null $rent_business_price
 * @property int|null $rent_business_long_contracts
 * @property int|null $rent_business_last_repair
 * @property int|null $rent_business_payback
 * @property int|null $rent_business_income
 * @property int|null $rent_business_profit
 * @property int|null $sale_company продажа юр лица
 * @property int|null $holidays каникулы
 * @property int|null $ad_realtor реклама на сайт
 * @property int|null $ad_cian реклама циан
 * @property int|null $ad_cian_top3 циан топ 3
 * @property int|null $ad_cian_premium циан премиум
 * @property int|null $ad_cian_hl циан выделить
 * @property int|null $ad_yandex реклама яндекс
 * @property int|null $ad_yandex_raise яндекс поднять
 * @property int|null $ad_yandex_promotion яндекс продвинуть
 * @property int|null $ad_yandex_premium яндекс премиум
 * @property int|null $ad_arendator реклама на арендатор
 * @property int|null $ad_free реклама на бесплатныее
 * @property int|null $ad_special реклама на спец ресурсы
 * @property string|null $description описание
 * @property int|null $deleted удаленный
 * @property int|null $test_only тестовый лот
 * @property int|null $is_exclusive эксклюзив
 * @property int|null $deal_id номер сделки
 * @property int|null $hide_from_market скрыто от рынка
 *
 *
 * @property User $consultant
 * @property ObjectsBlock $block
 * @property Objects $object
 * @property $this[] $miniOffersMix
 * @property Offers $offer
 */
class OfferMix extends ActiveRecord
{

    public const STATUS_ACTIVE = 1;
    public const STATUS_PASSIVE = 2;

    public const DEAL_TYPE_RENT = 1;
    public const DEAL_TYPE_SALE = 2;
    public const DEAL_TYPE_RESPONSE_STORAGE = 3;
    public const DEAL_TYPE_SUBLEASE = 4;

    public const DEAL_TYPES_STRING = [
        1 => "rent",
        2 => "sale",
        3 => "responce_storage",
        4 => "sublease"
    ];

    public const MINI_TYPE_ID = 1;
    public const GENERAL_TYPE_ID = 2;
    public const OBJECT_TYPE_ID = 3;


    public const USERS = [
        '0' => 41,
        '1' => 32,
        '2' => 33,
        '4' => 34,
        '5' => 35,
        '6' => 36,
        '7' => 37,
        '8' => 38,
        '9' => 39,
        '10' => 40,
        '11' => 41,
        '12' => 42,
        '45' => 43,
        '70' => 44,
        '109' => 45,
        '113' => 46,
        '140' => 47,
        '141' => 48,
        '150' => 49,
        '151' => 50,
        '154' => 51,
        '155' => 52,
        '179' => 53,
        '218' => 54,
        '278' => 55,
        '285' => 56,
        '287' => 57,
        '290' => 58,
        '297' => 59,
        '314' => 60,
        '315' => 61,
        '317' => 62,
        '319' => 63,
        '329' => 64,
        '330' => 65,
        '331' => 66,
        '332' => 67,
        '333' => 3,
        '334' => 68,
        '335' => 69
    ];



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'c_industry_offers_mix';
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
            [['original_id', 'type_id', 'deal_type', 'status', 'object_id', 'complex_id', 'parent_id', 'company_id', 'contact_id', 'year_built', 'agent_id', 'agent_visited', 'is_land', 'land_width', 'land_length', 'land_use_restrictions', 'from_mkad', 'cian_region', 'outside_mkad', 'near_mo', 'from_metro_value', 'from_metro', 'from_station_value', 'from_station', 'blocks_amount', 'last_update', 'commission_client', 'commission_owner', 'deposit', 'pledge', 'area_building', 'area_floor_full', 'area_mezzanine_full', 'area_office_full', 'area_min', 'area_max', 'area_floor_min', 'area_floor_max', 'area_mezzanine_min', 'area_mezzanine_max', 'area_mezzanine_add', 'area_office_min', 'area_office_max', 'area_office_add', 'area_tech_min', 'area_tech_max', 'area_field_min', 'area_field_max', 'pallet_place_min', 'pallet_place_max', 'cells_place_min', 'cells_place_max', 'inc_electricity', 'inc_heating', 'inc_water', 'price_opex_inc', 'price_opex', 'price_opex_min', 'price_opex_max', 'price_public_services_inc', 'price_public_services', 'public_services', 'price_public_services_min', 'price_public_services_max', 'price_floor_min', 'price_floor_max', 'price_floor_min_month', 'price_floor_max_month', 'price_min_month_all', 'price_max_month_all', 'price_floor_100_min', 'price_floor_100_max', 'price_mezzanine_min', 'price_mezzanine_max', 'price_office_min', 'price_office_max', 'price_sale_min', 'price_sale_max', 'price_safe_pallet_min', 'price_safe_pallet_max', 'price_safe_volume_min', 'price_safe_volume_max', 'price_safe_floor_min', 'price_safe_floor_max', 'price_safe_calc_min', 'price_safe_calc_max', 'price_safe_calc_month_min', 'price_safe_calc_month_max', 'price_sale_min_all', 'price_sale_max_all', 'temperature_min', 'temperature_max', 'prepay', 'floor_min', 'floor_max', 'self_leveling', 'heated', 'elevators_min', 'elevators_max', 'elevators_num', 'has_cranes', 'cranes_num', 'cranes_railway_num', 'cranes_gantry_num', 'cranes_overhead_num', 'cranes_cathead_num', 'telphers_min', 'telphers_max', 'telphers_num', 'railway', 'railway_value', 'power', 'power_value', 'steam', 'steam_value', 'gas', 'gas_value', 'phone', 'water_value', 'sewage_central', 'sewage_central_value', 'sewage_rain', 'firefighting', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'smoke_exhaust', 'canteen', 'hostel', 'racks', 'warehouse_equipment', 'charging_room', 'cross_docking', 'cranes_runways', 'parking_car', 'parking_lorry', 'parking_truck', 'built_to_suit', 'built_to_suit_time', 'built_to_suit_plan', 'rent_business', 'rent_business_fill', 'rent_business_price', 'rent_business_long_contracts', 'rent_business_last_repair', 'rent_business_payback', 'rent_business_income', 'rent_business_profit', 'sale_company', 'holidays', 'ad_realtor', 'ad_cian', 'ad_cian_top3', 'ad_cian_premium', 'ad_cian_hl', 'ad_yandex', 'ad_yandex_raise', 'ad_yandex_promotion', 'ad_yandex_premium', 'ad_arendator', 'ad_free', 'ad_special', 'deleted', 'test_only', 'is_exclusive', 'deal_id', 'hide_from_market'], 'integer'],
            [['address', 'blocks', 'photos', 'thumbs', 'description'], 'string'],
            [['latitude', 'longitude', 'ceiling_height_min', 'ceiling_height_max', 'load_floor_min', 'load_floor_max', 'load_mezzanine_min', 'load_mezzanine_max', 'cranes_min', 'cranes_max', 'cranes_railway_min', 'cranes_railway_max', 'cranes_gantry_min', 'cranes_gantry_max', 'cranes_overhead_min', 'cranes_overhead_max', 'cranes_cathead_min', 'cranes_cathead_max'], 'number'],
            [['visual_id', 'column_grid'], 'string', 'max' => 20],
            [['deal_type_name', 'object_type', 'object_type_name', 'available_from'], 'string', 'max' => 50],
            [['title', 'railway_station', 'gates', 'gate_type', 'gate_num'], 'string', 'max' => 300],
            [['purposes', 'purposes_furl'], 'string', 'max' => 500],
            [['agent_name', 'landscape_type', 'safe_type', 'floor_type', 'floor_types', 'internet', 'heating', 'facing', 'ventilation', 'water', 'guard', 'own_type', 'own_type_land', 'land_category', 'entry_territory', 'parking_car_value', 'parking_lorry_value', 'parking_truck_value'], 'string', 'max' => 100],
            [['class', 'class_name'], 'string', 'max' => 5],
            [['region', 'region_name', 'town', 'town_name', 'district', 'district_name', 'district_moscow', 'district_moscow_name', 'direction', 'direction_name', 'highway', 'highway_name', 'highway_moscow', 'highway_moscow_name', 'metro', 'metro_name', 'safe_type_furl', 'cadastral_number', 'cadastral_number_land'], 'string', 'max' => 200],
            [['videos', 'field_allow_usage'], 'string', 'max' => 1000],
            [['tax_form'], 'string', 'max' => 10],
            [['firefighting_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'original_id' => 'Original ID',
            'visual_id' => 'Visual ID',
            'type_id' => 'Type ID',
            'deal_type' => 'Deal Type',
            'deal_type_name' => 'Deal Type Name',
            'title' => 'Title',
            'status' => 'Status',
            'object_id' => 'Object ID',
            'complex_id' => 'Complex ID',
            'parent_id' => 'Parent ID',
            'company_id' => 'Company ID',
            'contact_id' => 'Contact ID',
            'object_type' => 'Object Type',
            'purposes' => 'Purposes',
            'purposes_furl' => 'Purposes Furl',
            'object_type_name' => 'Object Type Name',
            'year_built' => 'Year Built',
            'agent_id' => 'Agent ID',
            'agent_visited' => 'Agent Visited',
            'agent_name' => 'Agent Name',
            'is_land' => 'Is Land',
            'land_width' => 'Land Width',
            'land_length' => 'Land Length',
            'landscape_type' => 'Landscape Type',
            'land_use_restrictions' => 'Land Use Restrictions',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'class' => 'Class',
            'class_name' => 'Class Name',
            'longitude' => 'Longitude',
            'from_mkad' => 'From Mkad',
            'region' => 'Region',
            'region_name' => 'Region Name',
            'cian_region' => 'Cian Region',
            'outside_mkad' => 'Outside Mkad',
            'near_mo' => 'Near Mo',
            'town' => 'Town',
            'town_name' => 'Town Name',
            'district' => 'District',
            'district_name' => 'District Name',
            'district_moscow' => 'District Moscow',
            'district_moscow_name' => 'District Moscow Name',
            'direction' => 'Direction',
            'direction_name' => 'Direction Name',
            'highway' => 'Highway',
            'highway_name' => 'Highway Name',
            'highway_moscow' => 'Highway Moscow',
            'highway_moscow_name' => 'Highway Moscow Name',
            'metro' => 'Metro',
            'metro_name' => 'Metro Name',
            'from_metro_value' => 'From Metro Value',
            'from_metro' => 'From Metro',
            'railway_station' => 'Railway Station',
            'from_station_value' => 'From Station Value',
            'from_station' => 'From Station',
            'blocks' => 'Blocks',
            'blocks_amount' => 'Blocks Amount',
            'photos' => 'Photos',
            'videos' => 'Videos',
            'thumbs' => 'Thumbs',
            'last_update' => 'Last Update',
            'commission_client' => 'Commission Client',
            'commission_owner' => 'Commission Owner',
            'deposit' => 'Deposit',
            'pledge' => 'Pledge',
            'area_building' => 'Area Building',
            'area_floor_full' => 'Area Floor Full',
            'area_mezzanine_full' => 'Area Mezzanine Full',
            'area_office_full' => 'Area Office Full',
            'area_min' => 'Area Min',
            'area_max' => 'Area Max',
            'area_floor_min' => 'Area Floor Min',
            'area_floor_max' => 'Area Floor Max',
            'area_mezzanine_min' => 'Area Mezzanine Min',
            'area_mezzanine_max' => 'Area Mezzanine Max',
            'area_mezzanine_add' => 'Area Mezzanine Add',
            'area_office_min' => 'Area Office Min',
            'area_office_max' => 'Area Office Max',
            'area_office_add' => 'Area Office Add',
            'area_tech_min' => 'Area Tech Min',
            'area_tech_max' => 'Area Tech Max',
            'area_field_min' => 'Area Field Min',
            'area_field_max' => 'Area Field Max',
            'pallet_place_min' => 'Pallet Place Min',
            'pallet_place_max' => 'Pallet Place Max',
            'cells_place_min' => 'Cells Place Min',
            'cells_place_max' => 'Cells Place Max',
            'tax_form' => 'Tax Form',
            'inc_electricity' => 'Inc Electricity',
            'inc_heating' => 'Inc Heating',
            'inc_water' => 'Inc Water',
            'price_opex_inc' => 'Price Opex Inc',
            'price_opex' => 'Price Opex',
            'price_opex_min' => 'Price Opex Min',
            'price_opex_max' => 'Price Opex Max',
            'price_public_services_inc' => 'Price Public Services Inc',
            'price_public_services' => 'Price Public Services',
            'public_services' => 'Public Services',
            'price_public_services_min' => 'Price Public Services Min',
            'price_public_services_max' => 'Price Public Services Max',
            'price_floor_min' => 'Price Floor Min',
            'price_floor_max' => 'Price Floor Max',
            'price_floor_min_month' => 'Price Floor Min Month',
            'price_floor_max_month' => 'Price Floor Max Month',
            'price_min_month_all' => 'Price Min Month All',
            'price_max_month_all' => 'Price Max Month All',
            'price_floor_100_min' => 'Price Floor  100 Min',
            'price_floor_100_max' => 'Price Floor  100 Max',
            'price_mezzanine_min' => 'Price Mezzanine Min',
            'price_mezzanine_max' => 'Price Mezzanine Max',
            'price_office_min' => 'Price Office Min',
            'price_office_max' => 'Price Office Max',
            'price_sale_min' => 'Price Sale Min',
            'price_sale_max' => 'Price Sale Max',
            'price_safe_pallet_min' => 'Price Safe Pallet Min',
            'price_safe_pallet_max' => 'Price Safe Pallet Max',
            'price_safe_volume_min' => 'Price Safe Volume Min',
            'price_safe_volume_max' => 'Price Safe Volume Max',
            'price_safe_floor_min' => 'Price Safe Floor Min',
            'price_safe_floor_max' => 'Price Safe Floor Max',
            'price_safe_calc_min' => 'Price Safe Calc Min',
            'price_safe_calc_max' => 'Price Safe Calc Max',
            'price_safe_calc_month_min' => 'Price Safe Calc Month Min',
            'price_safe_calc_month_max' => 'Price Safe Calc Month Max',
            'price_sale_min_all' => 'Price Sale Min All',
            'price_sale_max_all' => 'Price Sale Max All',
            'ceiling_height_min' => 'Ceiling Height Min',
            'ceiling_height_max' => 'Ceiling Height Max',
            'temperature_min' => 'Temperature Min',
            'temperature_max' => 'Temperature Max',
            'load_floor_min' => 'Load Floor Min',
            'load_floor_max' => 'Load Floor Max',
            'load_mezzanine_min' => 'Load Mezzanine Min',
            'load_mezzanine_max' => 'Load Mezzanine Max',
            'safe_type' => 'Safe Type',
            'safe_type_furl' => 'Safe Type Furl',
            'prepay' => 'Prepay',
            'floor_min' => 'Floor Min',
            'floor_max' => 'Floor Max',
            'floor_type' => 'Floor Type',
            'floor_types' => 'Floor Types',
            'self_leveling' => 'Self Leveling',
            'heated' => 'Heated',
            'gates' => 'Gates',
            'gate_type' => 'Gate Type',
            'gate_num' => 'Gate Num',
            'column_grid' => 'Column Grid',
            'elevators_min' => 'Elevators Min',
            'elevators_max' => 'Elevators Max',
            'elevators_num' => 'Elevators Num',
            'has_cranes' => 'Has Cranes',
            'cranes_min' => 'Cranes Min',
            'cranes_max' => 'Cranes Max',
            'cranes_num' => 'Cranes Num',
            'cranes_railway_min' => 'Cranes Railway Min',
            'cranes_railway_max' => 'Cranes Railway Max',
            'cranes_railway_num' => 'Cranes Railway Num',
            'cranes_gantry_min' => 'Cranes Gantry Min',
            'cranes_gantry_max' => 'Cranes Gantry Max',
            'cranes_gantry_num' => 'Cranes Gantry Num',
            'cranes_overhead_min' => 'Cranes Overhead Min',
            'cranes_overhead_max' => 'Cranes Overhead Max',
            'cranes_overhead_num' => 'Cranes Overhead Num',
            'cranes_cathead_min' => 'Cranes Cathead Min',
            'cranes_cathead_max' => 'Cranes Cathead Max',
            'cranes_cathead_num' => 'Cranes Cathead Num',
            'telphers_min' => 'Telphers Min',
            'telphers_max' => 'Telphers Max',
            'telphers_num' => 'Telphers Num',
            'railway' => 'Railway',
            'railway_value' => 'Railway Value',
            'power' => 'Power',
            'power_value' => 'Power Value',
            'steam' => 'Steam',
            'steam_value' => 'Steam Value',
            'gas' => 'Gas',
            'gas_value' => 'Gas Value',
            'phone' => 'Phone',
            'internet' => 'Internet',
            'heating' => 'Heating',
            'facing' => 'Facing',
            'ventilation' => 'Ventilation',
            'water' => 'Water',
            'water_value' => 'Water Value',
            'sewage_central' => 'Sewage Central',
            'sewage_central_value' => 'Sewage Central Value',
            'sewage_rain' => 'Sewage Rain',
            'guard' => 'Guard',
            'firefighting' => 'Firefighting',
            'firefighting_name' => 'Firefighting Name',
            'video_control' => 'Video Control',
            'access_control' => 'Access Control',
            'security_alert' => 'Security Alert',
            'fire_alert' => 'Fire Alert',
            'smoke_exhaust' => 'Smoke Exhaust',
            'canteen' => 'Canteen',
            'hostel' => 'Hostel',
            'racks' => 'Racks',
            'warehouse_equipment' => 'Warehouse Equipment',
            'charging_room' => 'Charging Room',
            'cross_docking' => 'Cross Docking',
            'cranes_runways' => 'Cranes Runways',
            'cadastral_number' => 'Cadastral Number',
            'cadastral_number_land' => 'Cadastral Number Land',
            'field_allow_usage' => 'Field Allow Usage',
            'available_from' => 'Available From',
            'own_type' => 'Own Type',
            'own_type_land' => 'Own Type Land',
            'land_category' => 'Land Category',
            'entry_territory' => 'Entry Territory',
            'parking_car' => 'Parking Car',
            'parking_car_value' => 'Parking Car Value',
            'parking_lorry' => 'Parking Lorry',
            'parking_lorry_value' => 'Parking Lorry Value',
            'parking_truck' => 'Parking Truck',
            'parking_truck_value' => 'Parking Truck Value',
            'built_to_suit' => 'Built To Suit',
            'built_to_suit_time' => 'Built To Suit Time',
            'built_to_suit_plan' => 'Built To Suit Plan',
            'rent_business' => 'Rent Business',
            'rent_business_fill' => 'Rent Business Fill',
            'rent_business_price' => 'Rent Business Price',
            'rent_business_long_contracts' => 'Rent Business Long Contracts',
            'rent_business_last_repair' => 'Rent Business Last Repair',
            'rent_business_payback' => 'Rent Business Payback',
            'rent_business_income' => 'Rent Business Income',
            'rent_business_profit' => 'Rent Business Profit',
            'sale_company' => 'Sale Company',
            'holidays' => 'Holidays',
            'ad_realtor' => 'Ad Realtor',
            'ad_cian' => 'Ad Cian',
            'ad_cian_top3' => 'Ad Cian Top 3',
            'ad_cian_premium' => 'Ad Cian Premium',
            'ad_cian_hl' => 'Ad Cian Hl',
            'ad_yandex' => 'Ad Yandex',
            'ad_yandex_raise' => 'Ad Yandex Raise',
            'ad_yandex_promotion' => 'Ad Yandex Promotion',
            'ad_yandex_premium' => 'Ad Yandex Premium',
            'ad_arendator' => 'Ad Arendator',
            'ad_free' => 'Ad Free',
            'ad_special' => 'Ad Special',
            'description' => 'Description',
            'deleted' => 'Deleted',
            'test_only' => 'Test Only',
            'is_exclusive' => 'Is Exclusive',
            'deal_id' => 'Deal ID',
            'hide_from_market' => 'Hide From Market',
        ];
    }
    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMaxOfficeArea()
    {
        return max($this->area_office_max, $this->area_office_min);
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMaxFloorArea()
    {
        return max($this->area_floor_max, $this->area_floor_min);
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMaxMezzanineArea()
    {
        return max($this->area_mezzanine_max, $this->area_mezzanine_min);
    }
    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMinOfficeArea()
    {
        return min($this->area_office_max, $this->area_office_min);
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMinFloorArea()
    {
        return min($this->area_floor_max, $this->area_floor_min);
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMinMezzanineArea()
    {
        return min($this->area_mezzanine_max, $this->area_mezzanine_min);
    }
    public  function extraFields()
    {
        $extraFields = parent::extraFields();
        $extraFields['object_small_info'] = function () {
            return ['photo' => json_decode($this->object->photo)];
        };
        return $extraFields;
    }
    public function fields()
    {
        $fields = parent::fields();
        $fields['last_update_format'] = function ($fields) {
            return $fields['last_update'] ? Yii::$app->formatter->format($fields['last_update'], 'datetime') : null;
        };

        $fields['photos'] = function ($fields) {
            return json_decode($fields['photos']);
        };
        $fields['thumb'] = function ($fields) {
            $photos = json_decode($fields['photos'], true);
            $objectPhotos = json_decode($this->object->photo, true);
            if ($photos && is_array($photos)) {
                foreach ($photos as $photo) {
                    if (is_string($photo) && mb_strlen($photo) > 2) {
                        return Yii::$app->params['url']['objects'] . $photo;
                    }
                }
            }

            if ($objectPhotos && is_array($objectPhotos) && is_string($objectPhotos[0]) && strlen($objectPhotos[0]) > 2) {
                return Yii::$app->params['url']['objects'] . $objectPhotos[0];
            }
            return Yii::$app->params['url']['image_not_found'];
        };
        $fields['object_type'] = function ($fields) {
            return json_decode($fields['object_type']);
        };
        $fields['direction_name'] = function ($fields) {
            if (!$fields['direction_name'] || $fields['direction_name'] == "0") {
                return null;
            }
            return $fields['direction_name'];
        };
        $fields['blocks'] = function ($fields) {
            return json_decode($fields['blocks']);
        };
        $fields['calc_cranes'] = function ($fields) {
            return $this->calcMinMaxArea($fields->cranes_min, $fields->cranes_max);
        };
        $fields['calc_cranes_gantry'] = function ($fields) {
            return $this->calcMinMaxArea($fields->cranes_gantry_min, $fields->cranes_gantry_max);
        };
        $fields['calc_load_floor'] = function ($fields) {
            return $this->calcMinMaxArea($fields->load_floor_min, $fields->load_floor_max);
        };
        $fields['calc_load_mezzanine'] = function ($fields) {
            return $this->calcMinMaxArea($fields->load_mezzanine_min, $fields->load_mezzanine_max);
        };
        $fields['calc_temperature'] = function ($fields) {
            return $this->calcMinMaxArea($fields->temperature_min, $fields->temperature_max);
        };
        $fields['calc_floors'] = function ($fields) {
            return $this->calcMinMaxArea($fields->floor_min, $fields->floor_max);
        };
        $fields['calc_ceilingHeight'] = function ($fields) {
            return $this->calcMinMaxArea($fields->ceiling_height_min, $fields->ceiling_height_max);
        };
        $fields['calc_area_floor'] = function ($fields) {
            return $this->calcMinMaxArea($fields->area_floor_min, $fields->area_floor_max);
        };
        $fields['calc_area_mezzanine'] = function ($fields) {
            return $this->calcMinMaxArea($fields->area_mezzanine_min, $fields->area_mezzanine_max);
        };
        $fields['calc_area_tech'] = function ($fields) {
            return $this->calcMinMaxArea($fields->area_tech_min, $fields->area_tech_max);
        };
        $fields['calc_area_office'] = function ($fields) {
            return $this->calcMinMaxArea($fields->area_office_min, $fields->area_office_max);
        };
        $fields['calc_area_warehouse'] = function ($fields) {
            $min = $fields->area_floor_min;
            $max = $fields->area_mezzanine_max + $fields->area_floor_max;
            return $this->calcMinMaxArea($min, $max);
        };
        $fields['calc_area_general'] = function ($f) {
            // $area_warehouse_max = max([(int)$fields->area_floor_min, (int)($fields->area_mezzanine_max + $fields->area_floor_max)]);
            // $area_office = max([$fields->area_office_min, $fields->area_office_max]);
            // return Yii::$app->formatter->format($area_warehouse_max + $area_office, 'decimal');
            return $this->calcMinMaxArea(min($f->area_min, $f->area_max), max($f->area_min, $f->area_max));
        };
        $fields['calc_price_floor'] = function ($fields) {
            return $this->calcMinMaxArea($fields->price_floor_min, $fields->price_floor_max);
        };
        $fields['calc_price_mezzanine'] = function ($fields) {
            return $this->calcMinMaxArea($fields->price_mezzanine_min, $fields->price_mezzanine_max);
        };
        $fields['calc_price_office'] = function ($fields) {
            return $this->calcMinMaxArea($fields->price_office_min, $fields->price_office_max);
        };
        $fields['calc_price_sale'] = function ($fields) {
            return $this->calcMinMaxArea($fields->price_sale_min, $fields->price_sale_max);
        };
        $fields['calc_price_safe_pallet'] = function ($fields) {
            return $this->calcMinMaxArea($fields->price_safe_pallet_min, $fields->price_safe_pallet_max);
        };
        $fields['calc_pallet_place'] = function ($fields) {
            return $this->calcMinMaxArea($fields->pallet_place_min, $fields->pallet_place_max);
        };
        $fields['calc_price_warehouse'] = function ($fields) {
            $array = [
                // $fields->price_mezzanine_min,
                $fields->price_floor_min,
                // $fields->price_mezzanine_max,
                $fields->price_floor_max,
            ];
            $min = min($array);
            $max = max($array);
            return $this->calcMinMaxArea($min, $max);
        };
        $fields['calc_price_general'] = function ($fields) {
            if ($fields->deal_type == self::DEAL_TYPE_SALE) {
                return $this->calcPriceGeneralForSale($fields);
            }
            return $this->calcPriceGeneralForRent($fields);
        };
        return $fields;
    }
    public function calcPriceGeneralForRent($fields)
    {
        $array = [
            $fields->price_mezzanine_min,
            $fields->price_floor_min,
            $fields->price_mezzanine_max,
            $fields->price_floor_max,
            $fields->price_office_max,
            $fields->price_office_max,
        ];
        $min = min($array);
        $max = max($array);
        return $this->calcMinMaxArea($min, $max);
    }
    public function calcPriceGeneralForSale($fields)
    {
        // $area_warehouse_max = max([(int)$fields->area_floor_min, (int)($fields->area_mezzanine_max + $fields->area_floor_max)]);
        // $area_office = max([$fields->area_office_min, $fields->area_office_max]);
        // $calc_area_general = $area_warehouse_max + $area_office;
        // $min = $fields->price_sale_min * $calc_area_general;
        // $max = $fields->price_sale_max * $calc_area_general;
        // return $this->calcMinMaxArea($min, $max);
        $min = $fields->price_sale_min * $fields->area_min;
        $max = $fields->price_sale_max * $fields->area_max;
        return $this->calcMinMaxArea($min, $max);
    }
    public function calcMinMaxArea($min, $max)
    {
        $min = (int)$min;
        $max = (int)$max;
        $result = 0;
        if ($min == $max) {
            return Yii::$app->formatter->format($min, 'decimal');
        }
        if ($min) {
            $result = Yii::$app->formatter->format($min, 'decimal');
        }
        if ($max) {
            if ($min) {
                $result .= " - " . Yii::$app->formatter->format($max, 'decimal');
            } else {
                $result = Yii::$app->formatter->format($max, 'decimal');
            }
        }
        return $result;
    }


    public static function normalizeDealType($dealType)
    {
        // Тройное равно обязательно инача почему-то 0 == null
        if ($dealType === null) {
            return;
        }
        $dealTypes = [
            Request::DEAL_TYPE_RENT => [OfferMix::DEAL_TYPE_RENT, OfferMix::DEAL_TYPE_SUBLEASE, OfferMix::DEAL_TYPE_RESPONSE_STORAGE],
            Request::DEAL_TYPE_SALE => OfferMix::DEAL_TYPE_SALE,
            Request::DEAL_TYPE_RESPONSE_STORAGE => OfferMix::DEAL_TYPE_RESPONSE_STORAGE,
            Request::DEAL_TYPE_SUBLEASE => OfferMix::DEAL_TYPE_SUBLEASE,
        ];
        return $dealTypes[$dealType];
    }
    public static function normalizeRegions($data)
    {
        if ($data == null) {
            return;
        }
        $array = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            0 => 6,
            6 => 7,
            7 => 8,
            14 => 9,
            9 => 10,
            8 => 11,
            10 => 17,
            11 => 18,
            13 => 19,
            12 => 20,
            15 => 21,
        ];
        // $array = [
        //     14 => 9,
        //     7 => 8,
        //     6 => 7,
        //     0 => 6,
        //     5 => 5,
        //     4 => 4,
        //     3 => 3,
        //     2 => 2,
        //     1 => 1,
        //     13 => 19,
        //     11 => 18,
        //     10 => 17,
        // ];
        return $array[$data];
    }
    public static function normalizeDistricts($data)
    {
        if ($data == null) {
            return;
        }
        $array = [
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
            6 => 7,
            7 => 8,
            8 => 9,
            9  => 10,
            10 => 11,
        ];
        return $array[$data];
    }
    public static function normalizeDirections($data)
    {
        if ($data == null) {
            return;
        }
        $array = [
            0 => 2,
            1 => 3,
            2 => 4,
            3 => 5,
            4 => 6,
            5 => 7,
            6 => 8,
            7 => 9,
        ];
        return $array[$data];
    }
    public static function normalizeAgentId($consultant_id)
    {
        if ($consultant_id == null) {
            return null;
        }
        $newUsersArray = [];
        foreach (self::USERS as $key => $value) {
            $newUsersArray[$value] = $key;
        }
        return $newUsersArray[$consultant_id];
    }

    public static function normalizeObjectClasses($data)
    {
        if ($data == null) {
            return;
        }
        $array = [
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4,
        ];
        return $array[$data];
    }
    public static function normalizeGateTypes($data)
    {
        if ($data == null) {
            return;
        }
        $array = [
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4,
        ];
        return $array[$data];
    }

    public static function normalizeObjectTypes($data)
    {
        if ($data == null) {
            return;
        }
        $array = [
            0  => 1,
            1  => 3,
            2  => 8,
            3  => 10,
            4  => 11,
            5  => 12,
            6  => 13,
            7  => 16,
            8  => 27,
            9  => 28,
            10  => 29,
            11  => 7,
            12  => 2,
            13  => 4,
            14  => 5,
            15  => 6,
            16  => 17,
            17  => 18,
            18  => 19,
            19  => 20,
            20  => 21,
            21  => 22,
            22  => 23,
            23  => 24,
            24  => 30,
            25  => 9,
            26  => 14,
            27  => 15,
            28  => 26,
            29  => 31,
            30  => 32,
            31  => 33,
        ];
        return $array[$data];
    }
    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(TimelineStepObjectComment::className(), ['object_id' => 'object_id', 'type_id' => 'type_id', 'offer_id' => 'original_id']);
    }

    public function getTimelineComments($timeline_id)
    {
        return $this->hasMany(TimelineStepObjectComment::className(), ['object_id' => 'object_id', 'type_id' => 'type_id', 'offer_id' => 'original_id'])->where(['timeline_id' => $timeline_id]);
    }
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['id' => 'object_id']);
    }
    public function getOffer()
    {
        return $this->hasOne(Offers::class, ['id' => 'original_id']);
    }
    public function getBlock()
    {
        return $this->hasOne(ObjectsBlock::class, ['id' => 'original_id']);
    }
    public function getComplex()
    {
        return $this->hasOne(Complex::class, ['id' => 'complex_id']);
    }
    public function getMiniOffersMix()
    {
        return $this->hasMany(self::class, ['parent_id' => 'original_id'])
            ->where(['c_industry_offers_mix.deleted' => 0, 'c_industry_offers_mix.type_id' => self::MINI_TYPE_ID]);
    }
    public function getGeneralOffersMix()
    {
        return $this->hasOne(self::class, ['original_id' => 'parent_id'])
            ->where(['c_industry_offers_mix.deleted' => 0, 'c_industry_offers_mix.type_id' => self::GENERAL_TYPE_ID]);
    }
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }
    public function getAgent(): ActiveQuery
    {
        return $this->hasOne(OldDbUser::class, ['id' => 'agent_id']);
    }
    public function getConsultant(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id_new'])->via('agent');
    }
    public static function find(): OfferMixQuery
    {
        return new OfferMixQuery(get_called_class());
    }
}
