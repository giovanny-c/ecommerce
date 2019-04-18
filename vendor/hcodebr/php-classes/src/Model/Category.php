<?php

namespace Hcode\Model;//criando o namespace

use \Hcode\DB\Sql;//usando a classe sql do namespace DB>Hcode
use \Hcode\Model;//usando a classe model do namespace Hcode
use \Hcode\Mailer;

class Category extends Model{ 

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

	}


}

?>