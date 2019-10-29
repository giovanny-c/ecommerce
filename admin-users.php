<?php

use Hcode\PageAdmin;
use Hcode\Model\User;


$app->get("/admin/users", function(){//Qual rota esta sendo chamada ===> rota da lista do adminstrador / user, lista todos os usuarios

	User::verifyLogin();//metodo statico para verificar login

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != ''){

		$pagination = User::getPageSearch($search, $page );//metodo statico para listar os usuarios

	}else{

		$pagination = User::getPage($page);//metodo statico para listar os usuarios

	}

	

	$pages = [];

	for ($x=0; $x < $pagination['pages']; $x++) { 

		array_push($pages,[
			'href'=>'/admin/users?'.http_build_query([
				'page'=>$x+1,
				'search'=>$search
						]),
				'text'=>$x+1	
		]);
	}

	$page = new PageAdmin();//instanciando a classe PageAdmin
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("users", array(
		"users"=>$pagination['data'], 
		"search"=>$search,
		"pages"=>$pages
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

?>