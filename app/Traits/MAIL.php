<?php

namespace App\Traits;
use App\Models\Order;
use App\Models\Stock;
// use PHPMailer\PHPMailer\PHPMailer;

trait MAIL
{
    public static function orderCreateEndEmail($booking_pk, $customer_email) {

        require base_path("vendor/autoload.php");

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port = config('mail.port');
        $mail->setFrom('sales@ukshop.my', 'AZURAMART');
        $mail->addAddress($customer_email,'AZURAMART');
        $mail->isHTML(true);

        $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $booking_pk)->first();
        $products   =  Stock::where('F_BOOKING_NO', $booking_pk)->get();

        // $mail_body = view('admin.Mail.order_place')
        $mail_body = view('admin.Mail.greeting2')
        ->with('products', $products)
        ->with('order_info', $order_info)
        ->render();

        $mail->Subject = 'Your Order Has been Placed in AZURAMART';
        $mail->Body    = $mail_body;

        $mail->send();
        return true;
    }

    public static function orderDispatchEmail($booking_pk, $customer_email) {

        require base_path("vendor/autoload.php");

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port = config('mail.port');
        $mail->setFrom('sales@ukshop.my', 'AZURAMART');
        $mail->addAddress($customer_email,'AZURAMART');
        $mail->isHTML(true);

        $products   =  Stock::where('F_BOOKING_NO', $booking_pk)->get();
        $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $booking_pk)->first();

        $mail_body = view('admin.Mail.order_dispatch')
        ->with('products', $products)
        ->with('order_info', $order_info)
        ->render();

        $mail->Subject = 'Your Order has been dispatched';
        $mail->Body    = $mail_body;

        $mail->send();
        return true;
    }

    public static function orderCancelEmail($booking_pk, $customer_email) {

        require base_path("vendor/autoload.php");

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port = config('mail.port');
        $mail->setFrom('sales@ukshop.my', 'AZURAMART');
        $mail->addAddress($customer_email,'AZURAMART');
        $mail->isHTML(true);

        $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $booking_pk)->first();

        $mail_body = view('admin.Mail.order_cancel')
        ->with('order_info', $order_info)
        ->render();

        $mail->Subject = 'Your Order has been canceled';
        $mail->Body    = $mail_body;

        $mail->send();
        return true;
    }

    public static function orderDefaultEmail($booking_pk, $customer_email) {

        require base_path("vendor/autoload.php");

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port = config('mail.port');
        $mail->setFrom('sales@ukshop.my', 'AZURAMART');
        $mail->addAddress($customer_email,'AZURAMART');
        $mail->isHTML(true);

        $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $booking_pk)->first();

        $mail_body = view('admin.Mail.order_default')
        ->with('order_info', $order_info)
        ->render();

        $mail->Subject = 'Your Order has been default';
        $mail->Body    = $mail_body;

       $mail->send();
       return true;
    }

    public static function orderReturntEmail($booking_pk, $customer_email) {

        require base_path("vendor/autoload.php");

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port = config('mail.port');
        $mail->setFrom('sales@ukshop.my', 'AZURAMART');
        $mail->addAddress($customer_email,'AZURAMART');
        $mail->isHTML(true);

        $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $booking_pk)->first();

        $mail_body = view('admin.Mail.order_default')
        ->with('order_info', $order_info)
        ->render();

        $mail->Subject = 'Your Order has been default';
        $mail->Body    = $mail_body;

       $mail->send();
       return true;
    }

    public static function orderArrivalEmail($booking_pk,$send_to) {

        // require 'vendor/autoload.php';
        require base_path("vendor/autoload.php");

        $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $booking_pk)->first();

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 4;
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port = config('mail.port');
        $mail->setFrom('sales@azuramart.com', 'AZURAMART');
        $mail->addAddress($send_to,$order_info->CUSTOMER_NAME ?? $order_info->RESELLER_NAME);
        $mail->isHTML(true);

        $mail_body = view('admin.Mail.order_arrive')
        ->with('order_info', $order_info)
        ->render();

        $mail->Subject = 'Your Order has been arrived';
        $mail->Body    = $mail_body;

        $mail->send();
        return true;
    }

    public static function greetingEmail($cust_info) {

        require base_path("vendor/autoload.php");
        $customer_email = $cust_info->email;

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port = config('mail.port');
        $mail->setFrom('sales@ukshop.my', 'AZURAMART');
        $mail->addAddress($customer_email,'AZURAMART');
        $mail->isHTML(true);

        $mail_body = view('admin.Mail.greeting')
        ->with('cust_info', $cust_info)
        ->render();

        $mail->Subject = 'Greetings from AZURAMART with customer link';
        $mail->Body    = $mail_body;

        $mail->send();
        return true;
    }

    private function send_email($email, $sub, $msg)
    {
        $sub    = $sub;
        $msg    = $msg;
        $from   = "syedsifat02@gmail.com";

        $msgBody  = '<html><body>';

        $msgBody .=  "$msg" . '<br><br>';

        $msgBody .= '</html></body>';

        $headers  = "Form: " . strip_tags($from) . "\r\n";
        $headers .= "Reply-To: " . strip_tags($from) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $isSuccess = mail($email, $sub, $msgBody, $headers);

        return $isSuccess;
    }
}
