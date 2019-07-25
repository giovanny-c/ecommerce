<?php

use Hcode\PageAdmin;
use Hcode\Model\User;


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

?>