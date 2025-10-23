<?php

declare(strict_types=1);

namespace App\Enums\Hooks;

enum EventFilterHook: string
{
    // Validation rules
    case EVENT_STORE_VALIDATION_RULES = 'event.store.validation.rules';
    case EVENT_CREATED_BEFORE = 'event.created.before';
    case EVENT_UPDATED_BEFORE = 'event.updated.before';
    case EVENT_UPDATED_AFTER = 'event.updated.after';
    case EVENT_CREATED_AFTER = 'event.created.after';
    case EVENT_UPDATE_VALIDATION_RULES = 'event.update.validation.rules';

    // UI Hooks - Breadcrumbs
    case EVENTS_AFTER_BREADCRUMBS = 'filter.event.after_breadcrumbs';
    case EVENTS_LIST_AFTER_BREADCRUMBS = 'filter.event.list.after_breadcrumbs';
    case EVENTS_CREATE_AFTER_BREADCRUMBS = 'filter.event.create.after_breadcrumbs';
    case EVENTS_EDIT_AFTER_BREADCRUMBS = 'filter.event.edit.after_breadcrumbs';
    case EVENTS_SHOW_AFTER_BREADCRUMBS = 'filter.event.show.after_breadcrumbs';

    // UI Hooks - Table
    case EVENTS_AFTER_TABLE = 'filter.event.after_table';

    // UI Hooks - Form
    case INSIDE_EVENT_FORM_START = 'filter.event.form_start';
    case EVENT_FORM_AFTER_TITLE = 'filter.event.form_after_title';
    case EVENT_FORM_AFTER_DESCRIPTION = 'filter.event.form_after_description';
    case EVENT_FORM_AFTER_EVENT_DATE = 'filter.event.form_after_event_date';
    case EVENT_FORM_AFTER_START_TIME = 'filter.event.form_after_start_time';
    case EVENT_FORM_AFTER_END_TIME = 'filter.event.form_after_end_time';
    case EVENT_FORM_AFTER_LOCATION = 'filter.event.form_after_location';
    case EVENT_FORM_AFTER_CATEGORY = 'filter.event.form_after_category';
    case EVENT_FORM_AFTER_STATUS = 'filter.event.form_after_status';
    case EVENT_FORM_AFTER_SUBMIT_BUTTONS = 'filter.event.form_after_submit_buttons';
    case AFTER_EVENT_FORM = 'filter.event.after_form';

    case EVENTS_SHOW_AFTER_CONTENT = 'filter.event.show.after_content';

    // Options
    case EVENT_STATUS_OPTIONS = 'filter.event.status_options';
}
