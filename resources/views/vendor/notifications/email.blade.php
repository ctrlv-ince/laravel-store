<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notification' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1a202c;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4299e1;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Tech Store Logo">
        </div>
        <div class="content">
            @component('mail::message')
            # {{ $greeting }}

            {{ $introLines[0] }}

            @if (isset($actionText))
            @component('mail::button', ['url' => $actionUrl])
            {{ $actionText }}
            @endcomponent
            @endif

            {{ $outroLines[0] }}

            @if (isset($salutation))
            {{ $salutation }}
            @else
            @lang('Regards'),<br>
            {{ config('app.name') }}
            @endif

            @if (isset($subcopy))
            @slot('subcopy')
            @component('mail::subcopy')
            {{ $subcopy }}
            @endcomponent
            @endslot
            @endif
            @endcomponent
        </div>
        <div class="footer">
            © {{ date('Y') }} Tech Store. All rights reserved.
        </div>
    </div>
</body>
</html> 