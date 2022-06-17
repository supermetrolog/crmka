<?php

namespace app\models\oldDb;

use app\models\Company;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;

/**
 * This is the model class for table "c_industry".
 *
 * @property int $id Идентификатор
 * @property string $title
 * @property int|null $location_id Айди местоположения
 * @property int $last_update
 * @property int|null $is_land
 * @property string|null $buildings_on_territory_id
 * @property int|null $buildings_on_territory
 * @property string|null $buildings_on_territory_description
 * @property int|null $first_line
 * @property int|null $complex_id Айди комплекса
 * @property int|null $contact_id Собственник
 * @property int|null $company_id Компания собственник
 * @property string|null $owners Собственники
 * @property int|null $author_id Автор поста(кто создал внес)
 * @property int|null $type
 * @property string|null $object_type Тип объекта
 * @property string|null $floors_building Этажи
 * @property string|null $object_type2
 * @property int|null $status_id Результат/Статус
 * @property int|null $status_rent
 * @property int|null $status_sale
 * @property int|null $status_safe
 * @property int|null $status_subrent
 * @property int|null $onsite_noprice
 * @property string|null $purpose_warehouse Назначение склада
 * @property string $purposes
 * @property int $floor_type Тип покрытия
 * @property int|null $firefighting_type Пожаротушение система
 * @property int $object_class Класс объекта
 * @property int $region Регион
 * @property int $district Район
 * @property int $direction Направление
 * @property int $village Населенный пункт
 * @property int $highway Шоссе
 * @property int $highway_secondary Дублирующее шоссе
 * @property int|null $from_mkad От МКАД
 * @property int $metro Метро
 * @property string|null $address Адрес
 * @property string|null $cadastral_number Кадастровый номер
 * @property string|null $yandex_address Адрес формата Яндекс
 * @property int|null $area_field_full Площадь участка
 * @property int|null $area_building Общая площадь
 * @property int|null $area_floor_full Общая площадь пола
 * @property int|null $area_office_full Офисные помещения
 * @property int|null $area_tech_full
 * @property int|null $land Участок (вспомогательное)
 * @property int|null $land_length Длина участка
 * @property int|null $land_width Ширина участка
 * @property string|null $dsection Габариты участка
 * @property int|null $barrier Шлагбаум
 * @property int|null $fence_around_perimeter Забор по периметру
 * @property int|null $finishing Готовность к въезду
 * @property int|null $l_category Категория земли
 * @property int|null $l_function Разрешенное использование
 * @property int|null $l_property Вид права
 * @property int|null $floors Этажность
 * @property int|null $deposit Величина депозита
 * @property int|null $pledge Залог
 * @property int|null $prepay_subrent
 * @property int $facing_type Внешняя отделка
 * @property string|null $elevators Пассажирские лифты
 * @property string|null $cranes_gantry Козловые краны
 * @property string|null $cranes_railway Железнодорожные краны
 * @property int|null $cranes_runways Подкрановые пути
 * @property int $railway Ж/д ветка
 * @property int|null $railway_value Ж/д ветка протяженность
 * @property int $nooffice Нет офисов
 * @property int $phone_line Есть ли Телефония
 * @property string $telecommunications Телекоммуникации
 * @property int|null $year_build Год постройки
 * @property int|null $year_repair Год реконструкции
 * @property int $guard Охрана
 * @property int|null $entry_territory Въезд на территорию (Тип)
 * @property int|null $entry_territory_type
 * @property string|null $parking_car_type Парковка легковая какая
 * @property string|null $parking_lorry_type Парковка грузовичка какая
 * @property string|null $parking_truck_type Парковка грузовая какая
 * @property string|null $comments Комментарии
 * @property string|null $description Описание 
 * @property string|null $description_auto Описание авто
 * @property string|null $infrastructure Инфраструктура
 * @property int $gas Газ
 * @property int $ttk_mkad
 * @property string $parking Парковка
 * @property int $parking_car Парковка легковая
 * @property int $parking_car_value Парковка легковая цена
 * @property int $parking_lorry Парковка грузовичка
 * @property int $parking_lorry_value Парковка грузовичка цена
 * @property int $parking_truck Парковка грузовая
 * @property int $parking_truck_value Парковка грузовая цена
 * @property int $steam Пар
 * @property int $deposit_former Страховой депозит
 * @property int $is_prepay
 * @property int $agent_visited Брокер был на объекте
 * @property int $agent_visited_sale
 * @property int $agent_visited_safe
 * @property int $agent_visited_subrent
 * @property float|null $power Электричество доступно
 * @property float|null $power_all Электричество всего
 * @property float|null $power_available
 * @property int|null $gas_value Газ сколько кубов
 * @property int $steam_value
 * @property int $water Водоснабжение
 * @property int $water_value Водоснабжение объем
 * @property int $sewage Канализация
 * @property int|null $sewage_central Канализация центральная
 * @property int|null $sewage_central_value Канализация центральная объем
 * @property int|null $sewage_rain Канализация ливневая
 * @property int $heating Отопление
 * @property int|null $heating_central
 * @property int $ventilation Вентиляция/кондиционирование
 * @property string|null $internet_type Интернет
 * @property int|null $internet
 * @property string|null $safety_systems Системы безопасности
 * @property string|null $deal_type_help Тип сделки (вспомогательное)
 * @property int $sale_price Стоимость (продажи полной)
 * @property int $sale_price_metr Стоимость (продажи, за кв.м.)
 * @property int $rent_price Стоимость (аренды, за кв.м. в год)
 * @property int $subrent_price
 * @property int $rent_price_safe Стоимость (аренды, за 1 паллетоместо)
 * @property int $office_price Стоимость (офисов)
 * @property int $price_mezzanine Стоимость (мезонин)
 * @property string|null $rent_inc Стоимость включает - аренда
 * @property string|null $rent_inc_safe Стоимость включает - ответ-хранение
 * @property string|null $rent_inc_office Стоимость включает - офисы
 * @property int|null $tax_form Система налогов
 * @property string|null $inc_services Включенные КУ
 * @property string|null $incs_currency Стоимость включает (аренды, за кв.м. в год)
 * @property int $result Результат
 * @property int $result_sale
 * @property int $result_safe
 * @property int $result_subrent
 * @property int $result_who Кем
 * @property float $longitude Долгота
 * @property float $latitude Широта
 * @property int $agent_id Агент
 * @property int $agent_sale Агент по продаже
 * @property int $agent_safe Агент по ответ хр
 * @property int $agent_subrent Агент по субаренде
 * @property int $onsite На сайте
 * @property int $contract Договор подписан
 * @property int $onsite_top Спецпредложение
 * @property int $electricity_included Электричество и вода отдельно
 * @property int $deleted Удален
 * @property string|null $slcomments Служебный комментарий
 * @property int|null $openstage Открытые площадки
 * @property string|null $_calc_rent_payinc
 * @property string|null $_calc_safe_payinc
 * @property string|null $_calc_sale_payinc
 * @property string|null $_calc_subrent_payinc
 * @property float|null $owner_pays_howmuch Договоренность о комиссии с собственником - АРЕНДА
 * @property float|null $owner_pays_howmuch_sale
 * @property float|null $owner_pays_howmuch_safe
 * @property float|null $owner_pays_howmuch_subrent
 * @property float|null $owner_pays_howmuch_4client
 * @property float|null $owner_pays_howmuch_4client_sale
 * @property float|null $owner_pays_howmuch_4client_safe
 * @property float|null $owner_pays_howmuch_4client_subrent
 * @property string|null $contract_date Действие договора до	
 * @property int|null $bargain_rent Возможен торг - аренда
 * @property int|null $bargain_sale Возможен торг - продажа
 * @property int|null $bargain_office Возможен торг - офисы
 * @property int|null $bargain_safe Возможен торг - ответ-хранение
 * @property int|null $from_metro От метро сколько
 * @property int $from_metro_value От метро как
 * @property int $railway_station Ближайшая железнодорожная станция 
 * @property int $from_station От станции  на чем
 * @property int $from_station_value От станции
 * @property int $from_busstop
 * @property int $from_busstop_value
 * @property int $entrance_type
 * @property int $plain_type Вид права
 * @property string $area_mezzanine_full
 * @property int $safe_price_rack
 * @property int $safe_price_rack_oversized
 * @property int $safe_price_cell
 * @property int $safe_price_floor_oversized
 * @property string $photo
 * @property string $videos
 * @property int $publ_time
 * @property int $activity
 * @property int $order_row
 * @property int|null $video_control
 * @property int|null $access_control
 * @property int|null $security_alert
 * @property int|null $fire_alert
 * @property int|null $smoke_exhaust
 * @property int|null $canteen
 * @property int|null $hostel
 * @property int|null $street_area
 * @property int|null $own_type
 * @property string|null $building_layouts
 * @property string|null $building_presentations
 * @property string|null $building_contracts
 * @property string|null $building_property_documents
 * @property string|null $photos_360
 * @property string|null $import_sale_cian
 * @property string|null $import_sale_free
 * @property string|null $import_sale_yandex
 * @property string|null $import_rent_cian
 * @property string|null $import_rent_free
 * @property string|null $import_rent_yandex
 * @property string|null $import_sale_cian_premium
 * @property string|null $import_rent_cian_premium
 * @property string|null $import_sale_cian_top3
 * @property string|null $import_rent_cian_top3
 * @property string|null $import_sale_cian_hl
 * @property string|null $import_rent_cian_hl
 * @property int|null $fence
 * @property string|null $field_allow_usage
 * @property int|null $land_category
 * @property int|null $status
 * @property int|null $status_reason
 * @property string|null $status_description
 * @property int|null $own_type_land
 * @property int|null $area_outside
 * @property int|null $description_complex
 * @property int|null $description_manual_use
 * @property int|null $gas_near
 * @property int|null $mkad_ttk_between
 * @property int|null $empty_line
 * @property int|null $title_empty_main
 * @property int|null $title_empty_communications
 * @property int|null $title_empty_security
 * @property int|null $title_empty_railway
 * @property int|null $title_empty_infrastructure
 * @property int|null $landscape_type
 * @property int|null $land_use_restrictions
 * @property string|null $cadastral_number_land
 * @property int|null $documents_old
 * @property int|null $test_only
 */
