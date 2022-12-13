<?php

namespace app\models\user\auth;

class Rbac
{
    // ************ Permissions ************

    // Contact
    public const PERMISSION_CONTACT_DELETE = "deleteContact";
    public const PERMISSION_CONTACT_CREATE = "createContact";
    public const PERMISSION_CONTACT_UPDATE = "updateContact";
    public const PERMISSION_CONTACT_VIEW = "viewContact";
    public const PERMISSION_CONTACTS_VIEW = "viewContacts";
    public const PERMISSION_CONTACTS_CREATE_COMMENT = "createContactComment";

    // User
    public const PERMISSION_USER_CREATE = "createUser";
    public const PERMISSION_USER_UPDATE = "updateUser";
    public const PERMISSION_USER_DISABLE = "disableUser";
    public const PERMISSION_USER_VIEW = "viewUser";
    public const PERMISSION_USERS_VIEW = "viewUsers";

    // Company
    public const PERMISSION_COMPANY_CREATE = "createCompany";
    public const PERMISSION_COMPANY_UPDATE = "updateCompany";
    public const PERMISSION_COMPANY_VIEW = "viewCompany";
    public const PERMISSION_COMPANIES_VIEW = "viewCompanies";

    // Company group
    public const PERMISSION_COMPANY_GROUP_CREATE = "createCompanyGroup";
    public const PERMISSION_COMPANY_GROUP_UPDATE = "updateCompanyGroup";
    public const PERMISSION_COMPANY_GROUP_VIEW = "viewCompanyGroup";
    public const PERMISSION_COMPANY_GROUPS_VIEW = "viewCompanyGroups";

    // Deal
    public const PERMISSION_DEAL_CREATE = "createDeal";
    public const PERMISSION_DEAL_UPDATE = "updateDeal";
    public const PERMISSION_DEAL_DISABLE = "disableDeal";
    public const PERMISSION_DEAL_VIEW = "viewDeal";
    public const PERMISSION_DEALS_VIEW = "viewDeals";

    // Letter
    public const PERMISSION_LETTER_CREATE = "createLetter";
    public const PERMISSION_LETTER_UPDATE = "updateLetter";
    public const PERMISSION_LETTER_DELETE = "deleteLetter";
    public const PERMISSION_LETTER_VIEW = "viewLetter";
    public const PERMISSION_LETTERS_VIEW = "viewLetters";

    // Request
    public const PERMISSION_REQUEST_CREATE = "createRequest";
    public const PERMISSION_REQUEST_UPDATE = "updateRequest";
    public const PERMISSION_REQUEST_DISABLE = "disableRequest";
    public const PERMISSION_REQUEST_VIEW = "viewRequest";
    public const PERMISSION_REQUESTS_VIEW = "viewRequests";

    // Timeline
    public const PERMISSION_TIMELINE_CREATE = "createTimeline";
    public const PERMISSION_TIMELINE_UPDATE_STEP = "updateTimelineStep";
    public const PERMISSION_TIMELINE_DISABLE = "disableTimeline";
    public const PERMISSION_TIMELINE_VIEW = "viewTimeline";
    public const PERMISSION_TIMELINES_VIEW = "viewTimelines";
    public const PERMISSION_TIMELINES_SEND_OBJECTS = "sendObjectsTimeline";
    public const PERMISSION_TIMELINES_ADD_ACTION_COMMENT = "addActionCommentTimeline";

    // ************ Roles ************
    public const ROLE_USER = "user";
    public const ROLE_CONSULTANT = "consultant";
    public const ROLE_MODERATOR = "moderator";
    public const ROLE_DIRECTOR = "director";
    public const ROLE_ADMIN = "admin";
}
