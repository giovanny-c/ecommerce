<?php 


session_start();//inicia o uso de sessao

require_once("vendor/autoload.php");// do composer, para trazer as dependencias necessarias do projeto

//quais namespaces/classes que serao usadas
use \Slim\Slim; // usando a classe Slim do namespace Slim

/* NAO E MAIS USADO POIS OS $app->get estao em arquivos separados
use \Hcode\Page;//usando a classe Page do namespace Hcode
use \Hcode\PageAdmin;//usando a classe PageAdmin do namespace Hcode
use \Hcode\Model\User;//usando a classe User do namespace Model do namespace Hcode
use \Hcode\Model\Category;
*/


$app = new Slim();//criando nova aplicação do slim para facilitar, cria uma nova rota?

$app->config('debug', true);


require_once("functions.php");
require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-login.php");
require_once("admin-products.php");
require_once("admin-orders.php");


// -------- OLHAR OS ARQUIVOS HTML DAS RESPECTIVAS ROTAS, POIS SÃO ALTERADOS PARA QUE OS DADOS SEJAM MOSTRADOS NA TELA DE MANEIRA CORETA -------------------------



$app->run();//roda o codigo que estiver dentro da rota

 ?>