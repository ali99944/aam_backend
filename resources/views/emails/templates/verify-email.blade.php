<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد عنوان بريدك الإلكتروني</title>
     <style>
        body { font-family: 'Cairo', 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; direction: rtl; text-align: right; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .header { background-color: #1abc9c; padding: 25px 30px; text-align: center; }
        .header img { max-width: 150px; height: auto; }
        .content { padding: 30px 40px; color: #333333; line-height: 1.7; }
        .content h1 { color: #1abc9c; font-size: 22px; margin-top: 0; margin-bottom: 15px; }
        .content p { margin-bottom: 15px; font-size: 15px; }
        .button-container { text-align: center; margin: 25px 0; }
        .button { display: inline-block; background-color: #1abc9c; color: #ffffff !important; /* Important for email clients */ padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 16px; border: none; cursor: pointer; }
        .link-info { font-size: 13px; color: #777; word-break: break-all; margin-top: 20px; }
        .footer { background-color: #f4f7f6; padding: 20px 40px; text-align: center; font-size: 12px; color: #7f8c8d; border-top: 1px solid #e1e4e8; }
        .footer a { color: #1abc9c; text-decoration: none; }
         @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
    </style>
</head>
<body>
     <div class="container">
        <div class="header">
            <img src="" alt="{{ config('app.name') }} Logo">
        </div>
        <div class="content">
            <h1>تأكيد عنوان بريدك الإلكتروني</h1>
            <p>مرحبًا {{ $name ?? 'عميلنا العزيز' }},</p>
            <p>شكرًا لتسجيلك! الرجاء النقر على الزر أدناه لتأكيد عنوان بريدك الإلكتروني والبدء.</p>

            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="button" target="_blank" style="color: #ffffff;">تأكيد البريد الإلكتروني</a>
            </div>

            <p>إذا لم تقم بإنشاء حساب، فلا داعي لاتخاذ أي إجراء آخر.</p>

             <p class="link-info">
                إذا كنت تواجه مشكلة في النقر على الزر، انسخ الرابط التالي والصقه في متصفحك:
                <br>
                <a href="{{ $verificationUrl }}" target="_blank" style="color: #1abc9c;">{{ $verificationUrl }}</a>
            </p>
        </div>
        <div class="footer">
             © {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة. <br>
            <a href="{{ url('/') }}">زيارة موقعنا</a>
        </div>
    </div>
</body>
</html>