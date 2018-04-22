<?php 

include_once('db.class.php');
$pdo = new Db();

echo "DATA: ".$pdo->getFileDataLast()."<br>";
$indiceOR[0] = 'usuarios.nome';
//$pdo->setIndicesOR($indiceOR);
$pdo->setIndicesOrString('usuarios.nome');
$pdo->setDistinct();
$pdo->setCamps('usuarios.nome as namexxx, usuarios.id as iduser, pst.id as idpost, pst.titulo');
$search[0] = 'usuarios.nome';
//$pdo->setSearchParams($search);
$pdo->setSearchString('usuarios.nome');
//$pdo->addRelationship($table_with_alias = "" , $connected_camps, $joinType = " INNER ");
$pdo->addRelationship(" posts as pst " , 'usuarios.id=pst.autor', $joinType = " INNER ");
$pdo->addRelationship(" posts as pst2 " , 'usuarios.id=pst2.autor', $joinType = " INNER ");
$pdo->addRelationship(" posts as pst3 " , 'usuarios.id=pst3.autor', $joinType = " INNER ");

$dx['usuarios.id'] = 1;
$dx['usuarios.nome'] ="Ra";
$data = $pdo->ReadPdo($dx, ' users as usuarios', ' 0,1 ', ' usuarios.nome DESC ', 'usuarios.id ');

echo json_encode($data);
$pdo->writeDataIntoFile();
