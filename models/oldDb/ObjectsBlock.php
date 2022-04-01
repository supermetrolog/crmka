<?php

namespace app\models\oldDb;

use Yii;

/**
 * This is the model class for table "c_industry_blocks".
 *
 * @property int $id
 * @property int|null $id_visual
 * @property int|null $object_id Номер здания
 * @property int|null $offer_id id предложения
 * @property int|null $deal_id
 * @property string|null $title Название
 * @property string|null $photo_block
 * @property string|null $building_layouts_block Планировки блока
 * @property string|null $photos
 * @property string|null $purposes_block
 * @property int|null $is_land
 * @property int|null $land
 * @property int|null $land_width
 * @property int|null $land_length
 * @property string|null $excluded_areas Информация о том какеи параметры исключены
 * @property int|null $is_solid Собран целиком
 * @property int|null $area_floor
 * @property int|null $area_floor_min
 * @property int|null $area_floor_max
 * @property int|null $area Площадь(вспомогательная)
 * @property int|null $area_min Площадь от
 * @property int|null $area_max 2 - Площадь до
 * @property int|null $area_warehouse Складская площадь
 * @property int|null $area_warehouse_min
 * @property int|null $area_warehouse_max
 * @property int|null $racks Есть ли стеллажи
 * @property int|null $rack_levels
 * @property int|null $pallet_place Палетт мест(вспомогательная)
 * @property int|null $pallet_place_min Палетт мест минимально
 * @property int|null $pallet_place_max Палетт мест максимально
 * @property int|null $cells_place
 * @property int|null $cells_place_min
 * @property int|null $cells_place_max
 * @property int|null $area_mezzanine 2 - Площадь мезонина
 * @property int|null $area_mezzanine_min
 * @property int|null $area_mezzanine_max
 * @property int|null $area_office 2 - Площадь офисов
 * @property int|null $area_office_min
 * @property int|null $area_office_max
 * @property int|null $area_tech
 * @property int|null $area_tech_min
 * @property int|null $area_tech_max
 * @property string|null $floor Этаж
 * @property int|null $floor_min
 * @property string|null $floor_types Тип пола
 * @property string|null $floor_types_land
 * @property int|null $floor_max
 * @property int|null $floor_id
 * @property int|null $ceiling_height высота потолков(вспомогательное)
 * @property float|null $ceiling_height_min Высота потолков от
 * @property float|null $ceiling_height_max 2 - Высота потолков до
 * @property float|null $floor_level уровень пола
 * @property int|null $temperature температурный режим
 * @property int|null $temperature_min температура мин
 * @property int|null $temperature_max температура макс
 * @property float|null $power эл-во на блок
 * @property int|null $climate_control
 * @property int|null $gas
 * @property int|null $steam
 * @property int|null $internet
 * @property int|null $phone_line
 * @property string|null $firefighting_type
 * @property int|null $smoke_exhaust
 * @property int|null $video_control
 * @property int|null $access_control
 * @property int|null $security_alert
 * @property int|null $fire_alert
 * @property string|null $inc_services Что включено в цену
 * @property int|null $public_service_price Цена коммунальных услуг
 * @property int|null $operating_price Цена эксплуатационных  расходов
 * @property int|null $price_sub_min
 * @property int|null $price_sub_max
 * @property int|null $price_sub_two_min
 * @property int|null $price_sub_two_max
 * @property int|null $price_sub_three_min
 * @property int|null $price_sub_three_max
 * @property int|null $price Стоимость (за кв.м. в год)
 * @property int|null $price_floor
 * @property int|null $price_floor_min
 * @property int|null $price_floor_max
 * @property int|null $price_floor_two_min
 * @property int|null $price_floor_two_max
 * @property int|null $price_floor_three_min
 * @property int|null $price_floor_three_max
 * @property int|null $price_floor_four_min
 * @property int|null $price_floor_four_max
 * @property int|null $price_floor_five_min
 * @property int|null $price_floor_five_max
 * @property int|null $price_floor_six_min
 * @property int|null $price_floor_six_max
 * @property int|null $rent_price Стоимость аренды
 * @property int|null $price_sale
 * @property int|null $price_sale_min
 * @property int|null $price_sale_max
 * @property int|null $price_safe_cell_small 30*40 ячейки
 * @property int|null $price_safe_cell_small_min
 * @property int|null $price_safe_cell_small_max
 * @property int|null $price_safe_cell_middle 60*40 ячейки
 * @property int|null $price_safe_cell_middle_min
 * @property int|null $price_safe_cell_middle_max
 * @property int|null $price_safe_cell_big 60*80 ячейки
 * @property int|null $price_safe_cell_big_min
 * @property int|null $price_safe_cell_big_max
 * @property int|null $price_mezzanine Стоимость мезонин
 * @property int|null $price_mezzanine_min
 * @property int|null $price_mezzanine_max
 * @property int|null $price_mezzanine_two_min
 * @property int|null $price_mezzanine_two_max
 * @property int|null $price_mezzanine_three_min
 * @property int|null $price_mezzanine_three_max
 * @property int|null $price_mezzanine_four_min
 * @property int|null $price_mezzanine_four_max
 * @property int|null $price_office Стоимость офисы
 * @property int|null $price_office_min
 * @property int|null $price_office_max
 * @property int|null $price_tech
 * @property int|null $price_tech_min
 * @property int|null $price_tech_max
 * @property int|null $price_field
 * @property int|null $price_field_min Цена уличн мин
 * @property int|null $price_field_max Цена уличн макс
 * @property string|null $description_auto Описание авто
 * @property string|null $description Описание 
 * @property int|null $description_manual_use Использовать ручное описание
 * @property int|null $description_complex
 * @property int|null $deleted Удален
 * @property string|null $photo_small Фотографии блоков
 * @property int|null $deal_type Тип сделки
 * @property int|null $heated Отапливаемый
 * @property int|null $water
 * @property int|null $sewage
 * @property string|null $lighting
 * @property string|null $ventilation
 * @property string|null $column_grids  Сетка колонн
 * @property float|null $load_floor Нагрузка на пол
 * @property float|null $load_floor_min
 * @property float|null $load_floor_max
 * @property float|null $load_mezzanine Нагрузка на мезонин
 * @property float|null $load_mezzanine_min
 * @property float|null $load_mezzanine_max
 * @property int|null $gates_number Количество ворот
 * @property int|null $gate_type Тип ворот
 * @property string|null $gates Ворота
 * @property int|null $warehouse_equipment
 * @property string|null $finishing Готов
 * @property int|null $import_cian Выгружать в ЦМАН
 * @property int|null $import_cian_hl Выгружать в ЦИАН с подсветкой
 * @property int|null $import_cian_top3 Выгружать в ЦИАН TOP3 
 * @property int|null $import_cian_premium Выгружать в ЦИАН премиум
 * @property int|null $import_yandex
 * @property int|null $import_free
 * @property string|null $telphers Тельферы
 * @property string|null $cranes
 * @property int|null $cranes_num
 * @property float|null $cranes_min
 * @property float|null $cranes_max
 * @property string|null $cranes_cathead Кран балки
 * @property string|null $cranes_overhead Мостовые краны
 * @property string|null $elevators Грузовые лифты
 * @property int|null $elevators_num
 * @property int|null $elevators_min
 * @property int|null $elevators_max
 * @property int|null $result Результат
 * @property string|null $payinc
 * @property int|null $onsite_noprice
 * @property int|null $publ_time
 * @property int|null $last_update
 * @property int|null $order_row
 * @property int|null $activity
 * @property int|null $status_id
 * @property string|null $photo
 * @property int|null $ad_realtor Разливать на сайт
 * @property int|null $ad_cian
 * @property int|null $ad_cian_top3
 * @property int|null $ad_cian_premium
 * @property int|null $ad_cian_hl
 * @property int|null $ad_yandex
 * @property int|null $ad_yandex_raise
 * @property int|null $ad_yandex_promotion
 * @property int|null $ad_yandex_premium
 * @property int|null $ad_arendator
 * @property int|null $ad_free реклама в бесплатных источниках
 * @property int|null $charging_room
 * @property int|null $cranes_runways
 * @property int|null $cross_docking
 * @property int|null $status
 * @property int|null $status_reason
 * @property string|null $status_description
 * @property int|null $available_from
 * @property string|null $empty_line
 * @property string|null $empty_title_underline_mech
 * @property string|null $empty_title_underline_manual
 * @property string|null $empty_title_underline_complement
 * @property int|null $title_empty_main
 * @property int|null $title_empty_price_rent
 * @property int|null $title_empty_price_sale
 * @property int|null $title_empty_price_safe
 * @property int|null $title_empty_price_safe_in Приемка
 * @property int|null $title_empty_price_safe_out Отгрузка
 * @property int|null $title_empty_price_safe_extra Доп услуги
 * @property int|null $price_safe_volume
 * @property int|null $price_safe_volume_min
 * @property int|null $price_safe_volume_max
 * @property int|null $price_safe_floor
 * @property int|null $price_safe_floor_min
 * @property int|null $price_safe_floor_max
 * @property int|null $price_safe_pallet_eu
 * @property int|null $price_safe_pallet_eu_in Приемка
 * @property int|null $price_safe_pallet_eu_out Отгрузка
 * @property int|null $price_safe_pallet_eu_min
 * @property int|null $price_safe_pallet_eu_max
 * @property int|null $price_safe_pallet_fin
 * @property int|null $price_safe_pallet_fin_in Приемка
 * @property int|null $price_safe_pallet_fin_out Отгрузка
 * @property int|null $price_safe_pallet_fin_min
 * @property int|null $price_safe_pallet_fin_max
 * @property int|null $price_safe_pallet_us
 * @property int|null $price_safe_pallet_us_in Приемка
 * @property int|null $price_safe_pallet_us_out Отгрузка
 * @property int|null $price_safe_pallet_us_min
 * @property int|null $price_safe_pallet_us_max
 * @property int|null $price_safe_pallet_oversized
 * @property int|null $price_safe_pallet_oversized_in Приемка
 * @property int|null $price_safe_pallet_oversized_out Отгрузка
 * @property int|null $price_safe_pallet_oversized_min
 * @property int|null $price_safe_pallet_oversized_max
 * @property int|null $price_safe_pallet_oversized_middle_in
 * @property int|null $price_safe_pallet_oversized_middle_out
 * @property int|null $price_safe_pallet_oversized_big_in
 * @property int|null $price_safe_pallet_oversized_big_out
 * @property int|null $price_safe_pack_small_in
 * @property int|null $price_safe_pack_small_out
 * @property int|null $price_safe_pack_middle_in
 * @property int|null $price_safe_pack_middle_out
 * @property int|null $price_safe_pack_big_in
 * @property int|null $price_safe_pack_big_out
 * @property int|null $price_safe_pack_small_complement
 * @property int|null $price_safe_pack_middle_complement
 * @property int|null $price_safe_pack_big_complement
 * @property int|null $price_safe_service_inventory Инвентаризация
 * @property int|null $price_safe_service_winding Обмотка
 * @property int|null $price_safe_service_document Спороводительные доки
 * @property int|null $price_safe_service_report Отчеты
 * @property int|null $price_safe_service_pallet Предоставление поддонов
 * @property int|null $price_safe_service_stickers Стикеровка
 * @property int|null $price_safe_service_packing_pallet Формирование паллет
 * @property int|null $price_safe_service_packing_pack Формирование коробов
 * @property int|null $price_safe_service_recycling Утилизация мусора
 * @property int|null $price_safe_service_sealing Опломбирование авто
 * @property string|null $photos_360_block
 * @property string|null $building_presentations_block
 * @property int|null $landscape_type
 * @property int|null $area_mezzanine_add
 * @property int|null $area_office_add
 * @property int|null $area_tech_add
 * @property string|null $rack_types
 * @property string|null $safe_type
 * @property int|null $cells
 * @property int|null $enterance_block
 * @property int|null $area_field
 * @property int|null $area_field_min
 * @property int|null $area_field_max
 * @property int|null $ad_special
 * @property int|null $is_fake
 * @property string|null $parts
 * @property int|null $stack_strict
 * @property int|null $partition_area
 * @property string|null $price_multi
 * @property int|null $prices
 * @property int|null $offer_stats
 * @property int|null $offer_blocks
 * @property int|null $entire_only
 * @property int|null $fence
 * @property int|null $barrier
 */
