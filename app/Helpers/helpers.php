<?php

if (!function_exists('get_program_status_class')) {
    function get_program_status_class(string $status): string
    {
        return match (strtolower($status)) {
            'upcoming' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'paused' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'archived' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}

if (!function_exists('get_action_button_classes')) {
    function get_action_button_classes(string $color): string
    {
        return match (strtolower($color)) {
            'green' => 'text-white',
            'yellow' => 'text-white',
            'gray' => 'text-white',
            'blue' => 'text-white',
            'red' => 'text-white',
            default => 'text-white',
        };
    }
}

if (!function_exists('get_action_button_style')) {
    function get_action_button_style(string $color): string
    {
        return match (strtolower($color)) {
            'green' => 'background-color: #16a34a;',
            'yellow' => 'background-color: #ca8a04;',
            'gray' => 'background-color: #374151;',
            'blue' => 'background-color: #2563eb;',
            'red' => 'background-color: #dc2626;',
            default => 'background-color: #374151;',
        };
    }
}
