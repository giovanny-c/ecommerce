<?php

use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;
use Hcode\Model\Product;

$app->get("/admin/categories", function(){ //lista todas as categorias para o admin

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", array(
						"categories"=>$categories
				));


});

$app->get("/admin/categories/create", function(){//rota para criar uma nova categoria

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");


});

$app->post("/admin/categories/create", function(){//recebe a nova categoria e insere no bd

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');

	exit();


});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){//exclui uma categoria baseada no id

		User::verifyLogin();

		$category = new Category();

		$category->get((int)$idcategory);

		$category->delete();

		header('Location: /admin/categories');

		exit;


});

$app->get("/admin/categories/:idcategory", function($idcategory){//pega a categoria pelo id dela

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

$app->post("/admin/categories/:idcategory", function($idcategory){//usa o id dela para fazer update no banco

		User::verifyLogin();

		$category = new Category();

		$category->get((int)$idcategory);

		$category->setData($_POST);

		$category->save();

		header('Location: /admin/categories');

		exit;


});




$app->get("/admin/categories/:idcategory/products", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-products",[
			'category'=>$category->getValues(),
			'productsRelated'=>$category->getProducts(),
			'productsNotRelated'=>$category->getProducts(false)
	]);

});


$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->addProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});

$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->removeProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});

?>