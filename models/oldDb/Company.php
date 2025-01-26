<?php

namespace app\models\oldDb;

/**
 * This is the model class for table "c_industry_companies".
 *
 * @property int $id
 * @property string $title
 * @property string $title_old
 * @property int $noname
 * @property int|null $company_type
 * @property int $company_activity
 * @property string $company_group
 * @property int|null $company_group_id
 * @property string|null $address
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $address_yandex
 * @property string|null $address_google
 * @property string|null $site_url
 * @property int $agent_id
 * @property int $contact_id
 * @property string $company_service
 * @property string|null $company_service_name
 * @property string|null $description
 * @property string|null $comment
 * @property int $rating
 * @property int $order_row
 * @property int $publ_time
 * @property int $last_update
 * @property int $deleted
 * @property int $activity
 * @property int|null $company_law_type
 * @property int|null $ready_for_safe
 * @property int|null $ready_for_buy
 * @property int|null $price_now
 * @property int|null $call_info_status
 * @property string|null $sites_urls
 * @property string|null $phones
 * @property int|null $good_relationship
 * @property int|null $status
 * @property int|null $status_reason
 * @property string|null $status_description
 * @property int|null $processed
 * @property string|null $title_eng
 * @property int|null $company_service_profile
 * @property string|null $company_service_nomenclature
 * @property string|null $emails
 * @property int|null $empty_line
 * @property string|null $law_address
 * @property string|null $law_ogrn
 * @property string|null $law_inn
 * @property string|null $law_kpp
 * @property string|null $law_account_checking
 * @property string|null $law_account_correspondent
 * @property string|null $law_bank
 * @property string|null $law_bik
 * @property string|null $law_code_okved
 * @property string|null $law_code_okpo
 * @property string|null $law_first_name
 * @property string|null $law_second_name
 * @property string|null $law_father_name
 * @property string|null $law_action
 * @property string|null $law_document_num
 * @property int|null $title_empty_old
 * @property string|null $phone
 * @property string|null $email
 * @property int|null $documents_old
 * @property string|null $documents
 */
class Company extends \app\kernel\common\models\AR\AR
{

    public static function tableName(): string
    {
        return 'c_industry_companies';
    }


    public static function getDb(): Connection
    {
        return Yii::$app->get('db_old');
    }

    public function rules(): array
    {
        return [
            [['title', 'title_old', 'noname', 'company_activity', 'company_group', 'agent_id', 'contact_id', 'company_service', 'rating', 'order_row', 'publ_time', 'last_update', 'deleted', 'activity'], 'required'],
            [['noname', 'company_type', 'company_activity', 'company_group_id', 'agent_id', 'contact_id', 'rating', 'order_row', 'publ_time', 'last_update', 'deleted', 'activity', 'company_law_type', 'ready_for_safe', 'ready_for_buy', 'price_now', 'call_info_status', 'good_relationship', 'status', 'status_reason', 'processed', 'company_service_profile', 'empty_line', 'title_empty_old', 'documents_old'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['sites_urls', 'phones', 'status_description', 'title_eng', 'emails', 'phone', 'email', 'documents'], 'string'],
            [['title', 'title_old'], 'string', 'max' => 100],
            [['company_group', 'site_url', 'company_service', 'company_service_name', 'law_address', 'law_ogrn', 'law_inn', 'law_kpp', 'law_account_checking', 'law_account_correspondent', 'law_bank', 'law_bik', 'law_code_okved', 'law_code_okpo', 'law_first_name', 'law_second_name', 'law_father_name', 'law_action', 'law_document_num'], 'string', 'max' => 200],
            [['address', 'address_yandex', 'address_google', 'description', 'comment'], 'string', 'max' => 500],
            [['company_service_nomenclature'], 'string', 'max' => 1000],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'title_old' => 'Title Old',
            'noname' => 'Noname',
            'company_type' => 'Company Type',
            'company_activity' => 'Company Activity',
            'company_group' => 'Company Group',
            'company_group_id' => 'Company Group ID',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'address_yandex' => 'Address Yandex',
            'address_google' => 'Address Google',
            'site_url' => 'Site Url',
            'agent_id' => 'Agent ID',
            'contact_id' => 'Contact ID',
            'company_service' => 'Company Service',
            'company_service_name' => 'Company Service Name',
            'description' => 'Description',
            'comment' => 'Comment',
            'rating' => 'Rating',
            'order_row' => 'Order Row',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
            'deleted' => 'Deleted',
            'activity' => 'Activity',
            'company_law_type' => 'Company Law Type',
            'ready_for_safe' => 'Ready For Safe',
            'ready_for_buy' => 'Ready For Buy',
            'price_now' => 'Price Now',
            'call_info_status' => 'Call Info Status',
            'sites_urls' => 'Sites Urls',
            'phones' => 'Phones',
            'good_relationship' => 'Good Relationship',
            'status' => 'Status',
            'status_reason' => 'Status Reason',
            'status_description' => 'Status Description',
            'processed' => 'Processed',
            'title_eng' => 'Title Eng',
            'company_service_profile' => 'Company Service Profile',
            'company_service_nomenclature' => 'Company Service Nomenclature',
            'emails' => 'Emails',
            'empty_line' => 'Empty Line',
            'law_address' => 'Law Address',
            'law_ogrn' => 'Law Ogrn',
            'law_inn' => 'Law Inn',
            'law_kpp' => 'Law Kpp',
            'law_account_checking' => 'Law Account Checking',
            'law_account_correspondent' => 'Law Account Correspondent',
            'law_bank' => 'Law Bank',
            'law_bik' => 'Law Bik',
            'law_code_okved' => 'Law Code Okved',
            'law_code_okpo' => 'Law Code Okpo',
            'law_first_name' => 'Law First Name',
            'law_second_name' => 'Law Second Name',
            'law_father_name' => 'Law Father Name',
            'law_action' => 'Law Action',
            'law_document_num' => 'Law Document Num',
            'title_empty_old' => 'Title Empty Old',
            'phone' => 'Phone',
            'email' => 'Email',
            'documents_old' => 'Documents Old',
            'documents' => 'Documents',
        ];
    }
}
