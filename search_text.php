<?php

$text = $_GET["keyword"];
session_destroy(); 

require 'vendor/autoload.php';
include('include/adodb/adodb.inc.php');

$servername = "localhost";
$username = "root";
$password = "";
$db = "corpus_bhsindonew";
$driver = 'mysqli';

require_once ('vendor/autoload.php');

use Stichoza\GoogleTranslate\TranslateClient;

$tr = new TranslateClient('id', 'en');

echo $tr->translate($_POST['keyword']);
// Create connection
$conn = new mysqli($servername, $username, $password,$db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//echo "Connected successfully";

/*
Tahap Mencari Kata pada artikel
*/

$adodb = ADONewConnection($driver); # eg. 'mysql' or 'oci8'
	
$adodb->Connect($servername, $username, $password, $db);

$sql = "select stoplist from tb_stoplist";
$rstoplist = $adodb->GetAll($sql);

foreach ($rstoplist as $key => $value) {
	$arr_stoplist[$value['stoplist']] = $value['stoplist'];
}

$sql = "select translated_word,word from word_translation";
$rstoplist = $adodb->GetAll($sql);

foreach ($rstoplist as $key => $value) {
	$arr_trans[$value['word']] = $value['translated_word'];
}


$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
$stemmer  = $stemmerFactory->createStemmer();

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["file-upload"]["name"]);

$dt_hasil = array();

$temp = str_replace(PHP_EOL,'.',$_POST['keyword']);

$kalimat = explode('.', $temp);

if($_FILES['file-upload']['name']){
	move_uploaded_file($_FILES["file-upload"]["tmp_name"], $target_file);
	$uploads = file_get_contents(''.$target_file, true);
	$kalimat = explode('.', $uploads);
}
$i=0;

if(1<2){
	foreach($kalimat as $keykel => $kel){
		$kel = trim($kel);
		$result = explode(' ', $kel);

		foreach ($result as $key => $value) {

			/*else {*/
				$sql = "select id_class from word_class wc
					join master_class mc using (id_class)
					where lower(word) = '".strtolower($value)."'";
					
					$hsl2 = $conn->query($sql);

					while ($row2 = $hsl2->fetch_assoc()) {
						$dt_hasil[$keykel][$value] = $row2['id_class'];
					}

				$output   = $stemmer->stem($value);
				
				if(!array_key_exists($value,$dt_hasil) and $value != $output){
						$imbuhan = str_replace($output, '-', $value);
						
						$sql = "select id_class from word_class_rule wc
						join master_class mc using (id_class)
						where lower(charachteristic) = '".strtolower($imbuhan)."'";
						$hsl = $conn->query($sql);
						
						while($row = $hsl->fetch_assoc()) {
								if(!empty($row['id_class']))
								$dt_hasil[$keykel][$value] = $row['id_class'];
						}
				}

				if(!array_key_exists($value,$dt_hasil)){

					$imbuhan = substr($value, 0,2)."-";

					$sql = "select id_class from word_class_rule wc
					join master_class mc using (id_class)
					where lower(charachteristic) = '".strtolower($imbuhan)."'";
					$hsl3 = $conn->query($sql);
					while($row3 = $hsl3->fetch_assoc()) {
								$dt_hasil[$keykel][$value] = $row3['id_class'];
					}
					
				}
		}
	}

foreach($kalimat as $keykel => $kel){
	$result = explode(' ', $kel);
	$hasverb = false;
	for ($i=0; $i < count($result); $i++) {
		if($i>0){ 
			$predata[$keykel][$result[$i]]['before']=$predata[$keykel][$result[$i-1]]['class'];
		}
			$predata[$keykel][$result[$i]]['after']=$predata[$keykel][$result[$i+1]]['class'];
			$predata[$keykel][$result[$i]]['class']=$dt_hasil[$keykel][$result[$i]];
			if(empty($predata[$keykel][$result[$i]]['class']))
				$predata[$keykel][$result[$i]]['class'] = '1';
			
			if($predata[$keykel][$result[$i]]['class'] == '2'){
				$hasverb = true;
				$predata[$keykel]['hasverb'] = $hasverb;
			} 
	}
}

 foreach($predata as $keyp => $rawdata){
	$i=0;
		if(!$predata[$keyp]['hasverb']){
			continue;
		}
		foreach ($rawdata as $key => $value) {
			$class = $value['class'];
			$before = $value['before'];
			$after = $value['after'];

			
			$sql = "select structure_code from word_sentence_structure_detail where id_class = '$class' ";
			if($before)
				$sql .= " and before_class = '$before' ";
			if($after)
				$sql .= " and after_class = '$after' ";
				
			$sql .= " limit 1";
			$rs = $adodb->GetOne($sql);

			if(!empty($rs)){
				$sentences = $key;
				
				if($rs == 'O'){
					if(array_key_exists(trim($key),$arr_stoplist)){
						$rsprev = $rs;
						continue;
					}else{
						$rsprev = $rs;
						$a_sentences[$keyp][$rs][$i]= $sentences;
					}
				} else {
						$a_sentences[$keyp][$rs]= $sentences;
				}
			}
			else {
					if($key != 'hasverb' and !array_key_exists(trim($key),$arr_stoplist))
						$a_sentences[$keyp][$rsprev][$i] = $key;
			}  
			$i++;
			
		}

		$a_sentences[$keyp]['structure'] = implode(",", array_keys($a_sentences[$keyp]));
	}

}

foreach($kalimat as $keykel => $kel){
	
	$structure = $a_sentences[$keykel]['structure'];

	if(!$predata[$keykel]['hasverb']){
		continue;
	}
	
	$sql = "select * from format_sentences where format = '$structure' ";
	$datastructure = $adodb->GetRow($sql);

	$verb   = $stemmer->stem($a_sentences[$keykel][$datastructure['verb']]);
	
	$element = end($a_sentences[$keykel][$datastructure['element']]);
	
	$value = $a_sentences[$keykel][$datastructure['value']];
	
	if($value=='')
		$str[$keykel] = '$i->'.$arr_trans[$verb] ."('".$element."');<br/>";
	else
		$str[$keykel] = '$i->'.$arr_trans[$verb] ."('#".$element."','".$value."');<br/>";

	echo $str[$keykel];
}

session_start();

$_SESSION["hasil"] = implode(" ",$str);
$_SESSION["teksasli"] = trim($_POST['keyword']);
$_SESSION['structure'] = $a_sentences;

//header("Location: http://localhost/nlp-dynamicscenario/hasil.php");

//$sentence = "mengatakan halo untuk pemimpin dunia"; 
//$sentence = 'Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan,Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan';

?>
