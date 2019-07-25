<?php

use Hcode\Page;

$app->get('/', function() {  // Qual rota esta sendo chamada ===> rota principal

	$page = new Page();//instanciando a classe page
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("index");// usando o setTpl com o parametro index para chamar o arquivo index.html
	//vai usar o arquivo"ecommerce/views/header.html"
    

    //vai chamar o destruct que chama footer ao final da execução


});

?>