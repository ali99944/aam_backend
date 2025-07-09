<?php

namespace App\Notifications;

// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// class SendOtpNotification extends Notification implements ShouldQueue
class SendOtpNotification extends Notification
{

    protected string $otpCode;
    protected string $purpose;

    /**
     * Create a new notification instance.
     * @param string $otpCode The 6-digit OTP code.
     * @param string $purpose A friendly name for the purpose, e.g., 'Password Reset'.
     */
    public function __construct(string $otpCode, string $purpose = 'Password Reset')
    {
        $this->otpCode = $otpCode;
        $this->purpose = $purpose;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "رمز التحقق لـ {$this->purpose} - متجر AAM";
        $greeting = "مرحباً،";
        $introLine = "لقد طلبت رمز تحقق لـ " . strtolower($this->purpose) . ". استخدم الرمز أدناه لإكمال عمليتك.";
        $outroLine = "هذا الرمز صالح لمدة 10 دقائق. إذا لم تقم بطلب هذا الرمز، يمكنك تجاهل هذه الرسالة بأمان.";
        $actionText = 'زيارة الموقع';

        // Customize messages for password reset
        if ($this->purpose === 'Password Reset') {
            $introLine = "لقد طلبت إعادة تعيين كلمة المرور الخاصة بك. استخدم الرمز أدناه للتحقق من هويتك والمتابعة.";
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($introLine)
                    ->line("رمز التحقق الخاص بك هو:")
                    ->line(new \Illuminate\Support\HtmlString("<div style='font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center; margin: 20px 0; padding: 10px; background-color: #f4f4f4; border-radius: 5px;'>{$this->otpCode}</div>"))
                    ->line($outroLine)
                    ->action($actionText, url('/'))
                    ->salutation('مع أطيب التحيات،<br>فريق متجر AAM');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}