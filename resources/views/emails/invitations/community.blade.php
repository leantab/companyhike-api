@component('mail::header', ['url' => $url])
Company Hike
@endcomponent

@component('mail::message')
# Hola!

Fuiste invitado/a a participar de la comunidad
{{ $community->name }}
en CompanyHike

@component('mail::button', ['url' => $url, 'color' => 'success'])
Aceptar invitación
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
