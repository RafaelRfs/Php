<?php 

include_once('db.class.php');
$pdo = new Db();

//$dx['nome'] = 'Rafael';

//$dx['id'] = 2;
$dx['nome'] ="Ze";
//$search[0] = 'id';
$search[1] = 'nome';

$pdo->setSearchParams($search);
$data = $pdo->ReadPdo($dx);

var_dump($data);


