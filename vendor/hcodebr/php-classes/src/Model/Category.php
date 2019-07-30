<?php

namespace Hcode\Model;//criando o namespace

use \Hcode\DB\Sql;//usando a classe sql do namespace DB>Hcode
use \Hcode\Model;//usando a classe model do namespace Hcode
use \Hcode\Mailer;

class Category extends Model{ //Para as categorias de produtos

	//nao usa o metodo __call()
	public static function listAll(){//le todos os dados da tabela

		$sql = new Sql();

		return $sql->select(
			"SELECT * FROM tb_categories ORDER BY descategory" 
			);
	}


	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_categories_save( :idcategory, :descategory)", 
			array(//parametros pegos com o __call, que estão no objeto
   				":idcategory"=>$this->getidcategory(),
   				":descategory"=>$this->getdescategory()        
			));//usando a procedure criada no banco, tornando o processo mais rapido, 

			$this->setData($results[0]);//vai rearmazenar os dados que retornaram novamente no objeto

			Category::updateFile();//vai sobrescrever o arquivo categories-menu.html

	}


	public function get($idcategory){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
			':idcategory'=>$idcategory
			]);

		$this->setData($results[0]);


	}

	public function delete(){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
			':idcategory'=>$this->getidcategory()
		]);

		Category::updateFile();//vai sobrescrever o arquivo categories-menu.html

	}

	public static function updateFile(){//vai sobrescrever o arquivo categories-menu.html, toda vez que uma categoria for feita, alterada ou excluida

		$categories = Category::listAll();

		$html=[];

		foreach ($categories as $row) {//categorias puxadas do bd sendo colocadas na var. $html com formatação em HTML
			array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		}

		// funçao que sobrescreve o arquivo
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views". DIRECTORY_SEPARATOR . "categories-menu.html" , implode('', $html));//vai converter o array $html em string

	}


	public function getProducts($related = true){

		$sql = new Sql();

		if($related === true){

			return $sql->select(
						"SELECT * FROM tb_products WHERE idproduct IN(
							  SELECT a.idproduct
							  FROM tb_products a 
							  INNER JOIN tb_productscategories b 
							  ON a.idproduct = b.idproduct 
							  WHERE b.idcategory = :idcategory);
					    ",[
					     	':idcategory'=>$this->getidcategory()
					    ]);

		}else{

			return $sql->select(
						"SELECT * FROM tb_products WHERE idproduct NOT IN(
							  SELECT a.idproduct
							  FROM tb_products a 
							  INNER JOIN tb_productscategories b 
							  ON a.idproduct = b.idproduct 
							  WHERE b.idcategory = :idcategory);
						",[
						 	':idcategory'=>$this->getidcategory()
						]);

		}

	}


	public function addProduct(Product $product){

		$sql = new Sql();

		$sql->query(
			"INSERT INTO tb_productscategories (idcategory, idproduct)
			 VALUES (:idcategory, :idproduct)",
		[
			':idcategory'=>$this->getidcategory(),
			':idproduct'=>$product->getidproduct()
		]);

	}

	public function removeProduct(Product $product){

		$sql = new Sql();

		$sql->query(
			"DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct",
			 [
			':idcategory'=>$this->getidcategory(),
			':idproduct'=>$product->getidproduct()
			]);

	}


}

?>