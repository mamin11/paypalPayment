<?php
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

require '../src/start.php';

if(isset($_GET['approved'])) {

    $approved = $_GET['approved'] === 'true';

    if(isset($approved)) {
        $payerID = $_GET['PayerID'];

        //get payment id from database
        $paymentId = $pdo->prepare("
            SELECT payment_id 
            FROM transactions_paypal
            WHERE hash = :hash
        ");
        $paymentId->execute([
            'hash' => $_SESSION['paypal_hash']
        ]);

        $paymentId = $paymentId->fetchObject()->payment_id;

        $payment = Payment::get($paymentId, $api);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerID);

        //charge the user
        $payment->execute($execution, $api);

        //update the transaction to complete
        $update = $pdo->prepare("
            UPDATE transactions_paypal
            SET complete = 1 
            WHERE payment_id = :payment_id
        ");

        $update->execute([
            'payment_id' => $paymentId
        ]);

        //then make the user a member
        $setMember = $pdo->prepare("
            UPDATE users 
            SET member = 1
            WHERE user_id = :user_id
        ");

        $setMember->execute([
            'user_id' => $_SESSION['user_id']
        ]);

        //unset the session hash
        unset($_SESSION['paypal_hash']);
        header('Location: ../member/complete.php');

    } else {
        header('Location: cancelled.php');
    }
}