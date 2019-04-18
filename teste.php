<?php

class Teste{



	const SECRET = "HcodePhp7_Secret";
	private $iv;

	public function setIv($iV){	

		$this->iv = $iV;


	}

	public function getIv(){	

		return $this->iv;

	}



	public static function encdec($p, $code = ""/*, $iv = ""*/){

$t = new Teste();

		if($p === 1){

			



			$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

			$t->setIv($iv);

			var_dump($iv);

			echo "iv <br>";

			$data = 1;

			$code = openssl_encrypt($data, 'aes-256-cbc', Teste::SECRET, 0, $iv);

			base64_encode($code);

			var_dump($code); 

	    	echo "ecode <br>";

	    	return $code;


		}elseif ($p === 2){

//$iv = $t->getIv();
			
			$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

			base64_decode($code);

			$dec = openssl_decrypt($code,'aes-256-cbc', Teste::SECRET, OPENSSL_RAW_DATA);

			var_dump($dec);

			echo "iv <br>";

			var_dump($code);

			echo "dcode <br>";

	    	return $code;

		}


}
}



$a = Teste::encdec(1);

$t = new Teste();

echo "<br>esse ".$t->getIv();
echo "<br>";

$b = Teste::encdec(2, $a );


echo "a = $a e b = $b ";





?>