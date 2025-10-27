<?php

declare(strict_types=1);

namespace App\Enums\Hooks;

enum PageActionHook: string
{
    case PAGE_CREATED_BEFORE = 'action.page.created_before';
    case PAGE_CREATED_AFTER = 'action.page.created_after';

    case PAGE_UPDATED_BEFORE = 'action.page.updated_before';
    case PAGE_UPDATED_AFTER = 'action.page.updated_after';

    case PAGE_DELETED_BEFORE = 'action.page.deleted_before';
    case PAGE_DELETED_AFTER = 'action.page.deleted_after';

    case PAGE_BULK_DELETED_BEFORE = 'action.page.bulk_deleted_before';
    case PAGE_BULK_DELETED_AFTER = 'action.page.bulk_deleted_after';

    case PAGE_PUBLISHED_BEFORE = 'action.page.published_before';
    case PAGE_PUBLISHED_AFTER = 'action.page.published_after';

    case PAGE_TAXONOMIES_UPDATED = 'action.page.taxonomies_updated';
}
