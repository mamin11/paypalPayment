<?php
//  echo 'USER';
require 'src/start.php';

// var_dump($user);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>webPayment</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="">
	</head>
	<body>
		<?php if($user->member) : ?>
			<p>YOU ARE A MEMMBER</p>
		<?php else : ?>
			<p>YOU ARE NOT A MEMBER. <a href="member/payment.php">click to become member</a></p>
		<?php endif; ?>


		<script src="https://www.paypal.com/sdk/js?client-id=sb"></script>
		<script>paypal.Buttons().render('body');</script>
	</body>
</html>
 <?php