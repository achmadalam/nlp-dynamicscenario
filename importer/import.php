<?php
include('../include/adodb/adodb.inc.php');

$servername = "localhost";
$username = "root";
$password = "";
$db = "corpus_bhsindonew";
$driver = 'mysqli';

$adodb = ADONewConnection($driver); # eg. 'mysql' or 'oci8'
$adodb->debug = true;
$adodb->Connect($servername, $username, $password, $db);

$files = glob('C:\xampp\htdocs\nlp-dynamicscenario\importer\kbbi\teks\*.txt');

$jenis = array(
	'n' => '1',
	'v' => '2',
	'a' => '3',
	'->' => null
);

foreach ($files as $file) {
	$fh = fopen($file,'r');
		while ($line = fgets($fh)) {
			$continue = true;
			foreach ($jenis as $kjenis => $vjenis) {
				if($continue && strpos($line, ' '.$kjenis.' ') !== false){
					list($kata) = explode(' '.$kjenis.' ', $line);
					list($fkata) = explode(', ', $kata);
					list($lkata) = explode(' ', $fkata);
					$continue = false;
					echo $lkata.' ADALAH '.$vjenis.PHP_EOL;
					$extracted_word[$i]['text'] = strtolower($lkata);
					$extracted_word[$i]['class'] = strtolower($vjenis);

					if(is_null($vjenis)){
						list($kata,$refkata) = explode(' '.$kjenis.' ', $line);
						$extracted_word[$i]['ref'] = strtolower($refkata);
					}
					$i++;
				}
			}
		}
}

// the MySQL insert statement.
$sql = "INSERT INTO word_class(word,id_class,ref_word) values(?,?,?) ";

$adodb->beginTrans();

foreach($extracted_word as $key => $ew)
{
		$record = array($ew['text'],strtolower($ew['class']),$ew['ref']);
	
		$d = $adodb->Execute($sql, $record);
		
		if (!$d) {
			print 'error' . $conn1->ErrorMsg() . '<br>';
		}
		
}

$adodb->commitTrans();

fclose($fh);
?>
