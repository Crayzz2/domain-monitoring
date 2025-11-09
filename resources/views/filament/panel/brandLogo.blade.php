@php
    $configuration = App\Models\Configuration::first();
@endphp
@if($configuration)
    <div class="flex gap-5 items-center space-x-4">
        @if($configuration->company_logo)
            <img class="h-10" src="{{asset('storage/' . $configuration->company_logo)}}">
        @endif
        <p class="text-lg font-bold px-2">{{$configuration?->company_name}}</p>
    </div>
@endif
