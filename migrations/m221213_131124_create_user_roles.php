<?php

use app\models\User;
use app\models\user\auth\Rbac;
use yii\db\Migration;

/**
 * Class m221213_131124_create_user_roles
 */
class m221213_131124_create_user_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->getAuthManager();

        // ********** CREATE PERMISSIONS ***********

        // Contact
        $deleteContact = $auth->createPermission(Rbac::PERMISSION_CONTACT_DELETE);
        $auth->add($deleteContact);
        $createContact = $auth->createPermission(Rbac::PERMISSION_CONTACT_CREATE);
        $auth->add($createContact);
        $updateContact = $auth->createPermission(Rbac::PERMISSION_CONTACT_UPDATE);
        $auth->add($updateContact);
        $viewContact = $auth->createPermission(Rbac::PERMISSION_CONTACT_VIEW);
        $auth->add($viewContact);
        $viewContacts = $auth->createPermission(Rbac::PERMISSION_CONTACTS_VIEW);
        $auth->add($viewContacts);
        $createContactComment = $auth->createPermission(Rbac::PERMISSION_CONTACTS_CREATE_COMMENT);
        $auth->add($createContactComment);

        // User
        $createUser = $auth->createPermission(Rbac::PERMISSION_USER_CREATE);
        $auth->add($createUser);
        $updateUser = $auth->createPermission(Rbac::PERMISSION_USER_UPDATE);
        $auth->add($updateUser);
        $disableUser = $auth->createPermission(Rbac::PERMISSION_USER_DISABLE);
        $auth->add($disableUser);
        $viewUser = $auth->createPermission(Rbac::PERMISSION_USER_VIEW);
        $auth->add($viewUser);
        $viewUsers = $auth->createPermission(Rbac::PERMISSION_USERS_VIEW);
        $auth->add($viewUsers);

        // Company
        $createCompany = $auth->createPermission(Rbac::PERMISSION_COMPANY_CREATE);
        $auth->add($createCompany);
        $updateCompany = $auth->createPermission(Rbac::PERMISSION_COMPANY_UPDATE);
        $auth->add($updateCompany);
        $viewCompany = $auth->createPermission(Rbac::PERMISSION_COMPANY_VIEW);
        $auth->add($viewCompany);
        $viewCompanies = $auth->createPermission(Rbac::PERMISSION_COMPANIES_VIEW);
        $auth->add($viewCompanies);

        // Company group
        $createCompanyGroup = $auth->createPermission(Rbac::PERMISSION_COMPANY_GROUP_CREATE);
        $auth->add($createCompanyGroup);
        $updateCompanyGroup = $auth->createPermission(Rbac::PERMISSION_COMPANY_GROUP_UPDATE);
        $auth->add($updateCompanyGroup);
        $viewCompanyGroup = $auth->createPermission(Rbac::PERMISSION_COMPANY_GROUP_VIEW);
        $auth->add($viewCompanyGroup);
        $viewCompanyGroups = $auth->createPermission(Rbac::PERMISSION_COMPANY_GROUPS_VIEW);
        $auth->add($viewCompanyGroups);

        // Deal
        $createDeal = $auth->createPermission(Rbac::PERMISSION_DEAL_CREATE);
        $auth->add($createDeal);
        $updateDeal = $auth->createPermission(Rbac::PERMISSION_DEAL_UPDATE);
        $auth->add($updateDeal);
        $disableDeal = $auth->createPermission(Rbac::PERMISSION_DEAL_DISABLE);
        $auth->add($disableDeal);
        $viewDeal = $auth->createPermission(Rbac::PERMISSION_DEAL_VIEW);
        $auth->add($viewDeal);
        $viewDeals = $auth->createPermission(Rbac::PERMISSION_DEALS_VIEW);
        $auth->add($viewDeals);

        // Letter
        $createLetter = $auth->createPermission(Rbac::PERMISSION_LETTER_CREATE);
        $auth->add($createLetter);
        $updateLetter = $auth->createPermission(Rbac::PERMISSION_LETTER_UPDATE);
        $auth->add($updateLetter);
        $deleteLetter = $auth->createPermission(Rbac::PERMISSION_LETTER_DELETE);
        $auth->add($deleteLetter);
        $viewLetter = $auth->createPermission(Rbac::PERMISSION_LETTER_VIEW);
        $auth->add($viewLetter);
        $viewLetters = $auth->createPermission(Rbac::PERMISSION_LETTERS_VIEW);
        $auth->add($viewLetters);

        // Letter
        $createRequest = $auth->createPermission(Rbac::PERMISSION_REQUEST_CREATE);
        $auth->add($createRequest);
        $updateRequest = $auth->createPermission(Rbac::PERMISSION_REQUEST_UPDATE);
        $auth->add($updateRequest);
        $disableRequest = $auth->createPermission(Rbac::PERMISSION_REQUEST_DISABLE);
        $auth->add($disableRequest);
        $viewRequest = $auth->createPermission(Rbac::PERMISSION_REQUEST_VIEW);
        $auth->add($viewRequest);
        $viewRequests = $auth->createPermission(Rbac::PERMISSION_REQUESTS_VIEW);
        $auth->add($viewRequests);

        // Timeline
        $createTimeline = $auth->createPermission(Rbac::PERMISSION_TIMELINE_CREATE);
        $auth->add($createTimeline);
        $updateTimelineStep = $auth->createPermission(Rbac::PERMISSION_TIMELINE_UPDATE_STEP);
        $auth->add($updateTimelineStep);
        $disableTimeline = $auth->createPermission(Rbac::PERMISSION_TIMELINE_DISABLE);
        $auth->add($disableTimeline);
        $viewTimeline = $auth->createPermission(Rbac::PERMISSION_TIMELINE_VIEW);
        $auth->add($viewTimeline);
        $viewTimelines = $auth->createPermission(Rbac::PERMISSION_TIMELINES_VIEW);
        $auth->add($viewTimelines);
        $sendObjectsTimeline = $auth->createPermission(Rbac::PERMISSION_TIMELINES_SEND_OBJECTS);
        $auth->add($sendObjectsTimeline);
        $addActionCommentTimeline = $auth->createPermission(Rbac::PERMISSION_TIMELINES_ADD_ACTION_COMMENT);
        $auth->add($addActionCommentTimeline);



        // ********* CREATE ROLES ***********


        $user = $auth->createRole(Rbac::ROLE_USER);
        $auth->add($user);

        $consultant = $auth->createRole(Rbac::ROLE_CONSULTANT);
        $auth->add($consultant);

        $moderator = $auth->createRole(Rbac::ROLE_MODERATOR);
        $auth->add($moderator);

        $director = $auth->createRole(Rbac::ROLE_DIRECTOR);
        $auth->add($director);

        $admin = $auth->createRole(Rbac::ROLE_ADMIN);
        $auth->add($admin);


        // ********* ADD CHILD ***********


        // TODO: ...


        // ********* ADD ADMIN ASSIGN ***********
        $adminUser = User::findOne(['username' => 'admin']);
        $auth->assign($admin, $adminUser->id);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->getAuthManager();
        $auth->removeAll();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221213_131124_create_user_roles cannot be reverted.\n";

        return false;
    }
    */
}
