<?php

declare(strict_types=1);

namespace App\Enums\Hooks;

enum EventActionHook: string
{
    case EVENT_CREATED_BEFORE = 'action.event.created_before';
    case EVENT_CREATED_AFTER = 'action.event.created_after';

    case EVENT_UPDATED_BEFORE = 'action.event.updated_before';
    case EVENT_UPDATED_AFTER = 'action.event.updated_after';

    case EVENT_DELETED_BEFORE = 'action.event.deleted_before';
    case EVENT_DELETED_AFTER = 'action.event.deleted_after';

    case EVENT_BULK_DELETED_BEFORE = 'action.event.bulk_deleted_before';
    case EVENT_BULK_DELETED_AFTER = 'action.event.bulk_deleted_after';

    case EVENT_PUBLISHED_BEFORE = 'action.event.published_before';
    case EVENT_PUBLISHED_AFTER = 'action.event.published_after';

    case EVENT_META_UPDATED = 'action.event.meta_updated';
}
