<?php

namespace app\models\oldDb;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "c_industry_offers".
 *
 * @property int $id
 * @property string|null $title Название
 * @property int|null $company_id компания
 * @property string|null $company Имя компании
 * @property int|null $object_id строение
 * @property int|null $agent_id брокер
 * @property int|null $contact_id Контакт по предложению
 * @property int|null $agent_visited видел ли брокер данное предложение
 * @property int|null $deal_type вид сделки
 * @property int|null $status
 * @property int|null $status_id Статус/Результат
 * @property int|null $pledge Залог
 * @property int|null $deposit_value Величина депозита
 * @property int|null $deposit
 * @property int|null $holidays Есть ли каникулы
 * @property int|null $holidays_pay Оплата каникулами
 * @property int|null $holidays_value_min Минимальные каникулы
 * @property int|null $holidays_value_max Максимальные каникулы
 * @property int|null $pay_through_holidays
 * @property int|null $dont_pay
 * @property int|null $pay_guarantee
 * @property int|null $price_opex Цена операционных расходов
 * @property int|null $price_opex_min
 * @property int|null $price_opex_value
 * @property int|null $price_public_services
 * @property int|null $public_services
 * @property float|null $commission_owner_value
 * @property int|null $commission_owner
 * @property int|null $commission_owner_type
 * @property float|null $commission_client_value
 * @property int|null $commission_client
 * @property float|null $commission_agent_value
 * @property int|null $commission_agent
 * @property int|null $site_price_hide
 * @property int|null $ad_realtor
 * @property int|null $ad_realtor_top
 * @property string|null $description
 * @property string|null $description_auto
 * @property int|null $description_manual_use
 * @property int|null $description_complex
 * @property int|null $order_row
 * @property int|null $activity
 * @property int|null $publ_time
 * @property int|null $last_update
 * @property int|null $deleted
 * @property int|null $empty_line
 * @property string|null $inc_services
 * @property string|null $inc_opex
 * @property int|null $tax_form Система налогов
 * @property int|null $ad_free Реклама в бесплатные
 * @property int|null $ad_cian
 * @property int|null $ad_cian_top3
 * @property int|null $ad_cian_hl
 * @property int|null $ad_cian_premium
 * @property int|null $ad_arendator
 * @property int|null $ad_yandex
 * @property int|null $ad_yandex_raise
 * @property int|null $ad_yandex_promotion
 * @property int|null $ad_yandex_premium
 * @property int|null $demolition
 * @property string|null $status_reason
 * @property int|null $contract_is_signed
 * @property int|null $contract_is_signed_type
 * @property int|null $sale_object
 * @property int|null $sale_company
 * @property int|null $built_to_suit
 * @property int|null $built_to_suit_time
 * @property int|null $built_to_suit_plan
 * @property int|null $built_to_suit_group
 * @property int|null $rent_business
 * @property int|null $rent_business_fill процент заполнения обьекта
 * @property int|null $rent_business_price Средняя ставка аренды
 * @property int|null $rent_business_long_contracts процент долгих контрактов
 * @property int|null $rent_business_last_repair Год последнего ремонта
 * @property int|null $rent_business_payback Срок окупаемости
 * @property int|null $rent_business_income Доналоговая прибыль
 * @property int|null $rent_business_profit Чистая прибыль
 * @property int|null $safe_service_handling Погруз - разгруз работы
 * @property int|null $safe_service_cross_docking Кросс докинг
 * @property int|null $safe_service_packs_counting Прием и пеерсчет по коробам
 * @property int|null $safe_service_culling Выбраковка товара
 * @property int|null $safe_service_repacking Переупаковка товара
 * @property int|null $safe_service_palleting Формирование паллет
 * @property int|null $safe_service_winding Обмотка стретч пленкой
 * @property int|null $safe_service_accounting_batch Партионный учет
 * @property int|null $safe_service_accounting_serials Учет серийных номеров
 * @property int|null $safe_service_accounting_fifo Учет в разрезах FIFO
 * @property int|null $safe_service_selection Подбор заказов
 * @property int|null $safe_service_give_pallets Предоставление паллет
 * @property int|null $safe_service_complement Комплектация наборов
 * @property int|null $safe_service_stickers Стикеровка
 * @property int|null $safe_service_packing Упаковка товара
 * @property int|null $safe_service_co_packing Ко-пакинг
 * @property int|null $safe_service_documents Печать сопроводительных доков
 * @property int|null $safe_service_inventory Инвентаризация
 * @property int|null $safe_service_reports Предоставление отчетов
 * @property int|null $safe_service_recycling Утилизация брака
 * @property int|null $safe_service_managing_stocks Управление запасам
 * @property int|null $safe_service_managing_returns приемка возвратов
 * @property int|null $safe_service_repair Восстановление упаковки ремонт
 * @property int|null $safe_service_archive архивное хранение
 * @property int|null $safe_service_3pl 3PL услуги
 * @property int|null $safe_service_delivery_town Доставка по городу
 * @property int|null $safe_service_delivery_region Доставка по области
 * @property int|null $safe_service_delivery_russia Доставка по России
 * @property int|null $is_exclusive
 * @property int|null $ad_special
 * @property int|null $area_field_full
 * @property int|null $area_floor_full
 * @property int|null $area_mezzanine_full
 * @property int|null $area_office_full
 * @property int|null $offer_stats
 * @property string|null $title_empty_company
 * @property string|null $title_empty_agent
 * @property string|null $title_empty_commission
 * @property string|null $title_empty_commercial
 * @property string|null $title_empty_conditions
 * @property string|null $title_empty_financial
 * @property int|null $hide_from_market
 */
