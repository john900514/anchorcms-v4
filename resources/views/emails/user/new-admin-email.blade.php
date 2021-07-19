@php
    $timezone = $new_user->timezone()->first();
    if(is_null($timezone))
    {
        $timezone = 'America/New_York';
    }
    else
    {
        $timezone = $timezone->value;
    }
    $processed_date = new \DateTime(date('Y-m-d H:i:s', strtotime($date)));
    $processed_date->setTimezone(new \DateTimeZone($timezone));
@endphp
<div id="email" style="margin-top: 1em;">
    <p>Dear {!! $new_user->name !!},</p>

    <br />
    @if(!is_null($modifier))
        <p>An account was created for you on {!! $processed_date->format('h:i A') !!} by {!! $modify_user->name !!}.</p>
    @else
        <p>An account was created for you on {!! $processed_date->format('h:i A') !!}.</p>
    @endif
    <br />

    <p>You will need to complete your registration in order to access your account.</p>
    <p> Click <a href="{!! env('APP_URL') !!}/registration?session={!! $new_user->id !!}">here</a> or paste the URL below into your browser to begin!</p>
    <p><a href="{!! env('APP_URL') !!}/registration?session={!! $new_user->id !!}">{!! env('APP_URL') !!}/registration?session={!! $new_user->id !!}</a></p>

    <br />
    <br />

    <p> Welcome.</p>

    <br />
    <br />

    <p style="margin-left: 2em;">Best,</p>

    <br />
    <br />

    <p style="margin-left: 2em;"><b>Cape & Bay Dev Team</b></p>

    <br />
    <br />
    @if(env('APP_ENV') != 'production')
        <p><b>NOTICE: This is a test email using test data. Do not follow up.</b></p>
    @endif
</div>
