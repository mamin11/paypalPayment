<?php

use PayPal\Api\Payer;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls; 
use PayPal\Exception\PPConnectionException;

require '../src/start.php';

$payer = new Payer();
$details = new Details();
$amount = new Amount();
$transaction = new Transaction();
$payment = new Payment();
$redirectUrls = new RedirectUrls();

//payer
$payer->setPaymentMethod('paypal');

//details
$details->setShipping('2.00')
        ->setTax('0.00')
        ->setSubtotal('20.00');

//amount
$amount->setCurrency('GBP')        
        ->setTotal('22.00')
        ->setDetails($details);

//transaction
$transaction->setAmount($amount)        
            ->setDescription('Membership');

//payment
$payment->setIntent('sale')
        ->setPayer($payer)
        ->setTransactions([$transaction]);

//redirect urls
$redirectUrls->setReturnUrl('https://v.je/paid/pay.php?approved=true')
             ->setCancelUrl('https://v.je/paid/pay.php?approved=false'); // or redicrect to cancelled.php
        
$payment->setRedirectUrls($redirectUrls);

try {
    $payment->create($api);

    //then generate and store hash for the database
    $hash = md5($payment->getId());
    $_SESSION['paypal_hash'] = $hash;

    //prepare and execute transaction storage
    $store = $pdo->prepare("
        INSERT INTO transactions_paypal (user_id, payment_id, hash, complete)
        VALUES (:user_id, :payment_id, :hash, 0)
    ");

    $store->execute([
        'user_id' => $_SESSION['user_id'],
        'payment_id' => $payment->getId() ,
        'hash' => $hash 
    ]);

} catch(PPConnectionException $e) {
    header('Location: ../paid/error.php');
}

// var_dump($payment->getLinks());
foreach($payment->getLinks() as $link) {
    if($link->getRel() == 'approval_url') {
        $redirectUrls = $link->getHref();
    }
}

// var_dump($redirectUrls);
header('Location: ' . $redirectUrls);