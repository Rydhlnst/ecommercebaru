<?php

namespace Beres\Notification\Listeners;

use Beres\Notification\Events\OrderCreatedNotification;
use Beres\Notification\Events\PaymentSuccessNotification;
use Beres\Notification\Events\OrderShippedNotification;
use Beres\Notification\Events\OrderCompletedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderNotification
{
    /**
     * Handle order created notification.
     */
    public function handleOrderCreated(OrderCreatedNotification $event): void
    {
        try {
            $order = $event->order;
            $customer = $order->customer;

            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(
                    new \Beres\Notification\Mail\OrderCreatedMail($order)
                );

                Log::info("Order created notification sent to: {$customer->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send order created notification: " . $e->getMessage());
        }
    }

    /**
     * Handle payment success notification.
     */
    public function handlePaymentSuccess(PaymentSuccessNotification $event): void
    {
        try {
            $order = $event->order;
            $customer = $order->customer;

            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(
                    new \Beres\Notification\Mail\PaymentSuccessMail($order)
                );

                Log::info("Payment success notification sent to: {$customer->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send payment success notification: " . $e->getMessage());
        }
    }

    /**
     * Handle order shipped notification.
     */
    public function handleOrderShipped(OrderShippedNotification $event): void
    {
        try {
            $order = $event->order;
            $customer = $order->customer;

            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(
                    new \Beres\Notification\Mail\OrderShippedMail($order)
                );

                Log::info("Order shipped notification sent to: {$customer->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send order shipped notification: " . $e->getMessage());
        }
    }

    /**
     * Handle order completed notification.
     */
    public function handleOrderCompleted(OrderCompletedNotification $event): void
    {
        try {
            $order = $event->order;
            $customer = $order->customer;

            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(
                    new \Beres\Notification\Mail\OrderCompletedMail($order)
                );

                Log::info("Order completed notification sent to: {$customer->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send order completed notification: " . $e->getMessage());
        }
    }
}
