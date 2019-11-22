<?php


require 'vendor/autoload.php';

$pagarme = new PagarMe\Client('ak_test_tyzdxe39mTDFsC0Bfdwlx3hYafC5TH');


$paidBoleto = $pagarme->transactions()->simulateStatus([
  'id' => '7363171',
  'status' => 'paid'
]);

echo var_dump($paidBoleto);
