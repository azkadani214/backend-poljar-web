<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #FF8C42; }
        .content { padding: 30px 0; }
        .footer { text-align: center; font-size: 12px; color: #999; padding: 20px 0; border-top: 1px solid #eee; }
        .button { display: inline-block; padding: 12px 24px; background-color: #FF8C42; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .logo { font-size: 24px; font-weight: bold; color: #002B5B; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Polinema Mengajar</div>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Polinema Mengajar. All rights reserved.</p>
            <p>Malang, Jawa Timur, Indonesia</p>
            @yield('footer_links')
        </div>
    </div>
</body>
</html>
