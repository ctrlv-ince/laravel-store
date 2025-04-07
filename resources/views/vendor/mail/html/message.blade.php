@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <div style="text-align: center;">
                <i class="fas fa-microchip" style="font-size: 40px; color: #4299e1;"></i>
                <div style="margin-top: 10px; font-size: 24px; font-weight: bold;">{{ config('app.name') }}</div>
            </div>
        @endcomponent
    @endslot

    {{-- Body --}}
    <div style="padding: 10px 0;">
        {{ $slot }}
    </div>

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <div style="margin-bottom: 10px;">
                <a href="{{ config('app.url') }}" style="text-decoration: none;">
                    <i class="fas fa-home" style="color: #4299e1; margin-right: 5px;"></i> Visit our store
                </a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="{{ config('app.url') }}/contact" style="text-decoration: none;">
                    <i class="fas fa-envelope" style="color: #4299e1; margin-right: 5px;"></i> Contact us
                </a>
            </div>
            <div>
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        @endcomponent
    @endslot
@endcomponent 