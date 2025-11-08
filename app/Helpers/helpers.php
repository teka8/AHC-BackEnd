<?php

if (!function_exists('get_program_status_class')) {
    function get_program_status_class(string $status): string
    {
        return match ($status) {
            'draft' => 'badge badge-outline-secondary',
            'active' => 'badge badge-outline-success',
            'paused' => 'badge badge-outline-warning',
            'archived' => 'badge badge-outline-info',
            default => 'badge badge-outline-secondary',
        };
    }
}
