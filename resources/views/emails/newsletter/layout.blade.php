<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        body { 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            line-height: 1.6; 
            color: #1a202c; 
            margin: 0; 
            padding: 0; 
            background-color: #f7fafc;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f7fafc;
            padding: 40px 0;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header { 
            text-align: center; 
            padding: 40px 20px; 
            background: linear-gradient(135deg, #002B5B 0%, #004d80 100%);
        }
        .logo-img {
            max-width: 180px;
            height: auto;
        }
        .logo-text { 
            font-size: 28px; 
            font-weight: 800; 
            color: #ffffff; 
            letter-spacing: -0.025em;
            text-decoration: none;
        }
        .content { 
            padding: 40px; 
        }
        .footer { 
            text-align: center; 
            font-size: 13px; 
            color: #718096; 
            padding: 30px 20px; 
            background-color: #edf2f7;
        }
        .button { 
            display: inline-block; 
            padding: 14px 32px; 
            background-color: #FF8C42; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: 700; 
            font-size: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(255, 140, 66, 0.25);
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #4a5568;
        }
        h1 { font-size: 24px; font-weight: 700; margin-bottom: 20px; color: #2d3748; }
        p { margin-bottom: 16px; }
        .divider { height: 1px; background-color: #e2e8f0; margin: 30px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                @php
                    $logoPath = public_path('img/logo-mail.png');
                    $logoUrl = file_exists($logoPath) ? asset('img/logo-mail.png') : null;
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
                <p>&copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. Seluruh hak cipta dilindungi.</p>
                <p>Politeknik Negeri Malang, Jawa Timur, Indonesia</p>
                
                <div class="social-links">
                    <a href="https://instagram.com/polinemamengajar">Instagram</a>
                    <a href="{{ config('app.frontend_url') }}">Website</a>
                </div>
                
                <div class="divider"></div>
                
                <p style="font-size: 11px;">Anda menerima email ini karena Anda berlangganan buletin kami. <br> 
                @yield('footer_links')</p>
            </div>
        </div>
    </div>
</body>
</html>
