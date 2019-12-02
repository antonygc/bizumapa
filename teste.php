<?php


// require 'vendor/autoload.php';

// $pagarme = new PagarMe\Client('ak_test_tyzdxe39mTDFsC0Bfdwlx3hYafC5TH');

// $paidBoleto = $pagarme->transactions()->simulateStatus([
//   'id' => '7373505',
//   'status' => 'paid'
// ]);

// echo var_dump($paidBoleto);

// "yyyy-MM-dd'T'HH:mm:ss'Z'"

// $data = '2019-11-19T21:59:53.276Z';

// $date = new DateTime($data);
// // echo $date->format("yyyy-MM-dd'T'HH:mm:ss'Z'");
// $date = $date->format("d-m-y");

// $agora = new DateTime();
// $agora->modify('+1 day');


// // echo var_dump($agora->getTimestamp());

// $agora = $agora->format("d-m-y");

// echo var_dump($agora);


echo var_dump(hexdec(uniqid()));



$domain = substr(strrchr($email, "@"), 1);

$blacklist = file_get_contents('app/blacklists/email.txt');
$pos = strpos($blacklist, $domain);

if ($pos) {
	throw new Exception("Domínio inválido");
	
}
echo var_dump('OK');
