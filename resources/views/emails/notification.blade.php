@component('mail::message')
<b>{{ $notification->title ?? 'New Notification!!' }}</b>

{{ $notification->message }}
@endforeach

Thanks,  
{{ config('app.name') }}
@endcomponent