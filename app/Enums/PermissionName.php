<?php

namespace App\Enums;

enum PermissionName: string
{
    case PLATFORM_ACCESS = 'platform.access';
    case PLATFORM_DASHBOARD_VIEW = 'platform.dashboard.view';
    case PLATFORM_CAR_WASHES_VIEW = 'platform.car-washes.view';
    case PLATFORM_CAR_WASHES_CREATE = 'platform.car-washes.create';
    case PLATFORM_CAR_WASHES_UPDATE = 'platform.car-washes.update';
    case PLATFORM_CAR_WASHES_APPROVE = 'platform.car-washes.approve';
    case PLATFORM_CAR_WASHES_SUSPEND = 'platform.car-washes.suspend';
    case PLATFORM_USERS_VIEW = 'platform.users.view';
    case PLATFORM_USERS_UPDATE = 'platform.users.update';
    case PLATFORM_ROLES_MANAGE = 'platform.roles.manage';
    case PLATFORM_BOOKINGS_VIEW = 'platform.bookings.view';
    case PLATFORM_BOOKINGS_MANAGE = 'platform.bookings.manage';
    case PLATFORM_REPORTS_VIEW = 'platform.reports.view';
    case PLATFORM_FINANCE_VIEW = 'platform.finance.view';
    case PLATFORM_FINANCE_MANAGE = 'platform.finance.manage';
    case PLATFORM_SETTINGS_MANAGE = 'platform.settings.manage';
    case PLATFORM_AUDIT_VIEW = 'platform.audit.view';

    case CAR_WASH_PANEL_ACCESS = 'carwash.panel.access';
    case CAR_WASH_DASHBOARD_VIEW = 'carwash.dashboard.view';
    case CAR_WASH_PROFILE_VIEW = 'carwash.profile.view';
    case CAR_WASH_PROFILE_UPDATE = 'carwash.profile.update';
    case CAR_WASH_SETTINGS_VIEW = 'carwash.settings.view';
    case CAR_WASH_SETTINGS_UPDATE = 'carwash.settings.update';

    case CAR_WASH_MEMBERS_VIEW = 'carwash.members.view';
    case CAR_WASH_MEMBERS_INVITE = 'carwash.members.invite';
    case CAR_WASH_MEMBERS_UPDATE = 'carwash.members.update';
    case CAR_WASH_MEMBERS_REMOVE = 'carwash.members.remove';

    case CAR_WASH_SERVICES_VIEW = 'carwash.services.view';
    case CAR_WASH_SERVICES_CREATE = 'carwash.services.create';
    case CAR_WASH_SERVICES_UPDATE = 'carwash.services.update';
    case CAR_WASH_SERVICES_DELETE = 'carwash.services.delete';

    case CAR_WASH_SCHEDULE_VIEW = 'carwash.schedule.view';
    case CAR_WASH_SCHEDULE_MANAGE = 'carwash.schedule.manage';
    case CAR_WASH_SLOTS_REGENERATE = 'carwash.slots.regenerate';

    case CAR_WASH_BOOKINGS_VIEW = 'carwash.bookings.view';
    case CAR_WASH_BOOKINGS_CREATE = 'carwash.bookings.create';
    case CAR_WASH_BOOKINGS_UPDATE = 'carwash.bookings.update';
    case CAR_WASH_BOOKINGS_CONFIRM = 'carwash.bookings.confirm';
    case CAR_WASH_BOOKINGS_CANCEL = 'carwash.bookings.cancel';
    case CAR_WASH_BOOKINGS_CHECK_IN = 'carwash.bookings.check-in';
    case CAR_WASH_BOOKINGS_START = 'carwash.bookings.start';
    case CAR_WASH_BOOKINGS_COMPLETE = 'carwash.bookings.complete';
    case CAR_WASH_BOOKINGS_NO_SHOW = 'carwash.bookings.no-show';

    case CAR_WASH_CUSTOMERS_VIEW = 'carwash.customers.view';
    case CAR_WASH_CUSTOMERS_UPDATE = 'carwash.customers.update';

    case CAR_WASH_PAYMENTS_VIEW = 'carwash.payments.view';
    case CAR_WASH_PAYMENTS_CREATE = 'carwash.payments.create';
    case CAR_WASH_PAYMENTS_REFUND = 'carwash.payments.refund';

    case CAR_WASH_REPORTS_VIEW = 'carwash.reports.view';
    case CAR_WASH_FINANCE_VIEW = 'carwash.finance.view';
    case CAR_WASH_QR_VIEW = 'carwash.qr.view';
    case CAR_WASH_QR_MANAGE = 'carwash.qr.manage';
    case CAR_WASH_AUDIT_VIEW = 'carwash.audit.view';
}
