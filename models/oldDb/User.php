<?php

namespace app\models\oldDb;

use Yii;
use app\models\UserProfile;

/**
 * This is the model class for table "core_users".
 *
 * @property int $id
 * @property int $user_id_new
 * @property string $title
 * @property string $user_type
 * @property string $first_name
 * @property string $last_name
 * @property string $father_name
 * @property string $password
 * @property string $user_password
 * @property string $photo
 * @property string $photo_small
 * @property string $member_group_id
 * @property string $member_group_secondary
 * @property string $friends
 * @property string $subscribers
 * @property string $subscriptions
 * @property string $visitors
 * @property string $user_hash
 * @property string $reg_hash
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property int $law_entity
 * @property string $company
 * @property string $industry_type
 * @property string $site
 * @property string $ip_address
 * @property int $last_visit
 * @property int $publ_time
 * @property int $last_update
 * @property string $dt_insert
 * @property string $dt_update
 * @property string $dt_update_full
 * @property int $agent
 * @property int $deleted
 * @property int $result
 * @property int $reputation_points
 * @property int $order_count
 * @property int $order_success_count
 * @property string $discount
 * @property string $restore_hash
 * @property string $orders
 * @property string $tasks
 * @property string $description
 * @property int $order_row
 * @property int $activity
 * @property string $instagram
 * @property string $vk
 * @property string $telegram
 * @property string $facebook
 * @property string|null $favourites
 * @property string|null $presentations
 * @property string|null $category
 * @property string|null $avatar
 * @property string|null $cover_photo
 * @property int|null $telegram_id
 * @property string|null $phones
 * @property string|null $emails
 * @property int|null $last_check_tasks
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'core_users';
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
            [['title', 'user_type', 'first_name', 'last_name', 'father_name', 'password', 'user_password', 'photo', 'photo_small', 'member_group_id', 'member_group_secondary', 'friends', 'subscribers', 'subscriptions', 'visitors', 'user_hash', 'reg_hash', 'phone', 'email', 'address', 'company', 'industry_type', 'site', 'ip_address', 'last_visit', 'publ_time', 'last_update', 'dt_insert', 'dt_update', 'dt_update_full', 'agent', 'deleted', 'result', 'reputation_points', 'order_count', 'order_success_count', 'discount', 'restore_hash', 'orders', 'tasks', 'description', 'order_row', 'activity', 'instagram', 'vk', 'telegram', 'facebook', 'user_id_new'], 'required'],
            [['title', 'password', 'photo', 'photo_small', 'member_group_secondary', 'friends', 'subscribers', 'subscriptions', 'visitors', 'phone', 'email', 'discount', 'restore_hash', 'orders', 'tasks', 'description', 'instagram', 'vk', 'facebook', 'favourites', 'presentations', 'category', 'avatar', 'cover_photo', 'phones', 'emails'], 'string'],
            [['law_entity', 'last_visit', 'publ_time', 'last_update', 'agent', 'deleted', 'result', 'reputation_points', 'order_count', 'order_success_count', 'order_row', 'activity', 'telegram_id', 'last_check_tasks', 'user_id_new'], 'integer'],
            [['dt_insert', 'dt_update', 'dt_update_full'], 'safe'],
            [['user_type', 'father_name'], 'string', 'max' => 255],
            [['first_name'], 'string', 'max' => 100],
            [['last_name'], 'string', 'max' => 200],
            [['user_password', 'industry_type'], 'string', 'max' => 120],
            [['member_group_id'], 'string', 'max' => 512],
            [['user_hash', 'reg_hash', 'address', 'company', 'site'], 'string', 'max' => 256],
            [['ip_address'], 'string', 'max' => 50],
            [['telegram'], 'string', 'max' => 300],
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
            'user_type' => 'User Type',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'father_name' => 'Father Name',
            'password' => 'Password',
            'user_password' => 'User Password',
            'photo' => 'Photo',
            'photo_small' => 'Photo Small',
            'member_group_id' => 'Member Group ID',
            'member_group_secondary' => 'Member Group Secondary',
            'friends' => 'Friends',
            'subscribers' => 'Subscribers',
            'subscriptions' => 'Subscriptions',
            'visitors' => 'Visitors',
            'user_hash' => 'User Hash',
            'reg_hash' => 'Reg Hash',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Address',
            'law_entity' => 'Law Entity',
            'company' => 'Company',
            'industry_type' => 'Industry Type',
            'site' => 'Site',
            'ip_address' => 'Ip Address',
            'last_visit' => 'Last Visit',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
            'dt_insert' => 'Dt Insert',
            'dt_update' => 'Dt Update',
            'dt_update_full' => 'Dt Update Full',
            'agent' => 'Agent',
            'deleted' => 'Deleted',
            'result' => 'Result',
            'reputation_points' => 'Reputation Points',
            'order_count' => 'Order Count',
            'order_success_count' => 'Order Success Count',
            'discount' => 'Discount',
            'restore_hash' => 'Restore Hash',
            'orders' => 'Orders',
            'tasks' => 'Tasks',
            'description' => 'Description',
            'order_row' => 'Order Row',
            'activity' => 'Activity',
            'instagram' => 'Instagram',
            'vk' => 'Vk',
            'telegram' => 'Telegram',
            'facebook' => 'Facebook',
            'favourites' => 'Favourites',
            'presentations' => 'Presentations',
            'category' => 'Category',
            'avatar' => 'Avatar',
            'cover_photo' => 'Cover Photo',
            'telegram_id' => 'Telegram ID',
            'phones' => 'Phones',
            'emails' => 'Emails',
            'last_check_tasks' => 'Last Check Tasks',
        ];
    }

    /**
     * Gets query for [[UserProfiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'user_id_new']);
    }
}
