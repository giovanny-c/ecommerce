<?php

namespace Hcode\Model;//criando o namespace

use \Hcode\DB\Sql;//usando a classe sql do namespace DB>Hcode
use \Hcode\Model;//usando a classe model do namespace Hcode


class Product extends Model{ //Para as categorias de produtos

	//nao usa o metodo __call()
	public static function listAll(){//le todos os dados da tabela

		$sql = new Sql();

		return $sql->select(
			"SELECT * FROM tb_products ORDER BY desproduct" 
			);
	}


	public static function checkList($list){

		foreach ($list as &$row) {// & vai manipular a mesma variavel na memoria 
			
			$p = new Product();
			$p->setData($row);
			$row = $p->getValues();

		}

		return $list;


	}


	


	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_products_save( :idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", 
			array(//parametros pegos com o __call, que estão no objeto
   				":idproduct"=>$this->getidproduct(),
   				":desproduct"=>$this->getdesproduct(),
   				":vlprice"=>$this->getvlprice(),
   				":vlwidth"=>$this->getvlwidth(), 
   				":vlheight"=>$this->getvlheight(), 
   				":vllength"=>$this->getvllength(),
   				":vlweight"=>$this->getvlweight(), 
   				":desurl"=>$this->getdesurl() 
   				        
			));//usando a procedure criada no banco, tornando o processo mais rapido,

			

			$this->setData($results[0]);//vai rearmazenar os dados que retornaram novamente no objeto


	}


	public function get($idproduct){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
			':idproduct'=>$idproduct
			]);

		$this->setData($results[0]);




	}

	public function delete(){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
			':idproduct'=>$this->getidproduct()
		]);

		

	}


	public function checkPhoto(){

		if(file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
		 "res" . DIRECTORY_SEPARATOR . 
		 "site"  . DIRECTORY_SEPARATOR . 
		 "img" . DIRECTORY_SEPARATOR . 
		 "products" . DIRECTORY_SEPARATOR . 
		 $this->getidproduct() . ".jpg")){

		 	$url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";

		}else{

			$url =  "/res/site/img/fabinhoematipnoiados.jpg";
		}


		return $this->setdesphoto($url);


	}


	public function getValues(){

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;

	}


	public function setPhoto($file){

		$extension = explode('.',$file['name']);

		$extension = end($extension);

		switch ($extension) {
			case "jpg":
			case "jpeg":
			$image = imagecreatefromjpeg($file["tmp_name"]);

				break;
			
			case "gif":
			$image = imagecreatefromgif($file["tmp_name"]);
				
				break;

			case "png":
			$image = imagecreatefrompng($file["tmp_name"]);

				break;
		}

		$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
		 "res" . DIRECTORY_SEPARATOR . 
		 "site"  . DIRECTORY_SEPARATOR . 
		 "img" . DIRECTORY_SEPARATOR . 
		 "products" . DIRECTORY_SEPARATOR . 
		 $this->getidproduct() . ".jpg";

		imagejpeg($image, $dist);

		imagedestroy($image);

		$this->checkPhoto();


	}

	


}

?>