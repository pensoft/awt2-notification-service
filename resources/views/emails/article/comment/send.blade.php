@component('mail::message')
# {{$subject}}

{{ $message }}

@component('mail::button', ['url' => $link])
{{ $article_title }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
