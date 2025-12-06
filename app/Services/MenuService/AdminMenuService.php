<?php

declare(strict_types=1);

namespace App\Services\MenuService;

use App\Enums\Hooks\AdminFilterHook;
use App\Services\Content\ContentService;
use App\Support\Facades\Hook;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AdminMenuService
{
    /**
     * @var AdminMenuItem[][]
     */
    protected array $groups = [];

    /**
     * Add a menu item to the admin sidebar.
     *
     * @param  AdminMenuItem|array  $item  The menu item or configuration array
     * @param  string|null  $group  The group to add the item to
     *
     * @throws \InvalidArgumentException
     */
    public function addMenuItem(AdminMenuItem|array $item, ?string $group = null): void
    {
        $group = $group ?: __('Main');
        $menuItem = $this->createAdminMenuItem($item);
        if (! isset($this->groups[$group])) {
            $this->groups[$group] = [];
        }

        if ($menuItem->userHasPermission()) {
            $this->groups[$group][] = $menuItem;
        }
    }

    protected function createAdminMenuItem(AdminMenuItem|array $data): AdminMenuItem
    {
        if ($data instanceof AdminMenuItem) {
            return $data;
        }

        $menuItem = new AdminMenuItem();

        if (isset($data['children']) && is_array($data['children'])) {
            $data['children'] = array_map(
                function ($child) {
                    // Check if user is authenticated
                    $user = auth()->user();
                    if (! $user) {
                        return null;
                    }

                    // Handle permissions.
                    if (isset($child['permission'])) {
                        $child['permissions'] = $child['permission'];
                        unset($child['permission']);
                    }

                    $permissions = $child['permissions'] ?? [];
                    if (empty($permissions) || $user->hasAnyPermission((array) $permissions)) {
                        return $this->createAdminMenuItem($child);
                    }

                    return null;
                },
                $data['children']
            );

            // Filter out null values (items without permission).
            $data['children'] = array_filter($data['children']);
        }

        // Convert 'permission' to 'permissions' for consistency
        if (isset($data['permission'])) {
            $data['permissions'] = $data['permission'];
            unset($data['permission']);
        }

        // Handle route with params
        if (isset($data['route']) && isset($data['params'])) {
            $routeName = $data['route'];
            $params = $data['params'];

            if (is_array($params)) {
                $data['route'] = route($routeName, $params);
            } else {
                $data['route'] = route($routeName, [$params]);
            }
        }

        return $menuItem->setAttributes($data);
    }

    public function getMenu()
    {
        $this->addMenuItem([
            'label' => __('Dashboard'),
            'icon' => 'lucide:layout-dashboard',
            'route' => route('admin.dashboard'),
            'active' => Route::is('admin.dashboard'),
            'id' => 'dashboard',
            'priority' => 1,
            'permissions' => 'dashboard.view',
        ]);

        $this->registerPostTypesInMenu(null);

        $this->addMenuItem([
            'label' => __('Events'),
            'icon' => 'lucide:calendar',
            'id' => 'events-submenu',
            'active' => Route::is('admin.events.*'),
            'priority' => 12,
            'permissions' => 'event.view',
            'children' => [
                [
                    'label' => __('All Events'),
                    'route' => route('admin.events.index'),
                    'active' => Route::is('admin.events.index') || Route::is('admin.events.*') && ! Route::is('admin.events.create'),
                    'priority' => 20,
                    'permissions' => 'event.view',
                ],
                [
                    'label' => __('Add New'),
                    'route' => route('admin.events.create'),
                    'active' => Route::is('admin.events.create'),
                    'priority' => 10,
                    'permissions' => 'event.create',
                ],
            ],
        ]);

        // pages
        $this->addMenuItem([
            'label' => __('Pages'),
            'icon' => 'lucide:file-text',
            'id' => 'pages-submenu',
            'active' => Route::is('admin.pages.*'),
            'priority' => 12,
            'permissions' => 'page.view',
            'children' => [
                [
                    'label' => __('All Pages'),
                    'route' => route('admin.pages.index'),
                    'active' => Route::is('admin.pages.index') || Route::is('admin.pages.*') && ! Route::is('admin.pages.create'),
                    'priority' => 20,
                    'permissions' => 'page.view',
                ],
                [
                    'label' => __('Add New'),
                    'route' => route('admin.pages.create'),
                    'active' => Route::is('admin.pages.create'),
                    'priority' => 10,
                    'permissions' => 'page.create',
                ],
            ],
        ]);

        $this->addMenuItem([
            'label' => __('Media Library'),
            'icon' => 'lucide:image',
            'route' => route('admin.media.index'),
            'active' => Route::is('admin.media.*'),
            'id' => 'media',
            'priority' => 35,
            'permissions' => 'media.view',
        ]);

        $this->addMenuItem([
            'label' => __('Media'),
            'icon' => 'lucide:folders',
            'route' => route('admin.media-manager.index'),
            'active' => Route::is('admin.media-manager.*'),
            'id' => 'media-manager',
            'priority' => 36,
            'permissions' => 'media.view',
        ]);

        // AHC Leaders
        $this->addMenuItem([
            'label' => __('AHC Leaders'),
            'icon' => 'lucide:users',
            'id' => 'ahc-leaders-submenu',
            'active' => Route::is('admin.ahc-leaders.*'),
            'priority' => 38,
            'permissions' => 'ahc-leader.view',
            'children' => [
                [
                    'label' => __('All Leaders'),
                    'route' => route('admin.ahc-leaders.index'),
                    'active' => Route::is('admin.ahc-leaders.index') || Route::is('admin.ahc-leaders.show') || Route::is('admin.ahc-leaders.edit'),
                    'priority' => 10,
                    'permissions' => 'ahc-leader.view',
                ],
                [
                    'label' => __('Add New Leader'),
                    'route' => route('admin.ahc-leaders.create'),
                    'active' => Route::is('admin.ahc-leaders.create'),
                    'priority' => 20,
                    'permissions' => 'ahc-leader.create',
                ],
            ],
        ]);

        $this->addMenuItem(
            [
                'label' => __('Resources'),
                'icon' => 'lucide:library',
                'id' => 'resources-submenu',
                'active' => Route::is('admin.document.*') || Route::is('admin.education.*') || Route::is('admin.others.*'),
                'priority' => 40,
                'permissions' => ['document.view', 'educational_resource.view'],
                'children' => [
                    [
                        'label' => __('Document Repository'),
                        'route' => route('admin.document.index'),
                        'active' => Route::is('admin.document.*'),
                        'priority' => 10,
                        'permissions' => 'document.view',
                    ],
                    [
                        'label' => __('Educational Resource Hub'),
                        'route' => route('admin.education.index'),
                        'active' => Route::is('admin.education.*'),
                        'priority' => 20,
                        'permissions' => 'educational_resource.view',
                    ],
                    [
                        'label' => __('Others'),
                        'route' => route('admin.others.index'),
                        'active' => Route::is('admin.others.*'),
                        'priority' => 30,
                        'permissions' => ['educational_resource.view'],
                    ],
                ],
            ],
        );

        // $this->addMenuItem([
        //     'label' => __('Modules'),
        //     'icon' => 'lucide:boxes',
        //     'route' => route('admin.modules.index'),
        //     'active' => Route::is('admin.modules.index'),
        //     'id' => 'modules',
        //     'priority' => 25,
        //     'permissions' => 'module.view',
        // ], __('More'));

        $this->addMenuItem([
            'label' => __('Monitoring'),
            'icon' => 'lucide:monitor',
            'id' => 'monitoring-submenu',
            'active' => Route::is('admin.actionlog.*'),
            'priority' => 50,
            'permissions' => ['pulse.view', 'actionlog.view'],
            'children' => [
                [
                    'label' => __('Action Logs'),
                    'route' => route('admin.actionlog.index'),
                    'active' => Route::is('admin.actionlog.index'),
                    'priority' => 10,
                    'permissions' => 'actionlog.view',
                ],
                [
                    'label' => __('Laravel Pulse'),
                    'route' => route('pulse'),
                    'active' => false,
                    'target' => '_blank',
                    'priority' => 20,
                    'permissions' => 'pulse.view',
                ],
            ],
        ], __('More'));

        $this->addMenuItem([
            'label' => __('Subscribers'),
            'icon' => 'lucide:mailbox',
            'route' => route('admin.subscriptions.index'),
            'active' => Route::is('admin.subscriptions.*'),
            'id' => 'subscriptions',
            'priority' => 45,
            'permissions' => 'subscription.view',
        ], __('More'));

        $this->addMenuItem([
            'label' => __('Contact Messages'),
            'icon' => 'lucide:mail',
            'route' => route('admin.contact-messages.index'),
            'active' => Route::is('admin.contact-messages.*'),
            'id' => 'contact-messages',
            'priority' => 46,
            'permissions' => 'contact_message.view',
        ], __('More'));

        $this->addMenuItem(
            [
                'label' => __('Access Control'),
                'icon' => 'lucide:key',
                'id' => 'access-control-submenu',
                'active' => Route::is('admin.roles.*') || Route::is('admin.permissions.*') || Route::is('admin.users.*'),
                'priority' => 30,
                'permissions' => ['role.create', 'role.view', 'role.edit', 'role.delete', 'user.create', 'user.view', 'user.edit', 'user.delete'],
                'children' => [
                    [
                        'label' => __('Users'),
                        'route' => route('admin.users.index'),
                        'active' => Route::is('admin.users.index') || Route::is('admin.users.create') || Route::is('admin.users.edit'),
                        'priority' => 10,
                        'permissions' => 'user.view',
                    ],
                    [
                        'label' => __('Roles'),
                        'route' => route('admin.roles.index'),
                        'active' => Route::is('admin.roles.index') || Route::is('admin.roles.create') || Route::is('admin.roles.edit'),
                        'priority' => 20,
                        'permissions' => 'role.view',
                    ],
                    [
                        'label' => __('Permissions'),
                        'route' => route('admin.permissions.index'),
                        'active' => Route::is('admin.permissions.index') || Route::is('admin.permissions.show'),
                        'priority' => 30,
                        'permissions' => 'role.view',
                    ],
                ],
            ],
            __('More')
        );

        $this->addMenuItem([
            'label' => __('Settings'),
            'icon' => 'lucide:settings',
            'id' => 'settings-submenu',
            'active' => Route::is('admin.settings.*') || Route::is('admin.translations.*'),
            'priority' => 40,
            'permissions' => ['settings.edit', 'translations.view'],
            'children' => [
                [
                    'label' => __('Settings'),
                    'route' => route('admin.settings.index'),
                    'active' => Route::is('admin.settings.index'),
                    'priority' => 20,
                    'permissions' => 'settings.edit',
                ],
                [
                    'label' => __('Translations'),
                    'route' => route('admin.translations.index'),
                    'active' => Route::is('admin.translations.*'),
                    'priority' => 10,
                    'permissions' => ['translations.view', 'translations.edit'],
                ],
            ],
        ], __('More'));

        $this->addMenuItem([
            'label' => __('Logout'),
            'icon' => 'lucide:log-out',
            'route' => route('admin.dashboard'),
            'active' => false,
            'id' => 'logout',
            'priority' => 10000,
            'html' => '
                <li>
                    <form method="POST" action="' . route('admin.logout.submit') . '">
                        ' . csrf_field() . '
                        <button type="submit" class="menu-item group w-full text-left menu-item-inactive text-gray-700 dark:text-white hover:text-gray-700">
                            <iconify-icon icon="lucide:log-out" class="menu-item-icon " width="16" height="16"></iconify-icon>
                            <span class="menu-item-text">' . __('Logout') . '</span>
                        </button>
                    </form>
                </li>
            ',
        ], __('More'));

        $this->groups = Hook::applyFilters(AdminFilterHook::ADMIN_MENU_GROUPS_BEFORE_SORTING, $this->groups);

        $this->sortMenuItemsByPriority();

        return $this->applyFiltersToMenuItems();
    }

    /**
     * Register post types in the menu
     * Move to main group if $group is null
     */
    protected function registerPostTypesInMenu(?string $group = 'Content'): void
    {
        $contentService = app(ContentService::class);
        $postTypes = $contentService->getPostTypes();

        if ($postTypes->isEmpty()) {
            return;
        }

        foreach ($postTypes as $typeName => $type) {
            // Skip if not showing in menu.
            if (isset($type->show_in_menu) && ! $type->show_in_menu) {
                continue;
            }

            // Create children menu items.
            $currentPostType = strtolower((string) request()->query('post_type'));

            $children = [
                [
                    'title' => __("All {$type->label}"),
                    'route' => 'admin.posts.index',
                    'params' => $typeName,
                    'active' => request()->is('admin/posts/' . $typeName) ||
                        (request()->is('admin/posts/' . $typeName . '/*') && ! request()->is('admin/posts/' . $typeName . '/create')),
                    'priority' => 10,
                    'permissions' => 'news.view',
                ],
                [
                    'title' => __('Add New'),
                    'route' => 'admin.posts.create',
                    'params' => $typeName,
                    'active' => request()->is('admin/posts/' . $typeName . '/create'),
                    'priority' => 20,
                    'permissions' => 'news.create',
                ],
            ];

            // Add taxonomies as children of this post type if this post type has them.
            if (! empty($type->taxonomies)) {
                $taxonomies = $contentService->getTaxonomies()
                    ->whereIn('name', $type->taxonomies);

                foreach ($taxonomies as $taxonomy) {
                    $children[] = [
                        'title' => __($taxonomy->label),
                        'route' => 'admin.terms.index',
                        'params' => [
                            'taxonomy' => $taxonomy->name,
                            'post_type' => $typeName,
                        ],
                        'active' => request()->segment(2) === 'terms'
                            && request()->segment(3) === $taxonomy->name
                            && $currentPostType === strtolower($typeName),
                        'priority' => 30 + $taxonomy->id, // Prioritize after standard items
                        'permissions' => 'term.view',
                    ];
                }
            }

            // Set up menu item with all children.
            $menuItem = [
                'title' => __($type->label),
                'icon' => get_post_type_icon($typeName),
                'id' => 'post-type-' . $typeName,
                'active' => request()->is('admin/posts/' . $typeName . '*') ||
                    (! empty($type->taxonomies) && $this->isCurrentTermBelongsToPostType($type->taxonomies, $typeName)),
                'priority' => 10,
                'permissions' => 'news.view',
                'children' => $children,

            ];

            $this->addMenuItem($menuItem, $group ?: __('Main'));
        }
    }

    /**
     * Check if the current term route belongs to the given taxonomies
     */
    protected function isCurrentTermBelongsToPostType(array $taxonomies, string $postType): bool
    {
        if (! request()->is('admin/terms/*')) {
            return false;
        }

        // Get the current taxonomy from the route
        $currentTaxonomy = request()->segment(3); // admin/terms/{taxonomy}

        if (! in_array($currentTaxonomy, $taxonomies)) {
            return false;
        }

        return strtolower((string) request()->query('post_type')) === strtolower($postType);
    }

    protected function sortMenuItemsByPriority(): void
    {
        foreach ($this->groups as &$groupItems) {
            usort($groupItems, function ($a, $b) {
                return (int) $a->priority <=> (int) $b->priority;
            });
        }
    }

    protected function applyFiltersToMenuItems(): array
    {
        $result = [];
        foreach ($this->groups as $group => $items) {
            // Filter items by permission.
            $filteredItems = array_filter($items, function (AdminMenuItem $item) {
                return $item->userHasPermission();
            });

            // Apply filters that might add/modify menu items.
            $filteredItems = Hook::applyFilters(AdminFilterHook::SIDEBAR_MENU->value . strtolower((string) $group), $filteredItems);

            // Only add the group if it has items after filtering.
            if (! empty($filteredItems)) {
                $result[$group] = $filteredItems;
            }
        }

        return $result;
    }

    public function shouldExpandSubmenu(AdminMenuItem $menuItem): bool
    {
        // If the parent menu item is active, expand the submenu.
        if ($menuItem->active) {
            return true;
        }

        // Check if any child menu item is active.
        foreach ($menuItem->children as $child) {
            if ($child->active) {
                return true;
            }
        }

        return false;
    }

    public function render(array $groupItems): string
    {
        $html = '';
        foreach ($groupItems as $menuItem) {
            $filterKey = ($menuItem->id ?? Str::slug($menuItem->label)) ?: '';
            $html .= Hook::applyFilters(AdminFilterHook::SIDEBAR_MENU_BEFORE->value . $filterKey, '');

            $html .= view('backend.layouts.partials.sidebar.menu-item', [
                'item' => $menuItem,
            ])->render();

            $html .= Hook::applyFilters(AdminFilterHook::SIDEBAR_MENU_AFTER->value . $filterKey, '');
        }

        return $html;
    }
}
