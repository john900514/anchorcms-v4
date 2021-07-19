<?php

namespace Database\Seeders\MenuOptions;

use App\Models\Utility\SidebarOptions;
use Illuminate\Database\Seeder;

class MenuOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mapping = [
            /* Admin Users Links */
            ['name' => 'Users',             'route' => '',                                   'menu_name' => 'Admin',           'is_submenu' => 1,  'permitted_role' => 'admin',      'order' =>   2, 'icon' => 'la la-user-nurse'],
            ['name' => 'Manage',            'route' => '/access/users',                      'menu_name' => 'Users',           'is_submenu' => 0,  'permitted_role' => 'admin',      'order' =>   1, 'icon' => 'la la-user-tie'],
            ['name' => 'New User',          'route' => '/access/users/create',               'menu_name' => 'Users',           'is_submenu' => 0,  'permitted_role' => 'admin',      'order' =>   1, 'icon' => 'la la-user-plus'],
            ['name' => 'Clients',           'route' => '',                                   'menu_name' => 'Admin',           'is_submenu' => 1,  'permitted_role' => 'admin',      'order' =>   3, 'icon' => 'la la-user-nurse'],
            ['name' => 'Manage',            'route' => '/access/clients',                    'menu_name' => 'Clients',         'is_submenu' => 0,  'permitted_role' => 'admin',      'order' =>   1, 'icon' => 'la la-user-tie'],
            ['name' => 'New Client',        'route' => '/access/clients/create',             'menu_name' => 'Clients',         'is_submenu' => 0,  'permitted_role' => 'admin',      'order' =>   2, 'icon' => 'la la-user-plus'],
            ['name' => 'Switch Dashboards', 'route' => '',                                   'menu_name' => '',                'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   3,     'icon' => 'fas fa-swatchbook', 'is_modal' => 1, 'action_url' => '/access/dashboards/switch'],

            ['name' => 'Logout',            'route' => '/access/logout',                     'menu_name' => '',                'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   1000,  'icon' => 'fas fa-sign-out-alt', 'is_modal' => 1, 'action_url' => '/access/dashboards/switch'],
            /* Developer User Links */
            ['name' => 'Users',             'route' => '',                                   'menu_name' => 'Admin',           'is_submenu' => 1,  'permitted_role' => 'developer',     'order' =>   2,     'icon' => 'la la-user-nurse'],
            ['name' => 'Manage',            'route' => '/access/users',                      'menu_name' => 'Users',           'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   1,     'icon' => 'la la-user-tie'],
            ['name' => 'New User',          'route' => '/access/users/create',               'menu_name' => 'Users',           'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   1,     'icon' => 'la la-user-plus'],
            ['name' => 'Clients',           'route' => '',                                   'menu_name' => 'Admin',           'is_submenu' => 1,  'permitted_role' => 'developer',     'order' =>   3,     'icon' => 'la la-user-nurse'],
            ['name' => 'Manage',            'route' => '/access/clients',                    'menu_name' => 'Clients',         'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   1,     'icon' => 'la la-user-tie'],
            ['name' => 'New Client',        'route' => '/access/clients/create',             'menu_name' => 'Clients',         'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   2,     'icon' => 'la la-user-plus'],
            ['name' => 'Switch Dashboards', 'route' => '',                                   'menu_name' => '',                'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   3,     'icon' => 'fas fa-swatchbook', 'is_standalone' => 1, 'is_modal' => 1,        'action_url' => '/access/dashboards/switch'],
            ['name' => 'Utility',           'route' => '',                                   'menu_name' => 'Admin',           'is_submenu' => 1,  'permitted_role' => 'developer',     'order' =>   999,   'icon' => 'las la-tools'],
            ['name' => 'SSO Generator',     'route' => '/access/sso/generator',              'menu_name' => 'Utility',         'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   9,     'icon' => 'fad fa-portal-enter'],
            ['name' => 'Library of Babble', 'route' => '',                                   'menu_name' => '',                'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   4,     'icon' => 'fas fa-swatchbook',  'is_standalone' => 1, 'is_post_action' => 1, 'action_url' => '/access/sso?location=kb'],
            ['name' => 'Logout',            'route' => '/access/logout',                     'menu_name' => '',                'is_submenu' => 0,  'permitted_role' => 'developer',     'order' =>   1000,  'icon' => 'fas fa-book-reader', 'is_standalone' => 1],
        ];

        foreach($mapping as $map)
        {
            $map['page_shown'] = 'all';
            $map['active'] = 1;
            $map['icon'] .= ' nav-icon';

            SidebarOptions::firstOrCreate($map);
        }
    }
}
