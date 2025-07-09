<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $invoice->invoice_number }}</title>
    {{-- For better Arabic font rendering, consider adding a font like Google's "Cairo" --}}
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com"> --}}
    {{-- <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> --}}
    {{-- <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet"> --}}
    <style>
        body {
            /* font-family: 'Cairo', sans-serif; /* Use this if you add the Google Font link */
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #555;
            background-color: #f4f5f7; /* Gray background */
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            border: 1px solid #ddd; /* A subtle border instead of shadow */
            background-color: #fff;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: right; /* Default text alignment for RTL */
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 8px;
            vertical-align: top;
        }
        .invoice-box .logo {
            max-width: 200px;
            max-height: 100px;
        }
        .invoice-box .invoice-details {
            text-align: right; /* Invoice details are better on the left in RTL */
        }
        .invoice-box .heading td {
            background: #195e6c;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            color: #fff;
            padding: 12px 8px;
        }
        .invoice-box .item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box .grand-total td {
            border-top: 2px solid #195e6c;
            font-weight: bold;
            color: #195e6c;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            display: inline-block;
        }
        .status-paid { background-color: #28a745; }
        .status-sent { background-color: #ffc107; color: #333; }
        .status-draft { background-color: #6c757d; }
        .status-overdue { background-color: #dc3545; }

        @media only screen and (max-width: 600px) {
            .invoice-box .invoice-details,
            .invoice-box .information-section td {
                text-align: right !important;
            }
        }

        * {
            font-family: "Almarai", sans-serif !important;
        }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="invoice-box">
        <table>
            <!-- Header Section -->
            <tr>
                <td class="invoice-details">
                    <h1 style="color: #195e6c; margin: 0;">فاتورة</h1>
                    <div><strong>رقم الفاتورة:</strong> {{ $invoice->invoice_number }}</div>
                    <div><strong>رقم الطلب:</strong> {{ $invoice->order->track_code }}</div>
                    <div><strong>التاريخ:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</div>
                </td>
                <td style="width: 50%; text-align: left;">
                    <img src="/assets/images/full-logo.png" alt="شعار محلات علي ابو مسعود" class="logo">
                </td>
            </tr>

            <!-- Information Section -->
            <tr>
                <td colspan="2" style="padding-bottom: 40px;">
                    <table>
                        <tr>
                            <td style="width: 50%;">
                                <strong>إلى:</strong><br>
                                {{ $invoice->order->customer_name }}<br>
                                {{ $invoice->order->customer_email }}<br>
                                {{ $invoice->order->customer_phone }}
                            </td>
                            <td>
                                <strong>عنوان الشحن:</strong><br>
                                {{ $invoice->order->customer_name }}<br>
                                {{ $invoice->order->address_line_1 }}<br>
                                @if($invoice->order->address_line_2)
                                    {{ $invoice->order->address_line_2 }}<br>
                                @endif
                                {{ $invoice->order->city->name ?? '' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Payment Details Section -->
            <tr class="heading">
                <td>طريقة الدفع</td>
                <td style="text-align: left;">الحالة</td>
            </tr>
            <tr class="item">
                <td>{{ $invoice->order->payments->first()->paymentMethod->name ?? 'غير محدد' }}</td>
                <td style="text-align: left;">
                    @php
                        $statusText = $invoice->status;
                        $statusClass = 'status-draft';
                        if ($invoice->status === 'paid') { $statusClass = 'status-paid'; $statusText = 'مدفوعة'; }
                        if ($invoice->status === 'sent') { $statusClass = 'status-sent'; $statusText = 'مرسلة'; }
                        if ($invoice->status === 'draft') { $statusClass = 'status-draft'; $statusText = 'مسودة'; }
                        if ($invoice->status === 'overdue') { $statusClass = 'status-overdue'; $statusText = 'متأخرة'; }
                    @endphp
                    <span class="status {{ $statusClass }}">{{ $statusText }}</span>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table style="margin-top: 30px;">
            <tr class="heading">
                <td>المنتج</td>
                <td style="text-align: center; width: 15%;">الكمية</td>
                <td style="text-align: left; width: 20%;">سعر الوحدة</td>
                <td style="text-align: left; width: 20%;">الإجمالي</td>
            </tr>
            @foreach($invoice->order->items as $item)
                <tr class="item">
                    <td>
                        <strong>{{ $item->product_name }}</strong><br>
                        <small style="color: #777;">رمز المنتج: {{ $item->product_sku ?? 'غير متوفر' }}</small>
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: left;">{{ number_format($item->price, 2) }} ر.س</td>
                    <td style="text-align: left;">{{ number_format($item->price * $item->quantity, 2) }} ر.س</td>
                </tr>
            @endforeach
        </table>

        <!-- Totals Section -->
        <table style="margin-top: 20px;">
             <tr>
                <td style="width: 60%;"></td>
                <td style="width: 20%; text-align: left;">المجموع الفرعي:</td>
                <td style="width: 20%; text-align: left;">{{ number_format($invoice->subtotal, 2) }} ر.س</td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: left;">رسوم التوصيل:</td>
                <td style="text-align: left;">{{ number_format($invoice->delivery_fee, 2) }} ر.س</td>
            </tr>
             @if($invoice->discount_amount > 0)
                <tr>
                    <td></td>
                    <td style="text-align: left;">الخصم:</td>
                    <td style="text-align: left;">-{{ number_format($invoice->discount_amount, 2) }} ر.س</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td></td>
                <td style="text-align: left; font-size: 1.2em;"><strong>المبلغ الإجمالي:</strong></td>
                <td style="text-align: left; font-size: 1.2em;"><strong>{{ number_format($invoice->total_amount, 2) }} ر.س</strong></td>
            </tr>
        </table>

        <!-- Notes and QR Code Section -->
        <table style="margin-top: 40px;">
            <tr>
                <td class="qr-code">
                    @php
                        $trackingUrl = url('/track-order/' . $invoice->order->track_code);
                        echo \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($trackingUrl);
                    @endphp
                    <br>
                    <small>تتبع طلبك</small>
                </td>
                <td style="width: 60%;">
                    <strong>ملاحظات:</strong><br>
                    <p style="font-size: 12px; color: #777;">
                        نشكركم على تسوقكم معنا. إذا كان لديكم أي استفسار بخصوص هذه الفاتورة، يرجى التواصل مع فريق الدعم لدينا عبر البريد الإلكتروني support@aamstore.com.
                    </p>
                </td>
            </tr>
        </table>

        <!-- Footer Section -->
        <div class="footer">
            محلات علي ابو مسعود © {{ date('Y') }} | ١٢٣ طريق التجارة، مدينة التجارة | aamstore.com
        </div>
    </div>
</body>
</html>