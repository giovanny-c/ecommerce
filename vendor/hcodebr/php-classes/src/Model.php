<?php


namespace Hcode;//namespace da classe

class Model {

	private $values = [];//vai ter todos os valores dos campos que tem dentro do objeto, no caso do usuario, os dados do objeto usuario: iduser, deslogin, despassword e etc

	public function __call($name, $args){//para saber qual metodo foi chamado
		//$name = nome do metodo
		//$args = parametros q foram passados


		$method = substr($name, 0, 3);//pegando os tres primeiros caracteres do nome do metodo(get ou set)

		$fieldName = substr($name, 3, strlen($name));//pegando o nome do campo que foi chamado, vai pegar todas os caracteres depois dos tres primeiros

		switch ($method) {//Detcta se $metodo é = get ou set
			case "get":
					return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;//retorna o valor do atributo $values na posição $fieldname, 
				break;

			case "set":
					$this->values[$fieldName] = $args[0];//atribuindo valor do parametro $args na posição 0 ao index $fieldname do atributo $values
				break;
		}

	}


	public function setData($data = array()){//por padrao é um array vazio

		//pega todos os campos retornados pela consulta e cria um atributo com o valor de cada uma dessas informações

		foreach ($data as $key => $value) {
			
			$this->{"set".$key}($value);//criando um metodo dinamicamente?
			//por ser um metodo, ele sera lido pelo __call()?
			// "set".nome do campo (valor do campo)
			//         $name        $args          da função __call()

			// seta as informações

			//os resultados serao armazenados no atributo privado $values por causa de __call()
		}


	}

	public function getValues(){

		return $this->values;

	}


}

?>