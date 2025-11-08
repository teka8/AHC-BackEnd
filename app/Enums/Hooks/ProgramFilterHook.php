<?php

namespace App\Enums\Hooks;

enum ProgramFilterHook: string
{
    case PROGRAMS_AFTER_BREADCRUMBS = 'filter.program.after_breadcrumbs';
    case PROGRAMS_AFTER_TABLE = 'filter.program.after_table';
}
