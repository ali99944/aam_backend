<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; // To check if view exists
// Import models needed for dummy data
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Carbon\Carbon;

class TemplateController extends Controller
{
    // Base path for email templates
    private $templateBasePath = 'emails.templates.';

    /**
     * Show index of available templates for preview
     */
    public function index() {
        // You can list available template keys here if needed
        $templates = [
            'otp' => 'OTP Email',
            'verify-email' => 'Email Verification',
            'invoice' => 'Order Invoice',
        ];
        return view('admin.templates.index', compact('templates')); // Create this index view
    }

    /**
     * Preview a specific email template.
     */
    public function preview(Request $request, string $templateKey) {
        $viewName = $this->templateBasePath . $templateKey;

        if (!View::exists($viewName)) {
            abort(404, "Template view '{$viewName}' not found.");
        }

        $data = $this->getSampleData($templateKey);

        try {
            // Render the view directly for preview
            return view($viewName, $data);
        } catch (\Exception $e) {
             // Catch potential errors in the template rendering itself
             return "Error rendering template '{$templateKey}': <pre>" . $e->getMessage() . "</pre>";
        }
    }


    /**
     * Helper function to generate sample data for previews.
     */
    private function getSampleData(string $templateKey): array
    {
        switch ($templateKey) {
            case 'otp':
                return [
                    'name' => 'أحمد محمد',
                    'otpCode' => '123456',
                    'validityMinutes' => 15,
                    'message' => 'test'
                ];

            case 'verify-email':
                 return [
                     'name' => 'فاطمة علي',
                     // Generate a dummy URL for preview
                     'message' => 'test',
                     'verificationUrl' => url('/email/verify/sample-id/sample-hash?expires=123&signature=abc'),
                 ];

            case 'invoice':
                // Create dummy related objects for a realistic preview
                $customer = (object) ['id' => 1, 'name' => 'شركة النجوم للتجارة', 'email' => 'billing@example.com', 'phone' => '+962 79 000 1111'];
                $billingAddress = (object) ['address_line_1' => 'شارع الملكة رانيا', 'address_line_2' => 'مبنى 55، الطابق 3', 'city' => 'عمان', 'country' => 'الأردن'];
                $shippingAddress = (object) ['name' => 'مستودع الشركة', 'address_line_1' => 'منطقة القسطل الصناعية', 'address_line_2' => 'قطعة رقم 10', 'city' => 'عمان', 'country' => 'الأردن', 'phone' => '+962 79 222 3333'];
                $product1 = (object) ['id' => 101, 'name' => 'جهاز عرض بروجكتور HD', 'sku_code' => 'PROJ-HD-01', 'main_image_url' => asset('images/placeholder-product.png')];
                $product2 = (object) ['id' => 102, 'name' => 'كابل HDMI طويل (5 متر)', 'sku_code' => 'HDMI-5M-02', 'main_image_url' => asset('images/placeholder-product.png')];

                $order = new Order([ // Use new Order to get accessors like formatted_total
                     'id' => 12345,
                     'customer_id' => $customer->id,
                     'created_at' => Carbon::now()->subDays(1),
                     'payment_method' => 'Credit Card (Stripe)',
                     'delivery_fee' => 5.00,
                     'total' => 467.50,
                     // Add other needed order fields if your template uses them
                 ]);
                // Manually set relationships for preview data
                 $order->setRelation('customer', $customer);
                 $order->setRelation('billing_address', $billingAddress); // Assumes you have this relation setup
                 $order->setRelation('shipping_address', $shippingAddress); // Assumes you have this relation setup

                $orderItems = collect([
                    (object) ['product' => $product1, 'quantity' => 1, 'price' => 450.00, 'total' => 450.00],
                    (object) ['product' => $product2, 'quantity' => 1, 'price' => 12.50, 'total' => 12.50],
                 ]);
                 $order->setRelation('items', $orderItems);

                 $invoice = new Invoice([
                     'id' => 678,
                     'order_id' => $order->id,
                     'invoice_number' => 'INV-2023-00123',
                     'invoice_date' => Carbon::now()->subDays(1)->toDateString(), // Needs casting in model
                     'due_date' => Carbon::now()->addDays(14)->toDateString(), // Needs casting in model
                     'subtotal' => 462.50,
                     'discount_amount' => 0.00,
                     'tax_amount' => 0.00,
                     'delivery_fee' => 5.00,
                     'total_amount' => 467.50,
                     'status' => Invoice::STATUS_SENT,
                     'notes' => 'يرجى ذكر رقم الفاتورة عند الدفع.',
                 ]);
                 // Manually set relationships for preview data
                  $invoice->setRelation('order', $order);
                  $order->setRelation('invoice', $invoice); // Link back for potential use

                 // Optional: Add dummy Payment data
                 $paymentMethod = (object)['name' => 'Visa **** 4242'];
                 $payment = new Payment([
                      'status' => Payment::STATUS_COMPLETED,
                      'amount' => 467.50,
                      'transaction_id' => 'ch_123abc456def',
                      'created_at' => Carbon::now()->subHours(2),
                 ]);
                 $payment->setRelation('paymentMethod', $paymentMethod);
                 $invoice->setRelation('payment', $payment);


                 // Sample 'settings' data
                  $settings = [
                     'company_address_line1' => 'مجموعة الأمل التجارية',
                     'company_address_line2' => 'عمان، الأردن',
                     'company_phone' => '+962 6 555 4444',
                     'company_email' => 'info@aamstore.com', // Use your actual details
                 ];

                 return [
                     'order' => $order,
                     'invoice' => $invoice,
                      'settings' => $settings, // Pass settings array
                 ];

            default:
                return []; // Default empty data
        }
    }
}