<?php

namespace App\Actions\Utility;

use Lorisleiva\Actions\Concerns\AsAction;

class ShowComingSoon
{
    use  AsAction {
        __invoke as protected invokeFromLaravelActions;
    }

    public function __invoke()
    {
        // ...
    }

    public function handle()
    {
        return [];
    }

    public function htmlResponse($result, $request)
    {
        return view('errors.coming-soon', ['title' => 'Sorry about that']);
    }
}
