<?php

namespace App\Actions\User\Dashboards;

use Lorisleiva\Actions\Concerns\AsAction;

class GenerateDeveloperTitleAndSubtitle
{
    use AsAction;

    public function handle()
    {
        $results = [
            'title' => 'Dashboard',
            'subtitle' => 'Yup. Good Job. You can type a password.'
        ];

        $stuff_to_say = $this->thingsToSay();
        $max = count($stuff_to_say);
        $which_one = $this_one = rand(0, ($max - 1));

        $results['title'] = $stuff_to_say[$which_one][0];
        $results['subtitle'] = $stuff_to_say[$this_one][1];

        return $results;
    }

    private function thingsToSay()
    {
        $mean_thing1 = [backpack_user()->name.'\'s Dashboard', backpack_user()->email == 'shivam@capeandbay.com'
            ? 'Your Drum Can, Sir'
            : '%WittyComment% {{-- Fuck This Guy, Just Really Let Him Have It --}}'

        ];

        $mean_thing2 = backpack_user()->email == 'shivam@capeandbay.com'
            ? [backpack_user()->name.'\'s Drum Can', 'Huh? Is Someone There?']
            : [backpack_user()->name.'\'s Shit', 'I mean, isn\'t it?']
        ;

        return [
            [backpack_user()->name.'\'s Dashboard', 'Welcome.'],
            [backpack_user()->name.'\'s Dashboard', 'Sup?'],
            [backpack_user()->name.'\'s Dashboard', 'Sup.'],
            [backpack_user()->name.'\'s Dashboard', 'How\'s it goin?'],
            [backpack_user()->name.'\'s Dashboard', 'What\'s Happening?'],
            [backpack_user()->name.'\'s Dashboard', 'Do Stuff.'],
            [backpack_user()->name.'\'s Dashboard', 'Yep.'],
            [backpack_user()->name.'\'s Dashboard', 'Yup. Good Job. You can type a password'],
            $mean_thing1,
            $mean_thing2,
            ['Just wait a Gahdam Section', 'Yeah, I don\'t remember what I was gonna say.'],
            [backpack_user()->name.'\'s Weather Forecast', 'Why don\'t you look out the window, genius?'],
            [backpack_user()->name.'\'s Dashboard', 'Add more things to say in GenerateDeveloperTitleAndSubtitle.php.'],
        ];
    }
}
