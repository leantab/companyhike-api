@component('mail::message')
# Hola!

Fuiste invitado/a a participar de una partida
en la comunidad {{ $community->name }}

@component('mail::button', ['url' => $url])
Aceptar invitación
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
