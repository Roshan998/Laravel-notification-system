@component('mail::message')
<b>{{ $notification->title ?? 'New Notification!!' }}</b>

{{ $notification->message }}


Thanks,  
{{ config('app.name') }}
@endcomponent