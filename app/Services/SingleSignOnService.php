<?php

namespace App\Services;

use App\Models\Auth\SsoIntegrations;

class SingleSignOnService
{
    protected $sso_model;

    public function __construct(SsoIntegrations $sso)
    {
        $this->sso_model = $sso;
    }

    public function getIntegrationWithSlug(string $slug)
    {
        $results = false;

        $model = $this->sso_model->whereSlug($slug)->first();

        if(!is_null($model))
        {
            $results = $model->toArray();
        }

        return $results;
    }
}
