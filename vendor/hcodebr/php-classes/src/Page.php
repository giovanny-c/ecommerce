<?php

namespace Hcode;//a classe esta no namespace hcode


use Rain\Tpl; // usando o namespace rain, quando for chamado a classe tpl


	class Page{


        private $tpl;
        private $options = [];//array
        private $defaults = [  //opções padrao para a construção
                         "header"=>true,
                         "footer"=>true,
                         "data"=>[]
                            ];


		public function __construct($opts = array(), $tpl_dir = "/views/"){
		//$opts por padrao é array		
		//$tpl_dir por padrao é "/views/"
        //vai construir a pagina
			
			$this->options = array_merge($this->defaults, $opts);//atributo $options é igual ao $defaults +  o que vier de parametro no metodo construtor  

			$config = array(
			//$_SERVER["DOCUMENT ROOT"] vai trazer a pasta root do seu servidor, no caso será "ecommerce"
						"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,//pasta onde será encontrado os arquivos html "ecommerce/views"
						"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",//pasta do cache dos arquivos html
						"debug"         => false // set to false to improve the speed
					     );

		    Tpl::configure( $config );// configurando o tpl

		    $this->tpl = new Tpl;//criando a classe TPL, atributo tpl é da classe "Page"

		    $this->setData($this->options["data"]);
		    // os Dados vao estar na chave "data" do array "$options"
		    //usando o metodo setData para pegar os dados do array e atribuir a chave ao valor
		    // as variaveis vao vir de acordo com a rota (ver ecommerce/index.php)

		    if($this->options["header"] === true) $this->tpl->draw("header");//se o valor do index header de $options for true, desenha o template, usando o nome do arquivo,
		    // a estenção padrao é .html
		    //vai usar o arquivo "ecommerce/views/header.html" 

		    //var_dump($this->options);

		}



		private function setData($data = array()){
 			

 			foreach ($data as $key => $value) {
		    	$this->tpl->assign($key, $value);//usando o metodo 'assign' do TPL para atribuir a chave ao valor

		    }
				
		}


		public function setTpl($name, $data = array(), $returnHTML = false){
		//metodo para o conteudo da pagina
		//parametros (nome do template, dados, retorna o html ou nao)
            
        	
        	$this->setData($data); 	

        	return $this->tpl->draw($name, $returnHTML);//vai usar parametro $name para procurar um arquivo em"ecommerce/views/", vai chamar ele

        	//var_dump($data);

		}


		public function __destruct(){

			if($this->options["footer"] === true) $this->tpl->draw("footer");//se o index footer de $options for true "desenha" o footer  é o final do html onde pode ser colocado o java script e outras infos.
			//vai usar o arquivo "ecommerce/views/header.html"

		}



	}


?>