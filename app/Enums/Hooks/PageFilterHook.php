<?php

declare(strict_types=1);

namespace App\Enums\Hooks;

enum PageFilterHook: string
{
    // Validation rules
    case PAGE_STORE_VALIDATION_RULES = 'page.store.validation.rules';
    case PAGE_CREATED_BEFORE = 'page.created.before';
    case PAGE_UPDATED_BEFORE = 'page.updated.before';
    case PAGE_UPDATED_AFTER = 'page.updated.after';
    case PAGE_CREATED_AFTER = 'page.created.after';
    case PAGE_UPDATE_VALIDATION_RULES = 'page.update.validation.rules';

    // UI Hooks - Breadcrumbs
    case PAGE_AFTER_BREADCRUMBS = 'filter.page.after_breadcrumbs';
    case PAGE_LIST_AFTER_BREADCRUMBS = 'filter.page.list.after_breadcrumbs';
    case PAGE_CREATE_AFTER_BREADCRUMBS = 'filter.page.create.after_breadcrumbs';
    case PAGE_EDIT_AFTER_BREADCRUMBS = 'filter.page.edit.after_breadcrumbs';
    case PAGE_SHOW_AFTER_BREADCRUMBS = 'filter.page.show.after_breadcrumbs';

    // UI Hooks - Table
    case PAGE_AFTER_TABLE = 'filter.page.after_table';

    // UI Hooks - Form
    case INSIDE_PAGE_FORM_START = 'filter.page.form_start';
    case PAGE_FORM_AFTER_TITLE = 'filter.page.form_after_title';
    case PAGE_FORM_AFTER_DESCRIPTION = 'filter.page.form_after_description';
    case PAGE_FORM_AFTER_EVENT_DATE = 'filter.page.form_after_event_date';
    case PAGE_FORM_AFTER_START_TIME = 'filter.page.form_after_start_time';
    case PAGE_FORM_AFTER_END_TIME = 'filter.page.form_after_end_time';
    case PAGE_FORM_AFTER_LOCATION = 'filter.page.form_after_location';
    case PAGE_FORM_AFTER_CATEGORY = 'filter.page.form_after_category';
    case PAGE_FORM_AFTER_STATUS = 'filter.page.form_after_status';
    case PAGE_FORM_AFTER_SUBMIT_BUTTONS = 'filter.page.form_after_submit_buttons';
    case AFTER_PAGE_FORM = 'filter.page.after_form';

    case PAGE_SHOW_AFTER_CONTENT = 'filter.page.show.after_content';

    // Options
    case PAGE_STATUS_OPTIONS = 'filter.page.status_options';
}
