<x-mail::message>
    {{-- Greeting --}}
    @if (!empty($greeting))
        # {{ $greeting }}
    @else
        @if ($level === 'error')
            # @lang('Oops!')
        @else
            # @lang('Hello,')
        @endif
    @endif

    {{-- Intro Lines --}}
    @foreach ($introLines as $line)
        {{ $line }}
    @endforeach

    {{-- Action Button --}}
    @isset($actionText)
        <?php
        // Customize button color logic
        $color = match ($level) {
            'success' => 'green',
            'error' => 'red',
            default => 'blue',
        };
        ?>
        <x-mail::button :url="$actionUrl" :color="$color"
            style="border-radius: 8px; padding: 12px 24px; font-weight: bold;">
            {{ $actionText }}
        </x-mail::button>
    @endisset

    {{-- Outro Lines --}}
    @foreach ($outroLines as $line)
        {{ $line }}
    @endforeach

    {{-- Salutation --}}
    @if (!empty($salutation))
        {{ $salutation }}
    @else
        @lang('Regards,')<br>
        <strong>{{ config('app.name') }}</strong>
    @endif

    {{-- Subcopy --}}
    @isset($actionText)
        <x-slot:subcopy>
            @lang("If you're having trouble clicking the \":actionText\" button, copy and paste the URL below into your web browser:", ['actionText' => $actionText])
            <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
        </x-slot:subcopy>
    @endisset

    {{-- Optional Footer Logo --}}
    <x-slot:footer>
        <div style="text-align: center; margin-top: 20px;">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" width="120">
            <p style="font-size: 12px; color: #888;">© {{ date('Y') }} {{ config('app.name') }}. All rights
                reserved.</p>
        </div>
    </x-slot:footer>

</x-mail::message>