class ObjectsBlock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'c_industry_blocks';
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
            [['id_visual', 'object_id', 'offer_id', 'deal_id', 'is_land', 'land', 'land_width', 'land_length', 'is_solid', 'area_floor', 'area_floor_min', 'area_floor_max', 'area', 'area_min', 'area_max', 'area_warehouse', 'area_warehouse_min', 'area_warehouse_max', 'racks', 'rack_levels', 'pallet_place', 'pallet_place_min', 'pallet_place_max', 'cells_place', 'cells_place_min', 'cells_place_max', 'area_mezzanine', 'area_mezzanine_min', 'area_mezzanine_max', 'area_office', 'area_office_min', 'area_office_max', 'area_tech', 'area_tech_min', 'area_tech_max', 'floor_min', 'floor_max', 'floor_id', 'ceiling_height', 'temperature', 'temperature_min', 'temperature_max', 'climate_control', 'gas', 'steam', 'internet', 'phone_line', 'smoke_exhaust', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'public_service_price', 'operating_price', 'price_sub_min', 'price_sub_max', 'price_sub_two_min', 'price_sub_two_max', 'price_sub_three_min', 'price_sub_three_max', 'price', 'price_floor', 'price_floor_min', 'price_floor_max', 'price_floor_two_min', 'price_floor_two_max', 'price_floor_three_min', 'price_floor_three_max', 'price_floor_four_min', 'price_floor_four_max', 'price_floor_five_min', 'price_floor_five_max', 'price_floor_six_min', 'price_floor_six_max', 'rent_price', 'price_sale', 'price_sale_min', 'price_sale_max', 'price_safe_cell_small', 'price_safe_cell_small_min', 'price_safe_cell_small_max', 'price_safe_cell_middle', 'price_safe_cell_middle_min', 'price_safe_cell_middle_max', 'price_safe_cell_big', 'price_safe_cell_big_min', 'price_safe_cell_big_max', 'price_mezzanine', 'price_mezzanine_min', 'price_mezzanine_max', 'price_mezzanine_two_min', 'price_mezzanine_two_max', 'price_mezzanine_three_min', 'price_mezzanine_three_max', 'price_mezzanine_four_min', 'price_mezzanine_four_max', 'price_office', 'price_office_min', 'price_office_max', 'price_tech', 'price_tech_min', 'price_tech_max', 'price_field', 'price_field_min', 'price_field_max', 'description_manual_use', 'description_complex', 'deleted', 'deal_type', 'heated', 'water', 'sewage', 'gates_number', 'gate_type', 'warehouse_equipment', 'import_cian', 'import_cian_hl', 'import_cian_top3', 'import_cian_premium', 'import_yandex', 'import_free', 'cranes_num', 'elevators_num', 'elevators_min', 'elevators_max', 'result', 'onsite_noprice', 'publ_time', 'last_update', 'order_row', 'activity', 'status_id', 'ad_realtor', 'ad_cian', 'ad_cian_top3', 'ad_cian_premium', 'ad_cian_hl', 'ad_yandex', 'ad_yandex_raise', 'ad_yandex_promotion', 'ad_yandex_premium', 'ad_arendator', 'ad_free', 'charging_room', 'cranes_runways', 'cross_docking', 'status', 'status_reason', 'available_from', 'title_empty_main', 'title_empty_price_rent', 'title_empty_price_sale', 'title_empty_price_safe', 'title_empty_price_safe_in', 'title_empty_price_safe_out', 'title_empty_price_safe_extra', 'price_safe_volume', 'price_safe_volume_min', 'price_safe_volume_max', 'price_safe_floor', 'price_safe_floor_min', 'price_safe_floor_max', 'price_safe_pallet_eu', 'price_safe_pallet_eu_in', 'price_safe_pallet_eu_out', 'price_safe_pallet_eu_min', 'price_safe_pallet_eu_max', 'price_safe_pallet_fin', 'price_safe_pallet_fin_in', 'price_safe_pallet_fin_out', 'price_safe_pallet_fin_min', 'price_safe_pallet_fin_max', 'price_safe_pallet_us', 'price_safe_pallet_us_in', 'price_safe_pallet_us_out', 'price_safe_pallet_us_min', 'price_safe_pallet_us_max', 'price_safe_pallet_oversized', 'price_safe_pallet_oversized_in', 'price_safe_pallet_oversized_out', 'price_safe_pallet_oversized_min', 'price_safe_pallet_oversized_max', 'price_safe_pallet_oversized_middle_in', 'price_safe_pallet_oversized_middle_out', 'price_safe_pallet_oversized_big_in', 'price_safe_pallet_oversized_big_out', 'price_safe_pack_small_in', 'price_safe_pack_small_out', 'price_safe_pack_middle_in', 'price_safe_pack_middle_out', 'price_safe_pack_big_in', 'price_safe_pack_big_out', 'price_safe_pack_small_complement', 'price_safe_pack_middle_complement', 'price_safe_pack_big_complement', 'price_safe_service_inventory', 'price_safe_service_winding', 'price_safe_service_document', 'price_safe_service_report', 'price_safe_service_pallet', 'price_safe_service_stickers', 'price_safe_service_packing_pallet', 'price_safe_service_packing_pack', 'price_safe_service_recycling', 'price_safe_service_sealing', 'landscape_type', 'area_mezzanine_add', 'area_office_add', 'area_tech_add', 'cells', 'enterance_block', 'area_field', 'area_field_min', 'area_field_max', 'ad_special', 'is_fake', 'stack_strict', 'partition_area', 'prices', 'offer_stats', 'offer_blocks', 'entire_only', 'fence', 'barrier'], 'integer'],
            [['photo_block', 'photos', 'description_auto', 'description', 'photo_small', 'payinc', 'photo', 'empty_line', 'empty_title_underline_mech', 'empty_title_underline_manual', 'empty_title_underline_complement', 'photos_360_block', 'building_presentations_block', 'rack_types', 'price_multi'], 'string'],
            [['ceiling_height_min', 'ceiling_height_max', 'floor_level', 'power', 'load_floor', 'load_floor_min', 'load_floor_max', 'load_mezzanine', 'load_mezzanine_min', 'load_mezzanine_max', 'cranes_min', 'cranes_max'], 'number'],
            [['title', 'purposes_block', 'column_grids', 'telphers', 'cranes'], 'string', 'max' => 200],
            [['building_layouts_block', 'gates', 'elevators', 'parts'], 'string', 'max' => 500],
            [['excluded_areas'], 'string', 'max' => 1000],
            [['floor'], 'string', 'max' => 50],
            [['floor_types', 'floor_types_land', 'firefighting_type', 'inc_services', 'lighting', 'ventilation'], 'string', 'max' => 100],
            [['finishing'], 'string', 'max' => 10],
            [['cranes_cathead', 'cranes_overhead', 'status_description'], 'string', 'max' => 300],
            [['safe_type'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_visual' => 'Id Visual',
            'object_id' => 'Object ID',
            'offer_id' => 'Offer ID',
            'deal_id' => 'Deal ID',
            'title' => 'Title',
            'photo_block' => 'Photo Block',
            'building_layouts_block' => 'Building Layouts Block',
            'photos' => 'Photos',
            'purposes_block' => 'Purposes Block',
            'is_land' => 'Is Land',
            'land' => 'Land',
            'land_width' => 'Land Width',
            'land_length' => 'Land Length',
            'excluded_areas' => 'Excluded Areas',
            'is_solid' => 'Is Solid',
            'area_floor' => 'Area Floor',
            'area_floor_min' => 'Area Floor Min',
            'area_floor_max' => 'Area Floor Max',
            'area' => 'Area',
            'area_min' => 'Area Min',
            'area_max' => 'Area Max',
            'area_warehouse' => 'Area Warehouse',
            'area_warehouse_min' => 'Area Warehouse Min',
            'area_warehouse_max' => 'Area Warehouse Max',
            'racks' => 'Racks',
            'rack_levels' => 'Rack Levels',
            'pallet_place' => 'Pallet Place',
            'pallet_place_min' => 'Pallet Place Min',
            'pallet_place_max' => 'Pallet Place Max',
            'cells_place' => 'Cells Place',
            'cells_place_min' => 'Cells Place Min',
            'cells_place_max' => 'Cells Place Max',
            'area_mezzanine' => 'Area Mezzanine',
            'area_mezzanine_min' => 'Area Mezzanine Min',
            'area_mezzanine_max' => 'Area Mezzanine Max',
            'area_office' => 'Area Office',
            'area_office_min' => 'Area Office Min',
            'area_office_max' => 'Area Office Max',
            'area_tech' => 'Area Tech',
            'area_tech_min' => 'Area Tech Min',
            'area_tech_max' => 'Area Tech Max',
            'floor' => 'Floor',
            'floor_min' => 'Floor Min',
            'floor_types' => 'Floor Types',
            'floor_types_land' => 'Floor Types Land',
            'floor_max' => 'Floor Max',
            'floor_id' => 'Floor ID',
            'ceiling_height' => 'Ceiling Height',
            'ceiling_height_min' => 'Ceiling Height Min',
            'ceiling_height_max' => 'Ceiling Height Max',
            'floor_level' => 'Floor Level',
            'temperature' => 'Temperature',
            'temperature_min' => 'Temperature Min',
            'temperature_max' => 'Temperature Max',
            'power' => 'Power',
            'climate_control' => 'Climate Control',
            'gas' => 'Gas',
            'steam' => 'Steam',
            'internet' => 'Internet',
            'phone_line' => 'Phone Line',
            'firefighting_type' => 'Firefighting Type',
            'smoke_exhaust' => 'Smoke Exhaust',
            'video_control' => 'Video Control',
            'access_control' => 'Access Control',
            'security_alert' => 'Security Alert',
            'fire_alert' => 'Fire Alert',
            'inc_services' => 'Inc Services',
            'public_service_price' => 'Public Service Price',
            'operating_price' => 'Operating Price',
            'price_sub_min' => 'Price Sub Min',
            'price_sub_max' => 'Price Sub Max',
            'price_sub_two_min' => 'Price Sub Two Min',
            'price_sub_two_max' => 'Price Sub Two Max',
            'price_sub_three_min' => 'Price Sub Three Min',
            'price_sub_three_max' => 'Price Sub Three Max',
            'price' => 'Price',
            'price_floor' => 'Price Floor',
            'price_floor_min' => 'Price Floor Min',
            'price_floor_max' => 'Price Floor Max',
            'price_floor_two_min' => 'Price Floor Two Min',
            'price_floor_two_max' => 'Price Floor Two Max',
            'price_floor_three_min' => 'Price Floor Three Min',
            'price_floor_three_max' => 'Price Floor Three Max',
            'price_floor_four_min' => 'Price Floor Four Min',
            'price_floor_four_max' => 'Price Floor Four Max',
            'price_floor_five_min' => 'Price Floor Five Min',
            'price_floor_five_max' => 'Price Floor Five Max',
            'price_floor_six_min' => 'Price Floor Six Min',
            'price_floor_six_max' => 'Price Floor Six Max',
            'rent_price' => 'Rent Price',
            'price_sale' => 'Price Sale',
            'price_sale_min' => 'Price Sale Min',
            'price_sale_max' => 'Price Sale Max',
            'price_safe_cell_small' => 'Price Safe Cell Small',
            'price_safe_cell_small_min' => 'Price Safe Cell Small Min',
            'price_safe_cell_small_max' => 'Price Safe Cell Small Max',
            'price_safe_cell_middle' => 'Price Safe Cell Middle',
            'price_safe_cell_middle_min' => 'Price Safe Cell Middle Min',
            'price_safe_cell_middle_max' => 'Price Safe Cell Middle Max',
            'price_safe_cell_big' => 'Price Safe Cell Big',
            'price_safe_cell_big_min' => 'Price Safe Cell Big Min',
            'price_safe_cell_big_max' => 'Price Safe Cell Big Max',
            'price_mezzanine' => 'Price Mezzanine',
            'price_mezzanine_min' => 'Price Mezzanine Min',
            'price_mezzanine_max' => 'Price Mezzanine Max',
            'price_mezzanine_two_min' => 'Price Mezzanine Two Min',
            'price_mezzanine_two_max' => 'Price Mezzanine Two Max',
            'price_mezzanine_three_min' => 'Price Mezzanine Three Min',
            'price_mezzanine_three_max' => 'Price Mezzanine Three Max',
            'price_mezzanine_four_min' => 'Price Mezzanine Four Min',
            'price_mezzanine_four_max' => 'Price Mezzanine Four Max',
            'price_office' => 'Price Office',
            'price_office_min' => 'Price Office Min',
            'price_office_max' => 'Price Office Max',
            'price_tech' => 'Price Tech',
            'price_tech_min' => 'Price Tech Min',
            'price_tech_max' => 'Price Tech Max',
            'price_field' => 'Price Field',
            'price_field_min' => 'Price Field Min',
            'price_field_max' => 'Price Field Max',
            'description_auto' => 'Description Auto',
            'description' => 'Description',
            'description_manual_use' => 'Description Manual Use',
            'description_complex' => 'Description Complex',
            'deleted' => 'Deleted',
            'photo_small' => 'Photo Small',
            'deal_type' => 'Deal Type',
            'heated' => 'Heated',
            'water' => 'Water',
            'sewage' => 'Sewage',
            'lighting' => 'Lighting',
            'ventilation' => 'Ventilation',
            'column_grids' => 'Column Grids',
            'load_floor' => 'Load Floor',
            'load_floor_min' => 'Load Floor Min',
            'load_floor_max' => 'Load Floor Max',
            'load_mezzanine' => 'Load Mezzanine',
            'load_mezzanine_min' => 'Load Mezzanine Min',
            'load_mezzanine_max' => 'Load Mezzanine Max',
            'gates_number' => 'Gates Number',
            'gate_type' => 'Gate Type',
            'gates' => 'Gates',
            'warehouse_equipment' => 'Warehouse Equipment',
            'finishing' => 'Finishing',
            'import_cian' => 'Import Cian',
            'import_cian_hl' => 'Import Cian Hl',
            'import_cian_top3' => 'Import Cian Top 3',
            'import_cian_premium' => 'Import Cian Premium',
            'import_yandex' => 'Import Yandex',
            'import_free' => 'Import Free',
            'telphers' => 'Telphers',
            'cranes' => 'Cranes',
            'cranes_num' => 'Cranes Num',
            'cranes_min' => 'Cranes Min',
            'cranes_max' => 'Cranes Max',
            'cranes_cathead' => 'Cranes Cathead',
            'cranes_overhead' => 'Cranes Overhead',
            'elevators' => 'Elevators',
            'elevators_num' => 'Elevators Num',
            'elevators_min' => 'Elevators Min',
            'elevators_max' => 'Elevators Max',
            'result' => 'Result',
            'payinc' => 'Payinc',
            'onsite_noprice' => 'Onsite Noprice',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
            'order_row' => 'Order Row',
            'activity' => 'Activity',
            'status_id' => 'Status ID',
            'photo' => 'Photo',
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
            'charging_room' => 'Charging Room',
            'cranes_runways' => 'Cranes Runways',
            'cross_docking' => 'Cross Docking',
            'status' => 'Status',
            'status_reason' => 'Status Reason',
            'status_description' => 'Status Description',
            'available_from' => 'Available From',
            'empty_line' => 'Empty Line',
            'empty_title_underline_mech' => 'Empty Title Underline Mech',
            'empty_title_underline_manual' => 'Empty Title Underline Manual',
            'empty_title_underline_complement' => 'Empty Title Underline Complement',
            'title_empty_main' => 'Title Empty Main',
            'title_empty_price_rent' => 'Title Empty Price Rent',
            'title_empty_price_sale' => 'Title Empty Price Sale',
            'title_empty_price_safe' => 'Title Empty Price Safe',
            'title_empty_price_safe_in' => 'Title Empty Price Safe In',
            'title_empty_price_safe_out' => 'Title Empty Price Safe Out',
            'title_empty_price_safe_extra' => 'Title Empty Price Safe Extra',
            'price_safe_volume' => 'Price Safe Volume',
            'price_safe_volume_min' => 'Price Safe Volume Min',
            'price_safe_volume_max' => 'Price Safe Volume Max',
            'price_safe_floor' => 'Price Safe Floor',
            'price_safe_floor_min' => 'Price Safe Floor Min',
            'price_safe_floor_max' => 'Price Safe Floor Max',
            'price_safe_pallet_eu' => 'Price Safe Pallet Eu',
            'price_safe_pallet_eu_in' => 'Price Safe Pallet Eu In',
            'price_safe_pallet_eu_out' => 'Price Safe Pallet Eu Out',
            'price_safe_pallet_eu_min' => 'Price Safe Pallet Eu Min',
            'price_safe_pallet_eu_max' => 'Price Safe Pallet Eu Max',
            'price_safe_pallet_fin' => 'Price Safe Pallet Fin',
            'price_safe_pallet_fin_in' => 'Price Safe Pallet Fin In',
            'price_safe_pallet_fin_out' => 'Price Safe Pallet Fin Out',
            'price_safe_pallet_fin_min' => 'Price Safe Pallet Fin Min',
            'price_safe_pallet_fin_max' => 'Price Safe Pallet Fin Max',
            'price_safe_pallet_us' => 'Price Safe Pallet Us',
            'price_safe_pallet_us_in' => 'Price Safe Pallet Us In',
            'price_safe_pallet_us_out' => 'Price Safe Pallet Us Out',
            'price_safe_pallet_us_min' => 'Price Safe Pallet Us Min',
            'price_safe_pallet_us_max' => 'Price Safe Pallet Us Max',
            'price_safe_pallet_oversized' => 'Price Safe Pallet Oversized',
            'price_safe_pallet_oversized_in' => 'Price Safe Pallet Oversized In',
            'price_safe_pallet_oversized_out' => 'Price Safe Pallet Oversized Out',
            'price_safe_pallet_oversized_min' => 'Price Safe Pallet Oversized Min',
            'price_safe_pallet_oversized_max' => 'Price Safe Pallet Oversized Max',
            'price_safe_pallet_oversized_middle_in' => 'Price Safe Pallet Oversized Middle In',
            'price_safe_pallet_oversized_middle_out' => 'Price Safe Pallet Oversized Middle Out',
            'price_safe_pallet_oversized_big_in' => 'Price Safe Pallet Oversized Big In',
            'price_safe_pallet_oversized_big_out' => 'Price Safe Pallet Oversized Big Out',
            'price_safe_pack_small_in' => 'Price Safe Pack Small In',
            'price_safe_pack_small_out' => 'Price Safe Pack Small Out',
            'price_safe_pack_middle_in' => 'Price Safe Pack Middle In',
            'price_safe_pack_middle_out' => 'Price Safe Pack Middle Out',
            'price_safe_pack_big_in' => 'Price Safe Pack Big In',
            'price_safe_pack_big_out' => 'Price Safe Pack Big Out',
            'price_safe_pack_small_complement' => 'Price Safe Pack Small Complement',
            'price_safe_pack_middle_complement' => 'Price Safe Pack Middle Complement',
            'price_safe_pack_big_complement' => 'Price Safe Pack Big Complement',
            'price_safe_service_inventory' => 'Price Safe Service Inventory',
            'price_safe_service_winding' => 'Price Safe Service Winding',
            'price_safe_service_document' => 'Price Safe Service Document',
            'price_safe_service_report' => 'Price Safe Service Report',
            'price_safe_service_pallet' => 'Price Safe Service Pallet',
            'price_safe_service_stickers' => 'Price Safe Service Stickers',
            'price_safe_service_packing_pallet' => 'Price Safe Service Packing Pallet',
            'price_safe_service_packing_pack' => 'Price Safe Service Packing Pack',
            'price_safe_service_recycling' => 'Price Safe Service Recycling',
            'price_safe_service_sealing' => 'Price Safe Service Sealing',
            'photos_360_block' => 'Photos  360 Block',
            'building_presentations_block' => 'Building Presentations Block',
            'landscape_type' => 'Landscape Type',
            'area_mezzanine_add' => 'Area Mezzanine Add',
            'area_office_add' => 'Area Office Add',
            'area_tech_add' => 'Area Tech Add',
            'rack_types' => 'Rack Types',
            'safe_type' => 'Safe Type',
            'cells' => 'Cells',
            'enterance_block' => 'Enterance Block',
            'area_field' => 'Area Field',
            'area_field_min' => 'Area Field Min',
            'area_field_max' => 'Area Field Max',
            'ad_special' => 'Ad Special',
            'is_fake' => 'Is Fake',
            'parts' => 'Parts',
            'stack_strict' => 'Stack Strict',
            'partition_area' => 'Partition Area',
            'price_multi' => 'Price Multi',
            'prices' => 'Prices',
            'offer_stats' => 'Offer Stats',
            'offer_blocks' => 'Offer Blocks',
            'entire_only' => 'Entire Only',
            'fence' => 'Fence',
            'barrier' => 'Barrier',
        ];
    }
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['id' => 'object_id']);
    }
}
