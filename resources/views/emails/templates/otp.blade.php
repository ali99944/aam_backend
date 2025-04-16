<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق الخاص بك</title>
    <style>
        body { font-family: 'Cairo', 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; direction: rtl; text-align: right; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .header { background-color: #1abc9c; padding: 25px 30px; text-align: center; }
        .header img { max-width: 150px; height: auto; }
        .content { padding: 30px 40px; color: #333333; line-height: 1.7; }
        .content h1 { color: #1abc9c; font-size: 22px; margin-top: 0; margin-bottom: 15px; }
        .content p { margin-bottom: 15px; font-size: 15px; }
        .otp-code { display: inline-block; background-color: #e0f2f1; color: #00796b; font-size: 28px; font-weight: bold; padding: 12px 25px; border-radius: 6px; margin: 15px 0; letter-spacing: 3px; border: 1px dashed #a7d7d7; }
        .warning { font-size: 13px; color: #777; border-top: 1px solid #eeeeee; padding-top: 15px; margin-top: 20px; }
        .footer { background-color: #f4f7f6; padding: 20px 40px; text-align: center; font-size: 12px; color: #7f8c8d; border-top: 1px solid #e1e4e8; }
        .footer a { color: #1abc9c; text-decoration: none; }
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap'); /* Ensure Cairo font */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Make sure the logo path is correct and accessible publicly --}}
            <img src="http://localhost" alt="{{ config('app.name') }} Logo">
             {{-- Or use asset() if hosted publicly: <img src="{{ asset('images/logo_email_white.png') }}" alt="{{ config('app.name') }} Logo"> --}}
        </div>
        <div class="content">
            <h1>رمز التحقق الخاص بك</h1>
            <p>مرحبًا {{ $name ?? 'عميلنا العزيز' }},</p>
            <p>الرجاء استخدام رمز التحقق التالي لإكمال عمليتك. الرمز صالح لمدة {{ $validityMinutes ?? '10' }} دقائق.</p>

            <p style="text-align: center;">
                <span class="otp-code">{{ $otpCode }}</span>
            </p>

            <p>إذا لم تطلب هذا الرمز، فيمكنك تجاهل هذا البريد الإلكتروني بأمان.</p>

            <p class="warning">
                لا تشارك هذا الرمز مع أي شخص آخر لحماية حسابك.
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة. <br>
            {{-- Add contact info or link to website --}}
            <a href="{{ url('/') }}">زيارة موقعنا</a>
        </div>
    </div>
</body>
</html>