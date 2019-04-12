<?php 


session_start();//inicia o uso de sessao

require_once("vendor/autoload.php");// do composer, para trazer as dependencias necessarias do projeto

//quais namespaces/classes que serao usadas
use \Slim\Slim; // usando a classe Slim do namespace Slim
use \Hcode\Page;//usando a classe Page do namespace Hcode
use \Hcode\PageAdmin;//usando a classe PageAdmin do namespace Hcode
use \Hcode\Model\User;//usando a classe User do namespace Model do namespace Hcode

$app = new Slim();//criando nova aplicação do slim para facilitar, cria uma nova rota?

$app->config('debug', true);

//realmente importante ------
$app->get('/', function() {  // Qual rota esta sendo chamada ===> rota principal

	$page = new Page();//instanciando a classe page
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("index");// usando o setTpl com o parametro index para chamar o arquivo index.html
	//vai usar o arquivo"ecommerce/views/header.html"
    

    //vai chamar o destruct que chama footer ao final da execução


});

$app->get('/admin', function() {  // Qual rota esta sendo chamada ===> rota do administrador

	User::verifyLogin();//metodo statico para verificar login

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("index");// usando o setTpl com o parametro index para chamar o arquivo index.html
	//vai usar o arquivo"ecommerce/views/admin/index.html"
    

    //vai chamar o destruct que chama footer ao final da execução


});


$app->get('/admin/login', function(){//Qual rota esta sendo chamada ===> rota do login do adminstrador via GET



	$page = new PageAdmin([
      	"header"=>false,//para nao chamar o header
      	"footer"=>false//para não chamar o footer
	    ]);

	$page->setTpl("login");// usando o setTpl com o parametro login para chamar o arquivo login.html
	//vai usar o arquivo"ecommerce/views/admin/login.html"

});

$app->post('/admin/login', function(){ //Qual rota esta sendo chamada ===> rota do login do adminstrador via POST
	//essa rota valida o login


	User::login($_POST["login"], $_POST["password"]);//esse metodo recebe o post de login e senha
	//ver em ecommerce\vendor\hcodebr\php-classes\src\Model\User.php

	header("Location: /admin");//redireciona para a pagina do admin se a verificação for positiva

	exit;//termina a execução



});

$app->get('/admin/logout', function(){//Qual rota esta sendo chamada ===> rota do logout do adminstrador 

	User::logout();//usa o metodo de logout

	header("Location: /admin/login");//redireciona para a tela de login

	exit;


});


$app->get("/admin/users", function(){//Qual rota esta sendo chamada ===> rota do logout do adminstrador / user

	User::verifyLogin();//metodo statico para verificar login

	$users = User::listAll();//metodo statico para listar os usuarios

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("users", array(
		"users"=>$users
						));

							// usando o setTpl com o parametro users para chamar o arquivousers.html
	//vai usar o arquivo"ecommerce/views/admin/users.html"

});


$app->get("/admin/users/create", function(){ //Qual rota esta sendo chamada ===> rota do logout do adminstrador / create

	User::verifyLogin();//metodo statico para verificar login

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("users-create");// usando o setTpl com o parametro users para chamar o arquivousers.html
	//vai usar o arquivo"ecommerce/views/admin/users.html"

});


$app->get("/admin/users/:iduser/delete", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");

	exit;

});


$app->get("/admin/users/:iduser", function($iduser){ //Qual rota esta sendo chamada ===> rota do logout do adminstrador / update

	User::verifyLogin();//metodo statico para verificar login

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("users-update", array(
					"user"=>$user->getValues()
				));

	// usando o setTpl com o parametro users para chamar o arquivousers.html
	//vai usar o arquivo"ecommerce/views/admin/users.html"

});
/**/
$app->post("/admin/users/create", function(){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");

	exit;


});

$app->post("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");

	exit;

});








$app->run();//roda o codigo que estiver dentro da rota

 ?>