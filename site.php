<?php

use Hcode\Page;
use Hcode\Model\Product;
use Hcode\Model\Category;

$app->get('/', function() {  // Qual rota esta sendo chamada ===> rota principal

	$products = Product::listAll();

	$page = new Page();//instanciando a classe page
	//quando criado chama metodo construct que chama o header.html na tela

	$page->setTpl("index",[
			'products'=>Product::checkList($products)
		]);// usando o setTpl com o parametro index para chamar o arquivo index.html
	//vai usar o arquivo"ecommerce/views/header.html"
    

    //vai chamar o destruct que chama footer ao final da execução


});


$app->get("/categories/:idcategory", function($idcategory){//menu de categoria na tela principal
	
	$urlCode = $_SERVER['REQUEST_URI'];

    $codeUrl = explode('page=', $urlCode);

    $page = (isset($codeUrl[1]))? (int) $codeUrl[1] : 1;

	$category = new Category();

	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($page);

	$pages = [];

	for ($i=1; $i <= $pagination['pages'] ; $i++){

		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory().'page='.$i,
			'page'=>$i
		]);
	}

	$page = new Page();

	$page->setTpl("category",[
			'category'=>$category->getValues(),
			'products'=>$pagination["data"],
			'pages'=>$pages
	]);


});

/**/
$app->get("/products/:desurl", function($desurl){


	$product = new Product();

	$product->getFromURL($desurl);

	$page = new Page();

	$page->setTpl("product-detail",[
			'product'=>$product->getValues(),
			'categories'=>$product->getCategories()
		]);


});

?>