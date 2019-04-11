<?php 


session_start();

require_once("vendor/autoload.php");// do composer, para trazer as dependencias necessarias do projeto

//quais namespaces/classes que serao usadas
use \Slim\Slim; // usando a classe Slim do namespace Slim
use \Hcode\Page;//usando a classe Page do namespace Hcode
use \Hcode\PageAdmin;//usando a classe PageAdmin do namespace Hcode
use \Hcode\Model\User;

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

	User::verifyLogin();

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("index");// usando o setTpl com o parametro index para chamar o arquivo index.html
	//vai usar o arquivo"ecommerce/views/admin/index.html"
    

    //vai chamar o destruct que chama footer ao final da execução


});


$app->get('/admin/login', function(){//Qual rota esta sendo chamada ===> rota do login do adminstrador

	

	$page = new PageAdmin([
      	"header"=>false,
      	"footer"=>false
	    ]);

	$page->setTpl("login");

});

$app->post('/admin/login', function(){

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");

	exit;



});

$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");

	exit;


});





$app->run();//roda o codigo que estiver dentro da rota

 ?>