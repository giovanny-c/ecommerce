<?php 


session_start();//inicia o uso de sessao

require_once("vendor/autoload.php");// do composer, para trazer as dependencias necessarias do projeto

//quais namespaces/classes que serao usadas
use \Slim\Slim; // usando a classe Slim do namespace Slim
use \Hcode\Page;//usando a classe Page do namespace Hcode
use \Hcode\PageAdmin;//usando a classe PageAdmin do namespace Hcode
use \Hcode\Model\User;//usando a classe User do namespace Model do namespace Hcode
use \Hcode\Model\Category;

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

// -------- OLHAR OS ARQUIVOS HTML DAS RESPECTIVAS ROTAS, POIS SÃO ALTERADOS PARA QUE OS DADOS SEJAM MOSTRADOS NA TELA DE MANEIRA CORETA -------------------------

$app->get("/admin/users", function(){//Qual rota esta sendo chamada ===> rota da lista do adminstrador / user, lista todos os usuarios

	User::verifyLogin();//metodo statico para verificar login

	$users = User::listAll();//metodo statico para listar os usuarios

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("users", array(
		"users"=>$users //passando a variavel $users como parametro, a var. contem os resultados que serão listados na tabela quando a pagina for chamada 
						));

							// usando o setTpl com o parametro users para chamar o arquivousers.html
	//vai usar o arquivo"ecommerce/views/admin/users.html"

});


$app->get("/admin/users/create", function(){ //Qual rota esta sendo chamada ===> rota da criação de cad. do adminstrador / create, chama a tela de criação um cadastro

	User::verifyLogin();//metodo statico para verificar login

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("users-create");// usando o setTpl com o parametro users para chamar o arquivousers.html
	//vai usar o arquivo"ecommerce/views/admin/users-create.html"

});

//sempre deixar o :iduser/--- antes de :iduser no codigo
$app->get("/admin/users/:iduser/delete", function($iduser){//Qual rota esta sendo chamada ===> rota de alteração do adminstrador / delete, chama a tela de exclusao de um cadastro
	//vai usar o :iduser como parametro para chamar os dados do usuario do banco

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);//vai pegar o id recebido via post ao clicar em excluir e vai armazenar no objeto $user

	$user->delete();//metodo para deletar

	header("Location: /admin/users");//redireciona

	exit;

});


$app->get("/admin/users/:iduser", function($iduser){ //Qual rota esta sendo chamada ===> rota de alteração do adminstrador / update, chama a tela de alteração de um cadastro
	//vai usar o :iduser como parametro para chamar os dados do usuario do banco

	User::verifyLogin();//metodo statico para verificar login

	$user = new User();//instanciando o User

	$user->get((int)$iduser);//vai pegar o id recebido via post ao clicar em editar e vai armazenar no objeto $user 

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("users-update", array(
					"user"=>$user->getValues()//vai pegar os valores de $values e vai usar para prencher os campos da pagina
				));

	// usando o setTpl com o parametro users para chamar o arquivousers.html
	//vai usar o arquivo"ecommerce/views/admin/users-update.html"

});
/**/
$app->post("/admin/users/create", function(){//rota de criação via post, vai mandar o que foi digitado para o banco

	User::verifyLogin();//verificandologin	

	$user = new User();//instanciando user

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;//fazendo uma operação ternaria para conferir se o campo inadmin foi marcado, se for, vai ser 1, se nao 0, (admininstrador)

	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, ["cost"=>12]
    );//para gerar o hash no banco

	$user->setData($_POST); //vai passar o que foi enviado pelo POST para o setData(), que vai acionar o __call(), para mandar para o $values

	$user->save();//metodo para salvar os dados

	header("Location: /admin/users");//redireciona 

	exit;


});

$app->post("/admin/users/:iduser", function($iduser){//post para ediçao de cadastros, vai pegar os dados alterados nos campos e jogar para o banco
//users-update
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;//fazendo uma operação ternaria para conferir se o campo inadmin foi marcado, se for, vai ser 1, se nao 0, (admininstrador)

	$user->get((int)$iduser);//vai pegar o id recebido via post ao clicar em editar e vai armazenar no objeto $user 

	$user->setData($_POST); //vai passar o que foi enviado pelo POST para o setData(), que vai acionar o __call(), para mandar para o $values

	$user->update();//metodo que faz o update

	header("Location: /admin/users");//redireciona

	exit;

});


$app->get("/admin/forgot", function(){ //tela do esqueceu a senha

	$page = new PageAdmin([
      	"header"=>false,//para nao chamar o header
      	"footer"=>false//para não chamar o footer
	    ]);

	$page->setTpl("forgot");


});


$app->post("/admin/forgot", function(){//recebe o email de mudança

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");

	var_dump($_POST["email"]);

	exit;

});

$app->get("/admin/forgot/sent", function() {//tela envio do email de mudança

	$page = new PageAdmin([
      	"header"=>false,//para nao chamar o header
      	"footer"=>false//para não chamar o footer
	    ]);

	$page->setTpl("forgot-sent");

});


$app->get("/admin/forgot/reset", function(){//tela de mudança de senha

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
      	"header"=>false,//para nao chamar o header
      	"footer"=>false//para não chamar o footer
	    ]);

	$page->setTpl("forgot-reset", array(
				"name"=>$user["desperson"],
				"code"=>$_GET["code"]
	));



});

$app->post("/admin/forgot/reset", function(){//tela de mudança de senha - para validar a mudança de senha

	$forgot = User::validForgotDecrypt($_GET["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);

	$user->setPassword($password);

	$page = new PageAdmin([
      	"header"=>false,//para nao chamar o header
      	"footer"=>false//para não chamar o footer
	    ]);

	$page->setTpl("forgot-reset-success");


});

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", array(
						"categories"=>$categories
				));


});

$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");


});

$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');

	exit();


});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

		User::verifyLogin();

		$category = new Category();

		$category->get((int)$idcategory);

		$category->delete();

		header('Location: /admin/categories');

		exit;


});

$app->get("/admin/categories/:idcategory", function($idcategory){

		User::verifyLogin();

		$category = new Category();

		$category->get((int)$idcategory);

		$page = new PageAdmin();

		$page->setTpl("categories-update",[
				'category'=>$category->getValues()
			]);


		//header('Location: /admin/categories');

		exit;


});

$app->post("/admin/categories/:idcategory", function($idcategory){

		User::verifyLogin();

		$category = new Category();

		$category->get((int)$idcategory);

		$category->setData($_POST);

		$category->save();

		header('Location: /admin/categories');

		exit;


});

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category",[
			'category'=>$category->getValues(),
			'products'=>[]
	]);


});

$app->run();//roda o codigo que estiver dentro da rota

 ?>