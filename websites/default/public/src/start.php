<?php
//fake the user sign in
//require dependencies
//set up api keys
//set the config for the api
//set up connection to the database

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

session_start();
$_SESSION['user_id'] = 1;

require __DIR__ . '/../vendor/autoload.php';

//CREATE NEW INSTANCE OF API
$api = new ApiContext(
    new OAuthTokenCredential (
        'AaiwJYwVYy2yL82qWVTiG_aMOHatZyyYy_6Tnh9gzaogFwvKPaw20gMY9vHHC9Za5ZCWxsscQFYO_QzU',
        'EBh5LSSVebkFs0cKGjrCqDxEJyydBVMH3K8oxuBlXQLgO2w82CkCpxNAo_JwX-BnYRWPFii36gn91gCp'
    )
);

$api->setConfig([
    'mode' => 'sandbox',
    'http.ConnectionTimeOut' => 30,
    'log.LogEnabled' => false,
    'log.FileName' => '',
    'log.logLevel' => 'FINE',
    'validation.level' => 'log'
]);

//database connection
$server = 'v.je';
$username = 'student';
$password = 'student';
$schema = 'webPayment';
$pdo = new PDO('mysql:dbname=' . $schema . ';host=' . $server, $username, $password,
[ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$user = $pdo->prepare("
    SELECT * FROM users
    WHERE user_id = :user_id
");
//execute the query
$user->execute(['user_id' => $_SESSION['user_id']]);

$user = $user->fetchObject();