<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'محلات علي ابو مسعود')</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 14px; line-height: 1.8; color: #555; background-color: #f4f5f7; margin: 0; padding: 0;
        }
        .email-wrapper { width: 100%; background-color: #f4f5f7; padding: 20px 0; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #ddd; }
        .email-header { background-color: #195e6c; padding: 20px; text-align: center; }
        .email-header img { max-width: 180px; }
        .email-body { padding: 30px; }
        .email-footer { background-color: #f4f5f7; padding: 20px; text-align: center; font-size: 12px; color: #777; }
        h1, h2, h3 { color: #195e6c; }
        p { margin-bottom: 15px; }
        .button {
            display: inline-block;
            background-color: #195e6c;
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .order-summary-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .order-summary-table th, .order-summary-table td { text-align: right; padding: 10px; border-bottom: 1px solid #eee; }
        .order-summary-table th { background-color: #f9f9f9; font-weight: bold; }
        .align-left { text-align: left !important; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <a href="{{ url('/') }}" target="_blank">
                    <img src="{{ public_path('assets/images/logo-white.png') }}" alt="شعار محلات علي ابو مسعود">
                    {{-- Note: Create a white version of your logo for dark backgrounds --}}
                </a>
            </div>

            <!-- Body -->
            <div class="email-body">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>© {{ date('Y') }} محلات علي ابو مسعود. جميع الحقوق محفوظة.</p>
                @hasSection('unsubscribe')
                    <p><a href="#unsubscribe-link">إلغاء الاشتراك</a></p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>