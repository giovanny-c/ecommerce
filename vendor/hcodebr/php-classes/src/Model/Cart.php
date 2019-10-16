<?php

namespace Hcode\Model;//criando o namespace


use \Hcode\DB\Sql;//usando a classe sql do namespace DB>Hcode
use \Hcode\Model;//usando a classe model do namespace Hcode
use \Hcode\Mailer;
use \Hcode\Model\User;

class Cart extends Model{ //Para as categorias de produtos

	const SESSION = "Cart";

	public static function getFromSession(){

		$cart = new Cart();

		if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart']>0) 
		{

			$cart->get((int)$_SESSION[Cart::SESSION]["idcart"]);

		} else {

			$cart->getFromSessionID();

			if (!(int)$cart->getidcart()>0){

				$data =[
					'dessessionid'=>session_id()

					];

					if (User::checkLogin(false))
					{
						$user = User::getFromSession();

						$data['iduser'] = $user->getiduser();
					}

					$cart->setData($data);

					$cart->save();

					$cart->setToSession();

					
			}
		}

		return $cart;

	}


	public function seToSession(){

		$_SESSION[Cart::SESSION] = $this->getvalues();

	}


	public function get(int $idcart){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
			':idcart'=>$idcart
		]);

		if (count($results)>0) {

			$this->setData($results[0]);
		
		}

	}

	public function getFromSessionID (){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [
			':dessessionid'=>session_id()
		]);

		if (count($results)>0) {

			$this->setData($results[0]);
		
		}

		

	}


	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays )",[
			':idcart'=>$this->getidcart(),
			':dessessionid'=>$this->getdessessionid(),
			':iduser'=>$this->getiduser(),
			':deszipcode'=>$this->getdeszipcode(),
			':vlfreight'=>$this->getvlfreight(),
			':nrdays'=>$this->getnrdays()
		]);

		$this->setData($results[0]);

	}

}

?>