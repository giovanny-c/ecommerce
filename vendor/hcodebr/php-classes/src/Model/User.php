<?php

namespace Hcode\Model;//criando o namespace

use \Hcode\DB\Sql;//usando a classe sql do namespace DB>Hcode
use \Hcode\Model;//usando a classe model do namespace Hcode
use \Hcode\Mailer;

class User extends Model{ //extende da classe model
	//\ecommerce\vendor\hcodebr\php-classes\src\Model.php


	const SESSION = "User";//constante da sessao
	const SECRET = "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV";
	const ERROR = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";
	const SUCCESS = "UserSuccess";
	//const KEY =


	public static function getFromSession(){
 		
 		$user = new User();
		
		if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser']>0)
		{
			$user->setData($_SESSION[User::SESSION]);

		}

		return $user;


	}

	public static function checklogin($inadmin = true){

		if( 
			!isset($_SESSION[User::SESSION])//se a sessao nao for definida
			|| 
			!$_SESSION[User::SESSION]// se ela for falsa
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0//verifica se o id do usuario nao for maior que zero
		){
			//nao esta logado
			return false;

		} else {

			if($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true )
			{
				return true;

			} else if ($inadmin === false ){

				return true;

			}else {

				return false;
			}



		}


	}



	public static function login($login, $password){//recebe os parametros login e password que vao ser usados para validação

		$sql = new Sql();//instanciando a classe sql da pasta DB

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGIN", array(
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

		if(!User::checklogin($inadmin)){//se nenhum deles for valido, volta para a tela de login

			if($inadmin){

				header("Location: /admin/login");//redireciona para a tela de login
			
			}else{

				header("Location: /login");
			}
			exit;

	    }

	}

	

	public static function logout(){//para deslogar

		$_SESSION[User::SESSION] = NULL;//exclui a sessao 

		

	}


	//nao usa o metodo __call()
	public static function listAll(){//le todos os dados da tabela

		$sql = new Sql();

		return $sql->select(
			"SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson" 
			);//une a tabela tb_users com tb_person e 

	}


	public function save(){//executa o insert do novo cadastro, retorna o iduser

		$sql = new Sql();
	

		$results = $sql->select("CALL sp_users_save( :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin )", 
			array(//parametros pegos com o __call, que estão no objeto
   				":desperson"=> $this->getdesperson(),
   				":deslogin"=> $this->getdeslogin(),
   				":despassword"=>User::getPasswordHash($this->getdespassword()),
   				":desemail"=> $this->getdesemail(),
   				":nrphone"=> $this->getnrphone(),
   				":inadmin"=> $this->getinadmin()         
			));//usando a procedure criada no banco, tornando o processo mais rapido, vai cadastrar o usuario no tb_person e tb_user e vai retornar o ID cadastrado

			$this->setData($results[0]);//vai rearmazenar os dados que retornaram novamente no objeto


	}


	public function get($iduser){//vai usar o parametro $iduser para pegar o id e buscar o seu cadastro
	 
		$sql = new Sql();
		 
		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser;", array(
			":iduser"=>$iduser
			));//faz um select pelo iduser
		 
		$data = $results[0];//armazena em o resultado
		 
		$this->setData($data);//joga no setData, para ser armazenado no objeto
	 
	}

	public function update(){//executa o update

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save( :iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin )", 
			array(
				":iduser"=>$this->getiduser(),//usando o iduser para fazer o update
   				":desperson"=> $this->getdesperson(),
   				":deslogin"=> $this->getdeslogin(),
   				":despassword"=>$this->getdespassword(),
   				":desemail"=> $this->getdesemail(),
   				":nrphone"=> $this->getnrphone(),
   				":inadmin"=> $this->getinadmin()         
			));//usando a procedure criada no banco, tornando o processo mais rapido, vai atualizar o usuario no tb_person e tb_user e vai retornar o ID do cadastro atualizado

			$this->setData($results[0]);//vai rearmazenar os dados que retornaram novamente no objeto


	}


	public function delete(){//executa o delete	


		$sql = new Sql();


		$sql->query("CALL sp_users_delete(:iduser)",array(
			":iduser"=>$this->getiduser()
		));//usando a procedure criada no banco, tornando o processo mais rapido, vai deletar o usuario no tb_person e tb_user 
	}


/////////// NOVO

public static function getForgot($email, $inadmin = true)
	{
		$sql = new Sql();
		$results = $sql->select("
			SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email;
		", array(
			":email"=>$email
		));
		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{
			$data = $results[0];
			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data['iduser'],
				":desip"=>$_SERVER['REMOTE_ADDR']
			));
			if (count($results2) === 0)
			{
				throw new \Exception("Não foi possível recuperar a senha.");
			}
			else
			{
				$dataRecovery = $results2[0];
				$code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));
				$code = base64_encode($code);
				if ($inadmin === true) {
					$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";
				} else {
					$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$code";
					
				}				
				$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
					"name"=>$data['desperson'],
					"link"=>$link
				));				
				$mailer->send();
				return $link;
			}
		}
	}

public static function validForgotDecrypt($code)
	{
		$code = base64_decode($code);

		

		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

		
		$sql = new Sql();


		$results = $sql->select("
			SELECT *
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE
				a.idrecovery = :idrecovery
				AND
				a.dtrecovery IS NULL
				AND
				DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			":idrecovery"=>$idrecovery
		));
		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{
			return $results[0];
		}
	}


    
    public static function setForgotUsed($idrecovery){

    	$sql = new Sql();

    	$sql->query("UPDATE tb_userpasseordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
    						":idrecovery"=>$idrecovery
    		));

    }


    public function setPassword($password){

    	$sql = new Sql();

    	$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
    					":password"=>$password,
    					":iduser"=>$this->getiduser()
    			      ));

    	


    }


    public static function getPasswordHash($password){
		
		return password_hash($password, PASSWORD_DEFAULT, [
	    	'cost'=>12
	    ]);

    }//APAGAR SE DER ERRO


	public static function setError($msg)
	{
			$_SESSION[User::ERROR] = $msg;
	}
		
	public static function getError()
	{
			$msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';
			User::clearError();
			return $msg;
	}
	
	public static function clearError()
	{
			$_SESSION[User::ERROR] = NULL;
	}



    public static function setSuccess($msg)
	{
			$_SESSION[User::SUCCESS] = $msg;
	}
		
	public static function getSuccess()
	{
			$msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : '';
			User::clearSuccess();
			return $msg;
	}
	
	public static function clearSuccess()
	{
			$_SESSION[User::SUCCESS] = NULL;
	}




    public static function setErrorRegister($msg){

    	$_SESSION[User::ERROR_REGISTER] = $msg;

    }


    public static function getErrorRegister(){

    	$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : '';

    	User::clearErrorRegister();

    	return $msg;


    }



    public static function clearErrorRegister(){

    	$_SESSION[User::ERROR_REGISTER] = NULL;

    }


    public static function checkLoginExist($login){

    	$sql = new Sql();

    	$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :deslogin", [
    			':deslogin'=>$login
    		]);

    	return (count($results) > 0);

    }

    

}

?>