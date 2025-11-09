@php
    $configuration = App\Models\Configuration::first();
@endphp
<div>
    <p>{{$configuration?->company_name}}</p>
</div>