class Objects extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'c_industry';
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
            [['title', 'last_update', 'purposes', 'floor_type', 'highway_secondary', 'telecommunications', 'parking', 'parking_car_value', 'parking_lorry', 'parking_lorry_value', 'parking_truck_value', 'steam_value', 'water_value', 'from_station', 'from_station_value', 'from_busstop', 'from_busstop_value', 'entrance_type', 'photo', 'videos', 'publ_time', 'order_row'], 'required'],
            [['location_id', 'last_update', 'is_land', 'buildings_on_territory', 'first_line', 'complex_id', 'contact_id', 'company_id', 'author_id', 'type', 'status_id', 'status_rent', 'status_sale', 'status_safe', 'status_subrent', 'onsite_noprice', 'floor_type', 'firefighting_type', 'object_class', 'region', 'district', 'direction', 'village', 'highway', 'highway_secondary', 'from_mkad', 'metro', 'area_field_full', 'area_building', 'area_floor_full', 'area_office_full', 'area_tech_full', 'land', 'land_length', 'land_width', 'barrier', 'fence_around_perimeter', 'finishing', 'l_category', 'l_function', 'l_property', 'floors', 'deposit', 'pledge', 'prepay_subrent', 'facing_type', 'cranes_runways', 'railway', 'railway_value', 'nooffice', 'phone_line', 'year_build', 'year_repair', 'guard', 'entry_territory', 'entry_territory_type', 'gas', 'ttk_mkad', 'parking_car', 'parking_car_value', 'parking_lorry', 'parking_lorry_value', 'parking_truck', 'parking_truck_value', 'steam', 'deposit_former', 'is_prepay', 'agent_visited', 'agent_visited_sale', 'agent_visited_safe', 'agent_visited_subrent', 'gas_value', 'steam_value', 'water', 'water_value', 'sewage', 'sewage_central', 'sewage_central_value', 'sewage_rain', 'heating', 'heating_central', 'ventilation', 'internet', 'sale_price', 'sale_price_metr', 'rent_price', 'subrent_price', 'rent_price_safe', 'office_price', 'price_mezzanine', 'tax_form', 'result', 'result_sale', 'result_safe', 'result_subrent', 'result_who', 'agent_id', 'agent_sale', 'agent_safe', 'agent_subrent', 'onsite', 'contract', 'onsite_top', 'electricity_included', 'deleted', 'openstage', 'bargain_rent', 'bargain_sale', 'bargain_office', 'bargain_safe', 'from_metro', 'from_metro_value', 'railway_station', 'from_station', 'from_station_value', 'from_busstop', 'from_busstop_value', 'entrance_type', 'plain_type', 'safe_price_rack', 'safe_price_rack_oversized', 'safe_price_cell', 'safe_price_floor_oversized', 'publ_time', 'activity', 'order_row', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'smoke_exhaust', 'canteen', 'hostel', 'street_area', 'own_type', 'fence', 'land_category', 'status', 'status_reason', 'own_type_land', 'area_outside', 'description_complex', 'description_manual_use', 'gas_near', 'mkad_ttk_between', 'empty_line', 'title_empty_main', 'title_empty_communications', 'title_empty_security', 'title_empty_railway', 'title_empty_infrastructure', 'landscape_type', 'land_use_restrictions', 'documents_old', 'test_only'], 'integer'],
            [['purposes', 'comments', 'description', 'description_auto', 'slcomments', 'photo', 'videos', 'building_layouts', 'building_presentations', 'building_contracts', 'building_property_documents', 'photos_360', 'import_sale_cian', 'import_sale_free', 'import_sale_yandex', 'import_rent_cian', 'import_rent_free', 'import_rent_yandex', 'import_sale_cian_premium', 'import_rent_cian_premium', 'import_sale_cian_top3', 'import_rent_cian_top3', 'import_sale_cian_hl', 'import_rent_cian_hl', 'field_allow_usage', 'cadastral_number_land'], 'string'],
            [['power', 'power_all', 'power_available', 'longitude', 'latitude', 'owner_pays_howmuch', 'owner_pays_howmuch_sale', 'owner_pays_howmuch_safe', 'owner_pays_howmuch_subrent', 'owner_pays_howmuch_4client', 'owner_pays_howmuch_4client_sale', 'owner_pays_howmuch_4client_safe', 'owner_pays_howmuch_4client_subrent'], 'number'],
            [['contract_date'], 'safe'],
            [['title', 'buildings_on_territory_id', 'cranes_gantry', 'cranes_railway', 'status_description'], 'string', 'max' => 300],
            [['buildings_on_territory_description'], 'string', 'max' => 1000],
            [['owners', 'object_type2', 'purpose_warehouse', 'telecommunications', 'parking_car_type', 'parking_lorry_type', 'parking_truck_type', 'infrastructure', 'parking', 'internet_type', 'safety_systems', 'deal_type_help', 'rent_inc', 'rent_inc_safe', 'rent_inc_office', 'incs_currency'], 'string', 'max' => 100],
            [['object_type'], 'string', 'max' => 30],
            [['floors_building'], 'string', 'max' => 50],
            [['address', 'cadastral_number', 'yandex_address', 'dsection', 'elevators', 'inc_services', '_calc_rent_payinc', '_calc_safe_payinc', '_calc_sale_payinc', '_calc_subrent_payinc'], 'string', 'max' => 200],
            [['area_mezzanine_full'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'location_id' => 'Location ID',
            'last_update' => 'Last Update',
            'is_land' => 'Is Land',
            'buildings_on_territory_id' => 'Buildings On Territory ID',
            'buildings_on_territory' => 'Buildings On Territory',
            'buildings_on_territory_description' => 'Buildings On Territory Description',
            'first_line' => 'First Line',
            'complex_id' => 'Complex ID',
            'contact_id' => 'Contact ID',
            'company_id' => 'Company ID',
            'owners' => 'Owners',
            'author_id' => 'Author ID',
            'type' => 'Type',
            'object_type' => 'Object Type',
            'floors_building' => 'Floors Building',
            'object_type2' => 'Object Type 2',
            'status_id' => 'Status ID',
            'status_rent' => 'Status Rent',
            'status_sale' => 'Status Sale',
            'status_safe' => 'Status Safe',
            'status_subrent' => 'Status Subrent',
            'onsite_noprice' => 'Onsite Noprice',
            'purpose_warehouse' => 'Purpose Warehouse',
            'purposes' => 'Purposes',
            'floor_type' => 'Floor Type',
            'firefighting_type' => 'Firefighting Type',
            'object_class' => 'Object Class',
            'region' => 'Region',
            'district' => 'District',
            'direction' => 'Direction',
            'village' => 'Village',
            'highway' => 'Highway',
            'highway_secondary' => 'Highway Secondary',
            'from_mkad' => 'From Mkad',
            'metro' => 'Metro',
            'address' => 'Address',
            'cadastral_number' => 'Cadastral Number',
            'yandex_address' => 'Yandex Address',
            'area_field_full' => 'Area Field Full',
            'area_building' => 'Area Building',
            'area_floor_full' => 'Area Floor Full',
            'area_office_full' => 'Area Office Full',
            'area_tech_full' => 'Area Tech Full',
            'land' => 'Land',
            'land_length' => 'Land Length',
            'land_width' => 'Land Width',
            'dsection' => 'Dsection',
            'barrier' => 'Barrier',
            'fence_around_perimeter' => 'Fence Around Perimeter',
            'finishing' => 'Finishing',
            'l_category' => 'L Category',
            'l_function' => 'L Function',
            'l_property' => 'L Property',
            'floors' => 'Floors',
            'deposit' => 'Deposit',
            'pledge' => 'Pledge',
            'prepay_subrent' => 'Prepay Subrent',
            'facing_type' => 'Facing Type',
            'elevators' => 'Elevators',
            'cranes_gantry' => 'Cranes Gantry',
            'cranes_railway' => 'Cranes Railway',
            'cranes_runways' => 'Cranes Runways',
            'railway' => 'Railway',
            'railway_value' => 'Railway Value',
            'nooffice' => 'Nooffice',
            'phone_line' => 'Phone Line',
            'telecommunications' => 'Telecommunications',
            'year_build' => 'Year Build',
            'year_repair' => 'Year Repair',
            'guard' => 'Guard',
            'entry_territory' => 'Entry Territory',
            'entry_territory_type' => 'Entry Territory Type',
            'parking_car_type' => 'Parking Car Type',
            'parking_lorry_type' => 'Parking Lorry Type',
            'parking_truck_type' => 'Parking Truck Type',
            'comments' => 'Comments',
            'description' => 'Description',
            'description_auto' => 'Description Auto',
            'infrastructure' => 'Infrastructure',
            'gas' => 'Gas',
            'ttk_mkad' => 'Ttk Mkad',
            'parking' => 'Parking',
            'parking_car' => 'Parking Car',
            'parking_car_value' => 'Parking Car Value',
            'parking_lorry' => 'Parking Lorry',
            'parking_lorry_value' => 'Parking Lorry Value',
            'parking_truck' => 'Parking Truck',
            'parking_truck_value' => 'Parking Truck Value',
            'steam' => 'Steam',
            'deposit_former' => 'Deposit Former',
            'is_prepay' => 'Is Prepay',
            'agent_visited' => 'Agent Visited',
            'agent_visited_sale' => 'Agent Visited Sale',
            'agent_visited_safe' => 'Agent Visited Safe',
            'agent_visited_subrent' => 'Agent Visited Subrent',
            'power' => 'Power',
            'power_all' => 'Power All',
            'power_available' => 'Power Available',
            'gas_value' => 'Gas Value',
            'steam_value' => 'Steam Value',
            'water' => 'Water',
            'water_value' => 'Water Value',
            'sewage' => 'Sewage',
            'sewage_central' => 'Sewage Central',
            'sewage_central_value' => 'Sewage Central Value',
            'sewage_rain' => 'Sewage Rain',
            'heating' => 'Heating',
            'heating_central' => 'Heating Central',
            'ventilation' => 'Ventilation',
            'internet_type' => 'Internet Type',
            'internet' => 'Internet',
            'safety_systems' => 'Safety Systems',
            'deal_type_help' => 'Deal Type Help',
            'sale_price' => 'Sale Price',
            'sale_price_metr' => 'Sale Price Metr',
            'rent_price' => 'Rent Price',
            'subrent_price' => 'Subrent Price',
            'rent_price_safe' => 'Rent Price Safe',
            'office_price' => 'Office Price',
            'price_mezzanine' => 'Price Mezzanine',
            'rent_inc' => 'Rent Inc',
            'rent_inc_safe' => 'Rent Inc Safe',
            'rent_inc_office' => 'Rent Inc Office',
            'tax_form' => 'Tax Form',
            'inc_services' => 'Inc Services',
            'incs_currency' => 'Incs Currency',
            'result' => 'Result',
            'result_sale' => 'Result Sale',
            'result_safe' => 'Result Safe',
            'result_subrent' => 'Result Subrent',
            'result_who' => 'Result Who',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'agent_id' => 'Agent ID',
            'agent_sale' => 'Agent Sale',
            'agent_safe' => 'Agent Safe',
            'agent_subrent' => 'Agent Subrent',
            'onsite' => 'Onsite',
            'contract' => 'Contract',
            'onsite_top' => 'Onsite Top',
            'electricity_included' => 'Electricity Included',
            'deleted' => 'Deleted',
            'slcomments' => 'Slcomments',
            'openstage' => 'Openstage',
            '_calc_rent_payinc' => 'Calc Rent Payinc',
            '_calc_safe_payinc' => 'Calc Safe Payinc',
            '_calc_sale_payinc' => 'Calc Sale Payinc',
            '_calc_subrent_payinc' => 'Calc Subrent Payinc',
            'owner_pays_howmuch' => 'Owner Pays Howmuch',
            'owner_pays_howmuch_sale' => 'Owner Pays Howmuch Sale',
            'owner_pays_howmuch_safe' => 'Owner Pays Howmuch Safe',
            'owner_pays_howmuch_subrent' => 'Owner Pays Howmuch Subrent',
            'owner_pays_howmuch_4client' => 'Owner Pays Howmuch  4client',
            'owner_pays_howmuch_4client_sale' => 'Owner Pays Howmuch  4client Sale',
            'owner_pays_howmuch_4client_safe' => 'Owner Pays Howmuch  4client Safe',
            'owner_pays_howmuch_4client_subrent' => 'Owner Pays Howmuch  4client Subrent',
            'contract_date' => 'Contract Date',
            'bargain_rent' => 'Bargain Rent',
            'bargain_sale' => 'Bargain Sale',
            'bargain_office' => 'Bargain Office',
            'bargain_safe' => 'Bargain Safe',
            'from_metro' => 'From Metro',
            'from_metro_value' => 'From Metro Value',
            'railway_station' => 'Railway Station',
            'from_station' => 'From Station',
            'from_station_value' => 'From Station Value',
            'from_busstop' => 'From Busstop',
            'from_busstop_value' => 'From Busstop Value',
            'entrance_type' => 'Entrance Type',
            'plain_type' => 'Plain Type',
            'area_mezzanine_full' => 'Area Mezzanine Full',
            'safe_price_rack' => 'Safe Price Rack',
            'safe_price_rack_oversized' => 'Safe Price Rack Oversized',
            'safe_price_cell' => 'Safe Price Cell',
            'safe_price_floor_oversized' => 'Safe Price Floor Oversized',
            'photo' => 'Photo',
            'videos' => 'Videos',
            'publ_time' => 'Publ Time',
            'activity' => 'Activity',
            'order_row' => 'Order Row',
            'video_control' => 'Video Control',
            'access_control' => 'Access Control',
            'security_alert' => 'Security Alert',
            'fire_alert' => 'Fire Alert',
            'smoke_exhaust' => 'Smoke Exhaust',
            'canteen' => 'Canteen',
            'hostel' => 'Hostel',
            'street_area' => 'Street Area',
            'own_type' => 'Own Type',
            'building_layouts' => 'Building Layouts',
            'building_presentations' => 'Building Presentations',
            'building_contracts' => 'Building Contracts',
            'building_property_documents' => 'Building Property Documents',
            'photos_360' => 'Photos  360',
            'import_sale_cian' => 'Import Sale Cian',
            'import_sale_free' => 'Import Sale Free',
            'import_sale_yandex' => 'Import Sale Yandex',
            'import_rent_cian' => 'Import Rent Cian',
            'import_rent_free' => 'Import Rent Free',
            'import_rent_yandex' => 'Import Rent Yandex',
            'import_sale_cian_premium' => 'Import Sale Cian Premium',
            'import_rent_cian_premium' => 'Import Rent Cian Premium',
            'import_sale_cian_top3' => 'Import Sale Cian Top 3',
            'import_rent_cian_top3' => 'Import Rent Cian Top 3',
            'import_sale_cian_hl' => 'Import Sale Cian Hl',
            'import_rent_cian_hl' => 'Import Rent Cian Hl',
            'fence' => 'Fence',
            'field_allow_usage' => 'Field Allow Usage',
            'land_category' => 'Land Category',
            'status' => 'Status',
            'status_reason' => 'Status Reason',
            'status_description' => 'Status Description',
            'own_type_land' => 'Own Type Land',
            'area_outside' => 'Area Outside',
            'description_complex' => 'Description Complex',
            'description_manual_use' => 'Description Manual Use',
            'gas_near' => 'Gas Near',
            'mkad_ttk_between' => 'Mkad Ttk Between',
            'empty_line' => 'Empty Line',
            'title_empty_main' => 'Title Empty Main',
            'title_empty_communications' => 'Title Empty Communications',
            'title_empty_security' => 'Title Empty Security',
            'title_empty_railway' => 'Title Empty Railway',
            'title_empty_infrastructure' => 'Title Empty Infrastructure',
            'landscape_type' => 'Landscape Type',
            'land_use_restrictions' => 'Land Use Restrictions',
            'cadastral_number_land' => 'Cadastral Number Land',
            'documents_old' => 'Documents Old',
            'test_only' => 'Test Only',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['photo'] = function ($fields) {
            return json_decode($fields['photo']);
        };
        $fields['calc_ceiling_height'] = function ($fields) {
            $maxes = [];
            foreach ($fields->objectFloors as $floor) {
                $maxes[] = $floor->ceiling_height_max;
            }
            $max = max($maxes);
            $min = min($maxes);
            return $this->calcMinMax($min, $max);
        };
        $fields['calc_gate_type'] = function ($fields) {
            $gates = [];
            foreach ($fields->objectFloors as $floor) {
                $floorGates = json_decode($floor->gates);
                if ($floorGates) $gates[] = $floorGates;
            }

            foreach ($gates as $floorGates) {
                if (ArrayHelper::keyExists($floorGates[0], ObjectFloors::GATE_TYPE_LIST)) {
                    return ObjectFloors::GATE_TYPE_LIST[$floorGates[0]];
                }
            }
            return null;
        };
        $fields['description'] = function ($fields) {
            $fuck =  HtmlPurifier::process($fields['description'], ['HTML.AllowedElements' => '']);
            $fuck = str_replace('\\', '', $fuck);
            $fuck = str_replace('??', '', $fuck);
            return $fuck;
        };
        $fields['description_auto'] = function ($fields) {
            $fuck =  HtmlPurifier::process($fields['description_auto'], ['HTML.AllowedElements' => '']);
            $fuck = str_replace('\\', '', $fuck);
            $fuck = str_replace('??', '', $fuck);
            return $fuck;
        };
        return $fields;
    }
    public function calcMinMax($min, $max)
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
    public function extraFields()
    {
        $extraFields = parent::extraFields();
        $extraFields['offerMix_formatted'] = function ($extraFields) {
            return $this->getFormattedOfferMix($extraFields);
        };
        return $extraFields;
    }
    public function getFormattedOfferMix($extraFields)
    {
        $offerMix = $extraFields->offerMix;
        return $offerMix[0];
    }

    public function getBlocks()
    {
        return $this->hasMany(ObjectsBlock::class, ['object_id' => 'id']);
    }
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }
    public function getOfferMix()
    {
        return $this->hasMany(OfferMix::class, ['object_id' => 'id']);
    }
    public function getObjectFloors()
    {
        return $this->hasMany(ObjectFloors::class, ['object_id' => 'id']);
    }
}
