<?php

namespace Hcode;

class PageAdmin extends Page{//A classe extende de Page 

	public function __construct($opts = array(), $tpl_dir = "/views/admin/"){//é a mesma função só que para a pagina do admin
 
			parent::__construct($opts, $tpl_dir);//usando o metodo construct da classe pai com os parametros usados na classe filho


	}


}




?>