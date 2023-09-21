<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function __invoke(Request $request)
    {
        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = 'whsec_5b6eb1f395d2f6dff7d02c23945b819ab403777c0ef62b91b6d98b49d4aff487';

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return response('', 400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('', 400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                Payment::where('gateway_reference_id', $session->id)->update([
                    'gateway_reference_id' => $session->payment_intent,
                ]);
                break;
            case 'checkout.session.expired':
                $session = $event->data->object;
                break;
            case 'payment_intent.amount_capturable_updated':
                $paymentIntent = $event->data->object;
                break;
            case 'payment_intent.canceled':
                $paymentIntent = $event->data->object;
                break;
            case 'payment_intent.created':
                $paymentIntent = $event->data->object;
                break;
            case 'payment_intent.partially_funded':
                $paymentIntent = $event->data->object;
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                break;
            case 'payment_intent.processing':
                $paymentIntent = $event->data->object;
                break;
            case 'payment_intent.requires_action':
                $paymentIntent = $event->data->object;
                break;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $payment = Payment::where('gateway_reference_id', $paymentIntent->id)->first();
                $payment->forceFill([
                    'status' => 'completed',
                ])->save();
                $subscription = Subscription::where('id', $payment->subscription_id)->first();
                $subscription->update([
                    'status' => 'active',
                    'expires_at' => now()->addMonths(3)
                ]);
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }
        return response('', 200);
    }
}
