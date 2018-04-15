<?php
define('USER', 'root');
define('PASS','');
define('HOST','localhost');
define('DBNAME','teste');
define('TYPEBD','mysql');

class Db{
private $conn, $table = 'users', $logfile = 'LogDb.txt', $numbers_indices_search  = array(), $name_indices_search = array(), $search_params= array();
function __construct(){
		$this->getConn();
	}
	
function getConn(){
	try{	
	if(is_null($this->conn)){
	$this->conn =  new PDO(TYPEBD.':host='.HOST.';dbname='.DBNAME.';charset=utf8', USER,PASS, array(PDO::ATTR_PERSISTENT => true));	
	$this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	}
	
	return $this->conn;
	}catch(Exception $e){	
	   $this->writeError($e);	
	}
}
public function setTable($table){
	$this->table = $table;
	}
	
function writeError($e){
	echo 'Erro -> ';
	$errorx = $e->getMessage();
    var_dump($errorx);	
	$fp = fopen($this->logfile, 'a');
	$write = fwrite($fp,date("d-m-Y H:i:s ").": Erro-> ");
	$write = fwrite($fp,$errorx.PHP_EOL.PHP_EOL);
	fclose($fp);
}	
		
public function getTable(){ return $this->table;}

public function getNumbersIndicesSearch(){return $this->numbers_indices_search;}
public function setNumbersIndicesSearch($indicesSearch){$this->numbers_indices_search[] = $indicesSearch;}
public function resetNumbersIndicesSearch(){$this->numbers_indices_search = array();}
public function setNameIndicesNumbers($ind, $value){ $this->name_indices_search[$ind] = $value;}
public function getNameIndicesNumbers($ind){ return $this->name_indices_search[$ind];} 
public function resetNameIndicesNumbers(){$this->name_indices_search = array();}


public function setSearchParams($searchs = array()){
	if(is_array($searchs)){
	$this->search_params = $searchs;
	}
	}
public function getSearchParams(){
	return $this->search_params;
	}	
public function addSearchParams($val){
	$this->search_params[] = $val;
	}

public function resetSearchParams(){$this->search_params = array();}

public function isIndiceSearch($ind){
	$verify = false;
	foreach($this->getNumbersIndicesSearch() as $inds){
		if($inds == $ind){ $verify = true; 
		}
		}
	return $verify;
	}

private function getStr($data){
	$dt =  array();
	$indices = is_array($data)? array_keys($data) : array();
	$indices_search = $this->getSearchParams();
	$dt['ind'] = $indices;
	$count = count($indices);
	$dt['count'] = $count; 
    $num = 0;
    $dt['str'] = '';
	$dt['qst'] = '';
    $dt['cmb'] = '';
	$dt['read'] = '';
    foreach($indices as $ind){
	$dt['str'] .=$ind;
	$dt['qst'] .='?';
	$dt['cmb'] .= $ind.'=?';
	$dt['read'] .= (!$this->verifyIfIsSearch($indices_search, $ind) && count($this->getSearchParams()) == 0) ? $ind.'=?' : $ind." LIKE ?";
	
	if($this->verifyIfIsSearch($indices_search, $ind)){ 
	$index_pesquisa = $num + 1;
	$this->setNumbersIndicesSearch($index_pesquisa);
	$this->setNameIndicesNumbers($index_pesquisa,":".$ind);
	}
	
	if($num+1 < $count){ 
	$dt['str'] .=','; 
	$dt['qst'] .=',';
	$dt['cmb'] .=',';
	$dt['read'] .= ' AND ';	
	}
	$num++;
	}
	
	return $dt;
	}
function prepareSql($pdo, $dta,$dx){
	$count = $dx['count'];
	$count2 = count($dta);
	if($count == $count2){
      for($i = 0; $i < $count ; $i++){
		  $v = $i + 1 ;
		  $pdo = $this->getVarTp($v,$dta[$dx['ind'][$i]], $pdo);
		}
	$pdo->execute();  
	}
	}
	
function prepareSql2($pdo, $dta,$dx){
	$count = is_array($dx)? $dx['count'] : 0;
	$count2 = count($dta);
	if($count == $count2){
      for($i = 0; $i < $count ; $i++){
		  $v = $i + 1 ;
		  $pdo = $this->getVarTp($v,$dta[$dx['ind'][$i]], $pdo); 
		}
	$pdo->execute();  
	}
	$this->resetNumbersIndicesSearch();
	$this->resetNameIndicesNumbers();
	$this->resetSearchParams();
	return $pdo;
	}	


public function verifyIfIsSearch($indices_search, $value){
	$search = false;
	if(is_array($indices_search)){
	foreach($indices_search as $inds){
		if((string)$inds == (string)$value){
			$search = true;				
		}
	}
	}
	return $search;
}
			
function getVarTp($indice,  $value, $pdo){
	$tp_data = gettype($value);
	$isSearchValue = $this->isIndiceSearch($indice);
	if($isSearchValue == true){	
        $search = "%".(string)$value."%";
		$pdo->bindValue($indice,$search , PDO::PARAM_STR );	
    }else{
	switch($tp_data){
		case "integer":
		$pdo->bindValue($indice, $value, PDO::PARAM_INT );
		break;
	
		case "string":
		$pdo->bindValue($indice, $value, PDO::PARAM_STR );
		break;
		
		case "NULL":
		$pdo->bindValue($indice, $value, PDO::PARAM_NULL );
		break;
		
		case "":
		$pdo->bindValue($indice, $value, PDO::PARAM_NULL );
		break;
		
		case "double":
		$pdo->bindValue($indice, $value, PDO::PARAM_STR );
		break;
		
		case "boolean":
		$pdo->bindValue($indice, $value, PDO::PARAM_BOOL);
		break;
	
		default:
		$pdo->bindValue($indice, $value, PDO::PARAM_STR );	
	}
	}
	return $pdo;
}	
		
function Prepare($sql,$arg = '',$arg2 = '',$arg3='',$arg4='',$arg5 = ''){
		try{
		$data = array();
		$dados = $this->getConn()->prepare($sql);			
		if(trim($arg) <> '' ){   $dados = $this->getVarTp(1,  $arg, $dados);   }
		if(trim($arg2) <> '' ){  $dados = $this->getVarTp(2,  $arg2, $dados);   }
		if(trim($arg3) <> '' ){  $dados = $this->getVarTp(3,  $arg3, $dados);   }
		if(trim($arg4) <> '' ){  $dados = $this->getVarTp(4,  $arg4, $dados);    }
		if(trim($arg5) <> '' ){  $dados = $this->getVarTp(5,  $arg5, $dados);     }
		$dados->execute();
		$dados->setFetchMode(PDO::FETCH_ASSOC);
		$data['count'] = $dados->rowCount();
		$data['data'] = $dados->fetchAll();
		return $data;
		}catch(Exception $e){ $this->writeError($e);}
	}	
	
public function Create($data,$table = ''){
	try{
	$tab = trim($table) == '' ? $this->table : $table;
	$dt = $this->getStr($data);
	$sql = "INSERT INTO ".$tab."(".$dt['str'].") Values(".$dt['qst'].")";
	$pdo = $this->getConn()->prepare($sql);
	$this->prepareSql($pdo,$data,$dt);
	}catch(Exception $e){
		$this->writeError($e);	
		}
    }
public function Read($where = '', $table = '', $limit = "",$orderby = ""){
	try{
	$tab = trim($table) == '' ? $this->table : $table;
	$sql = "SELECT * FROM ".$tab;
	$sql = strip_tags(trim($where == ''))? $sql : $sql." WHERE ".$where;
	
	
	$sql = (trim($orderby) <> '')? $sql." ORDER BY ".$orderby : $sql; 
	$sql = (trim($limit) <> '') ? $sql.$limit  :$sql ;
	
	$pdo = $this->getConn()->query($sql);
	$pdo->setFetchMode(PDO::FETCH_ASSOC);
	return $pdo->fetchAll();
	}catch(Exception $e){
		$this->writeError($e);	
		}
	}
	
public function ReadPdo($where = array(), $table = '', $limit = "",$orderby = "", $search_params = array()){
	try{
	$tab = trim($table) == '' ? $this->table : $table;
	$sql = "SELECT * FROM ".$tab." ";
	$strWher =  is_array($where) && count($where) > 0 ? $this->getStr($where) : '';
	$str_where = "";
	
	if((is_array($where) && strpos($strWher['read'], "WHERE"))){
		$str_where = $strWher['read'];
		}
	else if(is_array($where) && !(strpos($strWher['read'], "WHERE"))){
		$str_where = " WHERE ".$strWher['read'];
		}
		
	else if(!(is_array($where)) && !(strpos($where, "WHERE")) && trim($where) <> ''){
		$str_where = " WHERE ".$where;
		}
	$sql .= $str_where;
	
	$sql .= $orderby.' '.$limit;
	
	$pdo = $this->getConn()->prepare($sql);
	$pdo = $this->prepareSql2($pdo,$where,$strWher);
	$pdo->setFetchMode(PDO::FETCH_ASSOC);
	$dat = array();
	$dat['count'] = $pdo->rowCount();
	$dat['data'] = $pdo->fetchAll();
    return $dat;
	
	}catch(Exception $e){
		$this->writeError($e);	
		}
	}		
	
public function Update($id,$camp = 'id',$data,$table){
	try{
	$tab = trim($table) == '' ? $this->table : $table;
	$dt =  $this->getStr($data);
	$sql = "UPDATE ".$tab." SET ".$dt['cmb']." WHERE ".$camp."='".$id."'";
	$pdo = $this->getConn()->prepare($sql);
	$this->prepareSql($pdo,$data,$dt);
	}catch(Exception $e){
	$this->writeError($e);	
		}
	}
	
public function Delete($id, $camp='id', $table = ''){
	try{
	$tab = trim($table) == '' ? $this->table : $table;
	$sql = "DELETE FROM ".$tab." WHERE ".$camp."='".$id."'";
	$pdo = $this->getConn()->query($sql);
	}catch(Exception $e){
	$this->writeError($e);	
		}
	}
	
	
function create_guid()
{
    $microTime = microtime();
    list($a_dec, $a_sec) = explode(' ', $microTime);

    $dec_hex = dechex($a_dec * 1000000);
    $sec_hex = dechex($a_sec);

    $this->ensure_length($dec_hex, 5);
    $this->ensure_length($sec_hex, 6);

    $guid = '';
    $guid .= $dec_hex;
    $guid .= $this->create_guid_section(3);
    $guid .= '-';
    $guid .= $this->create_guid_section(4);
    $guid .= '-';
    $guid .= $this->create_guid_section(4);
    $guid .= '-';
    $guid .= $this->create_guid_section(4);
    $guid .= '-';
    $guid .= $sec_hex;
    $guid .= $this->create_guid_section(6);

    return $guid;
}	
	

public function create_guid_section($characters)
{
    $return = '';
    for ($i = 0; $i < $characters; ++$i) {
        $return .= dechex(mt_rand(0, 15));
    }

    return $return;
}

public function ensure_length(&$string, $length)
{
    $strlen = strlen($string);
    if ($strlen < $length) {
        $string = str_pad($string, $length, '0');
    } elseif ($strlen > $length) {
        $string = substr($string, 0, $length);
    }
}

public function microtime_diff($a, $b)
{
    list($a_dec, $a_sec) = explode(' ', $a);
    list($b_dec, $b_sec) = explode(' ', $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
}
	
}