class Offers extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'c_industry_offers';
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
            [['company_id', 'object_id', 'agent_id', 'contact_id', 'agent_visited', 'deal_type', 'status', 'status_id', 'pledge', 'deposit_value', 'deposit', 'holidays', 'holidays_pay', 'holidays_value_min', 'holidays_value_max', 'pay_through_holidays', 'dont_pay', 'pay_guarantee', 'price_opex', 'price_opex_min', 'price_opex_value', 'price_public_services', 'public_services', 'commission_owner', 'commission_owner_type', 'commission_client', 'commission_agent', 'site_price_hide', 'ad_realtor', 'ad_realtor_top', 'description_manual_use', 'description_complex', 'order_row', 'activity', 'publ_time', 'last_update', 'deleted', 'empty_line', 'tax_form', 'ad_free', 'ad_cian', 'ad_cian_top3', 'ad_cian_hl', 'ad_cian_premium', 'ad_arendator', 'ad_yandex', 'ad_yandex_raise', 'ad_yandex_promotion', 'ad_yandex_premium', 'demolition', 'contract_is_signed', 'contract_is_signed_type', 'sale_object', 'sale_company', 'built_to_suit', 'built_to_suit_time', 'built_to_suit_plan', 'built_to_suit_group', 'rent_business', 'rent_business_fill', 'rent_business_price', 'rent_business_long_contracts', 'rent_business_last_repair', 'rent_business_payback', 'rent_business_income', 'rent_business_profit', 'safe_service_handling', 'safe_service_cross_docking', 'safe_service_packs_counting', 'safe_service_culling', 'safe_service_repacking', 'safe_service_palleting', 'safe_service_winding', 'safe_service_accounting_batch', 'safe_service_accounting_serials', 'safe_service_accounting_fifo', 'safe_service_selection', 'safe_service_give_pallets', 'safe_service_complement', 'safe_service_stickers', 'safe_service_packing', 'safe_service_co_packing', 'safe_service_documents', 'safe_service_inventory', 'safe_service_reports', 'safe_service_recycling', 'safe_service_managing_stocks', 'safe_service_managing_returns', 'safe_service_repair', 'safe_service_archive', 'safe_service_3pl', 'safe_service_delivery_town', 'safe_service_delivery_region', 'safe_service_delivery_russia', 'is_exclusive', 'ad_special', 'area_field_full', 'area_floor_full', 'area_mezzanine_full', 'area_office_full', 'offer_stats', 'hide_from_market'], 'integer'],
            [['commission_owner_value', 'commission_client_value', 'commission_agent_value'], 'number'],
            [['description', 'description_auto', 'inc_services', 'inc_opex', 'status_reason'], 'string'],
            [['title'], 'string', 'max' => 300],
            [['company'], 'string', 'max' => 200],
            [['title_empty_company', 'title_empty_agent', 'title_empty_commission', 'title_empty_commercial', 'title_empty_conditions', 'title_empty_financial'], 'string', 'max' => 100],
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
            'company_id' => 'Company ID',
            'company' => 'Company',
            'object_id' => 'Object ID',
            'agent_id' => 'Agent ID',
            'contact_id' => 'Contact ID',
            'agent_visited' => 'Agent Visited',
            'deal_type' => 'Deal Type',
            'status' => 'Status',
            'status_id' => 'Status ID',
            'pledge' => 'Pledge',
            'deposit_value' => 'Deposit Value',
            'deposit' => 'Deposit',
            'holidays' => 'Holidays',
            'holidays_pay' => 'Holidays Pay',
            'holidays_value_min' => 'Holidays Value Min',
            'holidays_value_max' => 'Holidays Value Max',
            'pay_through_holidays' => 'Pay Through Holidays',
            'dont_pay' => 'Dont Pay',
            'pay_guarantee' => 'Pay Guarantee',
            'price_opex' => 'Price Opex',
            'price_opex_min' => 'Price Opex Min',
            'price_opex_value' => 'Price Opex Value',
            'price_public_services' => 'Price Public Services',
            'public_services' => 'Public Services',
            'commission_owner_value' => 'Commission Owner Value',
            'commission_owner' => 'Commission Owner',
            'commission_owner_type' => 'Commission Owner Type',
            'commission_client_value' => 'Commission Client Value',
            'commission_client' => 'Commission Client',
            'commission_agent_value' => 'Commission Agent Value',
            'commission_agent' => 'Commission Agent',
            'site_price_hide' => 'Site Price Hide',
            'ad_realtor' => 'Ad Realtor',
            'ad_realtor_top' => 'Ad Realtor Top',
            'description' => 'Description',
            'description_auto' => 'Description Auto',
            'description_manual_use' => 'Description Manual Use',
            'description_complex' => 'Description Complex',
            'order_row' => 'Order Row',
            'activity' => 'Activity',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
            'deleted' => 'Deleted',
            'empty_line' => 'Empty Line',
            'inc_services' => 'Inc Services',
            'inc_opex' => 'Inc Opex',
            'tax_form' => 'Tax Form',
            'ad_free' => 'Ad Free',
            'ad_cian' => 'Ad Cian',
            'ad_cian_top3' => 'Ad Cian Top 3',
            'ad_cian_hl' => 'Ad Cian Hl',
            'ad_cian_premium' => 'Ad Cian Premium',
            'ad_arendator' => 'Ad Arendator',
            'ad_yandex' => 'Ad Yandex',
            'ad_yandex_raise' => 'Ad Yandex Raise',
            'ad_yandex_promotion' => 'Ad Yandex Promotion',
            'ad_yandex_premium' => 'Ad Yandex Premium',
            'demolition' => 'Demolition',
            'status_reason' => 'Status Reason',
            'contract_is_signed' => 'Contract Is Signed',
            'contract_is_signed_type' => 'Contract Is Signed Type',
            'sale_object' => 'Sale Object',
            'sale_company' => 'Sale Company',
            'built_to_suit' => 'Built To Suit',
            'built_to_suit_time' => 'Built To Suit Time',
            'built_to_suit_plan' => 'Built To Suit Plan',
            'built_to_suit_group' => 'Built To Suit Group',
            'rent_business' => 'Rent Business',
            'rent_business_fill' => 'Rent Business Fill',
            'rent_business_price' => 'Rent Business Price',
            'rent_business_long_contracts' => 'Rent Business Long Contracts',
            'rent_business_last_repair' => 'Rent Business Last Repair',
            'rent_business_payback' => 'Rent Business Payback',
            'rent_business_income' => 'Rent Business Income',
            'rent_business_profit' => 'Rent Business Profit',
            'safe_service_handling' => 'Safe Service Handling',
            'safe_service_cross_docking' => 'Safe Service Cross Docking',
            'safe_service_packs_counting' => 'Safe Service Packs Counting',
            'safe_service_culling' => 'Safe Service Culling',
            'safe_service_repacking' => 'Safe Service Repacking',
            'safe_service_palleting' => 'Safe Service Palleting',
            'safe_service_winding' => 'Safe Service Winding',
            'safe_service_accounting_batch' => 'Safe Service Accounting Batch',
            'safe_service_accounting_serials' => 'Safe Service Accounting Serials',
            'safe_service_accounting_fifo' => 'Safe Service Accounting Fifo',
            'safe_service_selection' => 'Safe Service Selection',
            'safe_service_give_pallets' => 'Safe Service Give Pallets',
            'safe_service_complement' => 'Safe Service Complement',
            'safe_service_stickers' => 'Safe Service Stickers',
            'safe_service_packing' => 'Safe Service Packing',
            'safe_service_co_packing' => 'Safe Service Co Packing',
            'safe_service_documents' => 'Safe Service Documents',
            'safe_service_inventory' => 'Safe Service Inventory',
            'safe_service_reports' => 'Safe Service Reports',
            'safe_service_recycling' => 'Safe Service Recycling',
            'safe_service_managing_stocks' => 'Safe Service Managing Stocks',
            'safe_service_managing_returns' => 'Safe Service Managing Returns',
            'safe_service_repair' => 'Safe Service Repair',
            'safe_service_archive' => 'Safe Service Archive',
            'safe_service_3pl' => 'Safe Service  3pl',
            'safe_service_delivery_town' => 'Safe Service Delivery Town',
            'safe_service_delivery_region' => 'Safe Service Delivery Region',
            'safe_service_delivery_russia' => 'Safe Service Delivery Russia',
            'is_exclusive' => 'Is Exclusive',
            'ad_special' => 'Ad Special',
            'area_field_full' => 'Area Field Full',
            'area_floor_full' => 'Area Floor Full',
            'area_mezzanine_full' => 'Area Mezzanine Full',
            'area_office_full' => 'Area Office Full',
            'offer_stats' => 'Offer Stats',
            'title_empty_company' => 'Title Empty Company',
            'title_empty_agent' => 'Title Empty Agent',
            'title_empty_commission' => 'Title Empty Commission',
            'title_empty_commercial' => 'Title Empty Commercial',
            'title_empty_conditions' => 'Title Empty Conditions',
            'title_empty_financial' => 'Title Empty Financial',
            'hide_from_market' => 'Hide From Market',
        ];
    }
}
