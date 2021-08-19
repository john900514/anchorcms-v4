<?php

namespace App\Services;

use App\Actions\User\Dashboards\GenerateDeveloperTitleAndSubtitle;

class CnBUserDashboardService
{
    public function __construct()
    {

    }

    public function getDashboardBlade()
    {
        $results = 'errors.coming-soon';

        $role = backpack_user()->getRoles()[0];
        switch($role)
        {
            case 'developer':
                $results = 'cms.dashboards.cape-and-bay.developer-dashboard';
                break;

            default:
        }

        return $results;
    }

    public function getDashboardArguments()
    {
        $results = [];

        switch($this->getDashboardBlade())
        {
            case 'cms.dashboards.cape-and-bay.developer-dashboard':
                $name = backpack_user()->name;
                $role = backpack_user()->getRoles()[0];
                $results['breadcrumbs'] = [
                     "{$name} ({$role})" => url(config('backpack.base.route_prefix'), 'dashboard'),
                    'Dashboard' => false,
                ];
                $results['section_headers'] = GenerateDeveloperTitleAndSubtitle::run();

                // @todo - make this more dynamic by allowing the developer to pick the widgets from My Account
                // and stored in user details
                $results['widgets']['before_content'] = [
                    [
                        'type'    => 'div',
                        'class'   => 'row',
                        'content' => [ // widgets
                            [
                                'type'       => 'card',
                                'wrapper' => ['class' => 'col-sm-6 col-md-6'], // optional
                                'class'   => 'card bg-success text-white', // optional
                                'content'    => [
                                    'header' => 'Assigned to You', // optional
                                    'body'   => 'You have <b>0</b> tickets assigned to you',
                                ]
                            ],
                            [
                                'type'       => 'card',
                                'wrapper' => ['class' => 'col-sm-6 col-md-6'], // optional
                                'class'   => 'card bg-info text-white', // optional
                                'content'    => [
                                    'header' => 'Messages', // optional
                                    'body'   => 'You have <b>0</b> new messages.',
                                ]
                            ],
                            [
                                'type'       => 'card',
                                'wrapper' => ['class' => 'col-sm-12 col-md-12'], // optional
                                'class'   => 'card bg-light text-dark', // optional
                                'content'    => [
                                    'header' => 'AWS Billing Activity', // optional
                                    'body'   => '<div style="padding:63% 0 0 0; position:relative;"><iframe src="https://app.databox.com/datawall/4842547e37f5ebd2712758d7dc51a7560611c1b80?i" style="position:absolute; top:0; left:0; width:100%; height:100%;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>',
                                ]
                            ],
                        ]
                    ]
                ];
                break;

            default:
                $results['error_number'] = 500;
        }

        return $results;
    }
}
