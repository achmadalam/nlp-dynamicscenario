<?php
include('../include/adodb/adodb.inc.php');

$servername = "localhost";
$username = "root";
$password = "";
$db = "corpus_bhsindo";
$driver = 'mysqli';

$adodb = ADONewConnection($driver); # eg. 'mysql' or 'oci8'
$adodb->debug = true;
$adodb->Connect($servername, $username, $password, $db);


$files = glob("/var/www/html/kbbi/teks/*.txt");
$extracted_word = array();
foreach($files as $fl){
$fh = fopen($fl,'r');
	while ($line = fgets($fh)) {
		$content = explode(";",$line);
		if(strpos($content[0]," n ") > 0){	
			$important = explode(" n ",$content[0]);
		        $extracted = explode("/",$important[0]);
		        $word = explode(" ",$extracted[0]);
			$extracted_word[strtolower($word[0])] = strtolower($word[0]);
			//print_r($line."<br>");
		
		}
		
	}
}

// the MySQL insert statement.
$sql = "INSERT INTO word_class(id_class,word) values(?,?) ";
$sql2 = "select id_class,word from word_class";
$data = $adodb->getAll($sql2);

foreach($data as $key =>$dt)
	$ada[]=strtolower($dt['word']);
	
$adodb->beginTrans();

foreach($extracted_word as $ew)
{
	if($ew == 'A,' or $ew=='a' or $ew=='A')
		continue;

	$record = array('1',strtolower($ew));
	if(!in_array(strtolower($ew),$ada)){
		$d = $adodb->Execute($sql, $record);
		if (!$d) {
			print 'error' . $conn1->ErrorMsg() . '<br>';
		}
		
	}
}

$adodb->commitTrans();

fclose($fh);
?>
