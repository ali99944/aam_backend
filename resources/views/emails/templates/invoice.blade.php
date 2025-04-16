<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة طلب #{{ $order->id }}</title>
    <style>
        body { font-family: 'Cairo', 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; direction: rtl; text-align: right; font-size: 14px; color: #333; }
        .container { width: 100%; max-width: 800px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #1abc9c;}
        .header img { max-width: 150px; height: auto; }
        .invoice-details { text-align: left; } /* LTR for invoice numbers/dates */
        .invoice-details h2 { margin: 0 0 5px 0; color: #1abc9c; font-size: 24px; }
        .invoice-details p { margin: 2px 0; font-size: 13px; color: #555; direction: ltr; /* Keep LTR */ }
        .addresses { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .addresses div { width: 48%; line-height: 1.6; }
        .addresses h3 { margin-top: 0; margin-bottom: 10px; font-size: 16px; color: #1abc9c; border-bottom: 1px solid #eee; padding-bottom: 5px;}
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border-bottom: 1px solid #eeeeee; padding: 10px 8px; text-align: right; }
        .items-table thead th { background-color: #f8f9fa; font-weight: bold; color: #333; font-size: 13px; border-bottom-width: 2px; border-color: #dee2e6; }
        .items-table tbody td { vertical-align: top; font-size: 14px; }
        .items-table .item-name { font-weight: bold; }
        .items-table .item-desc { font-size: 0.9em; color: #666; }
        .items-table .price, .items-table .quantity, .items-table .total { text-align: center; width: 15%; }
        .items-table .total { font-weight: bold; }
        .totals { width: 100%; max-width: 350px; margin-left: auto; /* Push to left in LTR, stays left in RTL */ margin-right: 0; } /* Adjust if needed for RTL */
        .totals table { width: 100%; }
        .totals td { padding: 8px 10px; font-size: 14px; }
        .totals tr:last-child td { font-weight: bold; font-size: 16px; border-top: 2px solid #333; }
        .payment-info { margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee; font-size: 14px; }
        .notes { margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 4px; font-size: 13px; color: #555; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #7f8c8d; }
        .footer a { color: #1abc9c; }
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div>
                 <img src="{{ $message->embed(public_path('images/logo_email.png')) }}" alt="{{ config('app.name') }} Logo">
                 {{-- Display Company Address --}}
                 <p style="font-size: 13px; margin-top: 10px; color: #555;">
                     {{ $settings['company_address_line1'] ?? 'Your Company Address Line 1' }}<br>
                     {{ $settings['company_address_line2'] ?? 'City, Country' }}<br>
                     {{ $settings['company_phone'] ?? 'Your Phone' }} | {{ $settings['company_email'] ?? 'Your Email' }}
                 </p>
            </div>
            <div class="invoice-details">
                 <h2>فاتورة</h2>
                 <p><strong>رقم الفاتورة:</strong> #{{ $invoice->invoice_number ?? 'N/A' }}</p>
                 <p><strong>تاريخ الفاتورة:</strong> {{ $invoice->invoice_date->format('Y-m-d') ?? 'N/A' }}</p>
                 <p><strong>رقم الطلب:</strong> #{{ $order->id }}</p>
                 {{-- Add Due Date if applicable --}}
                 @if($invoice->due_date)
                     <p><strong>تاريخ الاستحقاق:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>
                 @endif
                 {{-- Optional: Payment Status --}}
                  <p><strong>حالة الدفع:</strong> {{ $invoice->payment ? ($invoice->payment::statuses()[$invoice->payment->status] ?? ucfirst($invoice->payment->status)) : 'غير مدفوعة' }}</p>
            </div>
        </div>

        {{-- Addresses --}}
        <div class="addresses">
            <div>
                <h3>فاتورة إلى:</h3>
                <p>
                    <strong>{{ $order->customer->name ?? 'Customer Name' }}</strong><br>
                    {{-- Assuming you have address details linked to the order or customer --}}
                    {{ $order->billing_address->address_line_1 ?? 'Address Line 1' }}<br>
                     @if($order->billing_address->address_line_2 ?? null)
                         {{ $order->billing_address->address_line_2 }}<br>
                     @endif
                     {{ $order->billing_address->city ?? 'City' }}, {{ $order->billing_address->country ?? 'Country' }}<br>
                     {{ $order->customer->email ?? 'customer@email.com' }}<br>
                     {{ $order->customer->phone ?? 'Customer Phone' }}
                </p>
            </div>
            <div>
                 <h3>شحن إلى:</h3>
                <p>
                     {{-- Use shipping address details --}}
                     <strong>{{ $order->shipping_address->name ?? $order->customer->name ?? 'Customer Name' }}</strong><br>
                     {{ $order->shipping_address->address_line_1 ?? 'Address Line 1' }}<br>
                      @if($order->shipping_address->address_line_2 ?? null)
                         {{ $order->shipping_address->address_line_2 }}<br>
                      @endif
                      {{ $order->shipping_address->city ?? 'City' }}, {{ $order->shipping_address->country ?? 'Country' }}<br>
                      {{ $order->shipping_address->phone ?? $order->customer->phone ?? 'Customer Phone' }}
                 </p>
            </div>
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th class="quantity">الكمية</th>
                    <th class="price">سعر الوحدة</th>
                    <th class="total">المجموع</th>
                </tr>
            </thead>
            <tbody>
                 @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product->name ?? 'Product Name' }}</div>
                         {{-- Add variation/addon info here if applicable --}}
                         {{-- <div class="item-desc">Color: Red, Size: L</div> --}}
                    </td>
                    <td class="quantity">{{ $item->quantity }}</td>
                    <td class="price">AED {{ number_format($item->price, 2) }}</td>
                    <td class="total">AED {{ number_format($item->total, 2) }}</td>
                </tr>
                 @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals">
             <table>
                <tr><td>المجموع الفرعي:</td><td style="text-align: left;">AED {{ number_format($invoice->subtotal ?? 0, 2) }}</td></tr>
                 {{-- Add discount row if applicable --}}
                 @if(($invoice->discount_amount ?? 0) > 0)
                    <tr><td>الخصم:</td><td style="text-align: left; color: #e74c3c;">- AED {{ number_format($invoice->discount_amount, 2) }}</td></tr>
                 @endif
                 {{-- Add tax row if applicable --}}
                  @if(($invoice->tax_amount ?? 0) > 0)
                    <tr><td>الضريبة:</td><td style="text-align: left;">AED {{ number_format($invoice->tax_amount, 2) }}</td></tr>
                  @endif
                <tr><td>رسوم التوصيل:</td><td style="text-align: left;">AED {{ number_format($invoice->delivery_fee ?? 0, 2) }}</td></tr>
                <tr><td><strong>المجموع الكلي:</strong></td><td style="text-align: left;"><strong>AED {{ number_format($invoice->total_amount ?? $order->total, 2) }}</strong></td></tr>
            </table>
        </div>

        {{-- Payment Information --}}
         <div class="payment-info">
             <strong>طريقة الدفع:</strong> {{ $order->payment_method ?? 'N/A' }} <br>
              @if($invoice->payment && $invoice->payment->transaction_id)
                <small><strong>معرف العملية:</strong> {{ $invoice->payment->transaction_id }}</small>
              @endif
         </div>

        {{-- Notes/Terms --}}
         @if($invoice->notes)
         <div class="notes">
             <strong>ملاحظات:</strong><br>
             {{ $invoice->notes }}
         </div>
         @endif
         {{-- Add standard terms here --}}
         {{-- <div class="notes"><strong>Terms:</strong> Payment due within 30 days.</div> --}}


        {{-- Footer --}}
        <div class="footer">
             شكرًا لتعاملك معنا!<br>
            إذا كانت لديك أي أسئلة، فيرجى التواصل معنا.<br>
             <a href="{{ url('/') }}">{{ config('app.name') }}</a>
        </div>

    </div>
</body>
</html>