<?php


require 'vendor/autoload.php';

// $pagarme = new PagarMe\Client('ak_test_tyzdxe39mTDFsC0Bfdwlx3hYafC5TH');


// $paidBoleto = $pagarme->transactions()->simulateStatus([
//   'id' => '7363171',
//   'status' => 'paid'
// ]);

// echo var_dump($paidBoleto);

// "yyyy-MM-dd'T'HH:mm:ss'Z'"

$data = '2019-11-19T21:59:53.276Z';

$date = new DateTime($data);
// echo $date->format("yyyy-MM-dd'T'HH:mm:ss'Z'");
$date = $date->format("d-m-y");

$agora = new DateTime();
$agora->modify('+1 day');


// echo var_dump($agora->getTimestamp());

$agora = $agora->format("d-m-y");

echo var_dump($agora);

$retVal = ($date > $agora) ? true : false ;

echo var_dump($retVal);