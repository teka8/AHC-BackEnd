<?php

declare(strict_types=1);

namespace App\Enums\Hooks;

enum PageFilterHook: string
{
    case PAGE_CREATED_BEFORE = 'filter.page.created_before';
    case PAGE_CREATED_AFTER = 'filter.page.created_after';

    case PAGE_UPDATED_BEFORE = 'filter.page.updated_before';
    case PAGE_UPDATED_AFTER = 'filter.page.updated_after';

    case PAGE_DELETED_BEFORE = 'filter.page.deleted_before';
    case PAGE_DELETED_AFTER = 'filter.page.deleted_after';

    case PAGE_CONTENT_FILTER = 'filter.page.content';
    case PAGE_TITLE_FILTER = 'filter.page.title';
    case PAGE_STATUS_FILTER = 'filter.page.status';

    // UI Hooks - Breadcrumbs
    case PAGES_AFTER_BREADCRUMBS = 'filter.page.after_breadcrumbs';
    case PAGES_LIST_AFTER_BREADCRUMBS = 'filter.page.list.after_breadcrumbs';
    case PAGES_CREATE_AFTER_BREADCRUMBS = 'filter.page.create.after_breadcrumbs';
    case PAGES_EDIT_AFTER_BREADCRUMBS = 'filter.page.edit.after_breadcrumbs';
    case PAGES_SHOW_AFTER_BREADCRUMBS = 'filter.page.show.after_breadcrumbs';

    // UI Hooks - Table.
    case PAGES_AFTER_TABLE = 'filter.page.after_table';

    // UI Hooks - Form.
    case INSIDE_PAGE_FORM_START = 'filter.page.form_start';
    case PAGE_FORM_AFTER_TITLE = 'filter.page.form_after_title';
    case PAGE_FORM_AFTER_SLUG = 'filter.page.form_after_slug';
    case PAGE_FORM_AFTER_CONTENT = 'filter.page.form_after_content';
    case PAGE_FORM_AFTER_EXCERPT = 'filter.page.form_after_excerpt';
    case PAGE_FORM_AFTER_STATUS = 'filter.page.form_after_status';
    case PAGE_FORM_AFTER_PUBLISH_DATE = 'filter.page.form_after_publish_date';
    case PAGE_FORM_AFTER_SUBMIT_BUTTONS = 'filter.page.form_after_submit_buttons';
    case PAGE_FORM_AFTER_FEATURED_IMAGE = 'filter.page.form_after_featured_image';
    case PAGE_FORM_AFTER_CONTENT_PARENT = 'filter.page.form_after_content_parent';
    case PAGE_FORM_AFTER_TAXONOMY = 'filter.page.form_after_taxonomy_';
    case AFTER_PAGE_FORM = 'filter.page.after_form';

    // UI Hooks - Filters.
    case PAGE_ACTIONS_AFTER_EDIT = 'filter.page.actions_after_edit';
    case PAGE_ACTIONS_AFTER_VIEW = 'filter.page.actions_after_view';
    case PAGE_ACTIONS_AFTER_DELETE = 'filter.page.actions_after_delete';

    // Validation rule - Filters.
    case PAGE_STORE_VALIDATION_RULES = 'page.store.validation.rules';
    case PAGE_UPDATE_VALIDATION_RULES = 'page.update.validation.rules';

    // UI Hooks - Content.
    case PAGES_SHOW_AFTER_CONTENT = 'filter.pages.show_after_content';

    // Options.
    case PAGE_STATUS_OPTIONS = 'filter.page.status_options';
}
