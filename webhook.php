<?php

require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey("sk_test_51MbHgzCBfF8BFctwVhBzh0c54OWfSwjR8biUXRjIpmEFTDFcamxQTvt25JqIDiE9dOo3ZTjg4gsorK17k235bHu100lJfS7LgI");

// You can find your endpoint's secret in your webhook settings in the Stripe Dashboard
$endpoint_secret = 'your_webhook_secret';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );

    // Handle the event
    if ($event->type == 'checkout.session.completed') {
        $session = $event->data->object;

        // Mark this session as successful in the session or database
        session_start();
        $_SESSION['payment_success'] = true;
    }

} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

http_response_code(200);
