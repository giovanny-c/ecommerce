<?php

use Hcode\PageAdmin;
use Hcode\Model\User;

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

?>