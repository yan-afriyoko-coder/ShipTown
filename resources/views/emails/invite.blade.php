@component('mail::message')
# User Invite

You have been invited to join {{ config('app.name') }}.

@component('mail::button', ['url' => route('accept', $invite->token)])
Accept Invite
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
