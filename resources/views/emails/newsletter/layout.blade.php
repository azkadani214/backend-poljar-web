<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            line-height: 1.6; 
            color: #002B5B; 
            margin: 0; 
            padding: 0; 
            background-color: #F8FAFC;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #F8FAFC;
            padding: 48px 0;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            border: 1px solid #E2E8F0;
        }
        .header { 
            text-align: center; 
            padding: 40px 20px; 
            background-color: #ffffff;
            border-bottom: 2px solid #F1F5F9;
        }
        .logo-img {
            max-width: 160px;
            height: auto;
        }
        .logo-text { 
            font-size: 24px; 
            font-weight: 800; 
            color: #002B5B; 
            letter-spacing: -0.025em;
            text-decoration: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .content { 
            padding: 48px 40px; 
            background-color: #ffffff;
        }
        .footer { 
            text-align: center; 
            font-size: 13px; 
            color: #64748B; 
            padding: 40px 20px; 
            background-color: #F8FAFC;
            border-top: 1px solid #E2E8F0;
        }
        .button { 
            display: inline-block; 
            padding: 14px 32px; 
            background-color: #FF8C42; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 16px;
            margin: 24px 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
        }
        .social-links {
            margin: 24px 0;
        }
        .social-links a {
            margin: 0 12px;
            text-decoration: none;
            color: #002B5B;
            font-weight: 600;
        }
        h1, h2, h3 { 
            color: #002B5B; 
            font-weight: 800; 
            margin-bottom: 24px; 
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        h1 { font-size: 28px; }
        h2 { font-size: 24px; }
        p { margin-bottom: 18px; font-size: 16px; color: #334155; }
        .divider { height: 2px; background-color: #F1F5F9; margin: 32px 0; }
        .text-muted { color: #64748B; font-size: 14px; }
        .text-orange { color: #FF8C42; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                @php
                    $logoPath = public_path('img/poljar-logo.png');
                    $logoUrl = file_exists($logoPath) ? asset('img/poljar-logo.png') : null;
                @endphp
                
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" class="logo-img">
                @else
                    <a href="{{ config('app.frontend_url') }}" class="logo-text">{{ config('app.name') }}</a>
                @endif
            </div>
            
            <div class="content">
                @yield('content')
            </div>
            
            <div class="footer">
                <p style="font-weight: 700; color: #002B5B; margin-bottom: 8px;">{{ config('app.name') }}</p>
                <p style="font-size: 12px; margin-bottom: 16px;">Politeknik Negeri Malang, Jawa Timur, Indonesia</p>
                
                <div class="social-links">
                    <a href="https://instagram.com/polinemamengajar">Instagram</a>
                    <a href="{{ config('app.frontend_url') }}">Website</a>
                </div>
                
                <div class="divider"></div>
                
                <p style="font-size: 11px; line-height: 1.5;">
                    Anda menerima email ini karena Anda berlangganan buletin kami.<br>
                    @yield('footer_links')
                </p>
                <p style="font-size: 11px; margin-top: 16px;">&copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html>
