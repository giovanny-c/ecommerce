<?php

namespace Hcode\Model;//criando o namespace

use \Hcode\DB\Sql;//usando a classe sql do namespace DB>Hcode
use \Hcode\Model;//usando a classe model do namespace Hcode


class User extends Model{ //extende da classe model
	//\ecommerce\vendor\hcodebr\php-classes\src\Model.php


	const SESSION = "User";//constante da sessao

	public static function login($login, $password){//recebe os parametros login e password que vao ser usados para validação

		$sql = new Sql();//instanciando a classe sql da pasta DB

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login  //fazendo o bind dos parametros
		));
		//usa o parametro $login para pesquisar no banco de dados se tem algum usuario com esse login, armazena o resultado em $results



		if(count($results) === 0 ){//se nao encontrar nenhum login

			throw new \Exception("Usuário inexistente ou senha inválida.");
			// "\" para achar a exception principal

		}


		$data = $results[0];//data é igual ao primeiro registro encontrado

		if (password_verify($password, $data["despassword"]) === true){
		//função do php: https://www.php.net/manual/en/function.password-verify.php
        //usa a variavel $password que veio como parametro de login()
		//usa a var. $data que é um array do primeiro registro encontrado, contendo suas informações, na posiçao "despassword"(o hash)
		//vai comparar os dois
		//se o hash bater com a senha passada pelo parametro ele executa o codigo abaixo
			$user = new User();//criando uma instancia da propria classe, pois é um metodo estatico

			$user->setData($data);//passa o array data com todos os campos retornados do registro,por ser um "set", quando forem passados seram armazenados no atributo privado $values, por causa do metodo __call() 

			$_SESSION[User::SESSION] =  $user->getValues();//criando uma sessao e colocando os dados dentro da sessao

			return $user;



		}else{//se nao bater

			throw new \Exception("Usuário inexistente ou senha inválida.");

		}


	}


	public static function verifyLogin($inadmin = true){

		if(
			!isset($_SESSION[User::SESSION])//se a sessao nao for definida
			|| 
			!$_SESSION[User::SESSION]// se ela for falsa
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0//verifica se o id do usuario nao for maior que zero
			||
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin// se não for administrador
		){ //se nenhum deles for valido, volta para a tela de login
		

			header("Location: /admin/login");//redireciona para a tela de login

			exit;

		}

	}

	public static function logout(){//para deslogar

		$_SESSION[User::SESSION] = NULL;//exclui a sessao 

	}


	public static function listAll(){

		$sql = new Sql();

		return $sql->select(
			"SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson"
			);





	}


	public function save(){

		$sql = new Sql();
	

		$results = $sql->select("CALL sp_users_save( :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin )", 
			array(
   				":desperson"=> $this->getdesperson(),
   				":deslogin"=> $this->getdeslogin(),
   				":despassword"=> $this->getdespassword(),
   				":desemail"=> $this->getdesemail(),
   				":nrphone"=> $this->getnrphone(),
   				":inadmin"=> $this->getinadmin()         
			));

			$this->setData($results[0]);

	}


	public function get($iduser){
	 
		$sql = new Sql();
		 
		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser;", array(
			":iduser"=>$iduser
			));
		 
		$data = $results[0];
		 
		$this->setData($data);
	 
	}

	public function update(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save( :iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin )", 
			array(
				":iduser"=>$this->getiduser(),
   				":desperson"=> $this->getdesperson(),
   				":deslogin"=> $this->getdeslogin(),
   				":despassword"=> $this->getdespassword(),
   				":desemail"=> $this->getdesemail(),
   				":nrphone"=> $this->getnrphone(),
   				":inadmin"=> $this->getinadmin()         
			));

			$this->setData($results[0]);


	}


	public function delete(){


		$sql = new Sql();


		$sql->query("CALL sp_users_delete(:iduser)",array(
			":iduser"=>$this->getiduser()
		));
	}

}

?>