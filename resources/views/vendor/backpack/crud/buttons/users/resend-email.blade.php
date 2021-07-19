@if(is_null($entry->email_verified_at))
    <a href="{!! backpack_url('/users/resend-email/'.$entry->id) !!}" class="btn btn-sm btn-link"><i class="las la-envelope-open-text"></i> Resend Email</a>
@endif
