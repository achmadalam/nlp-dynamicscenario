<?php

$text = $_GET["keyword"];

require 'vendor/autoload.php';
include('include/adodb/adodb.inc.php');

$servername = "localhost";
$username = "root";
$password = "";
$db = "corpus_bhsindo";
$driver = 'mysqli';

// Create connection
$conn = new mysqli($servername, $username, $password,$db);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";

/*
Tahap Mencari Kata pada artikel
*/


$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
$stemmer  = $stemmerFactory->createStemmer();


$dt_hasil = array();

$result = explode(' ', $_POST['keyword']);

$i=0;

if(1<2){
foreach ($result as $key => $value) {
	/*else {*/
		$sql = "select id_class from word_class wc
			join master_class mc using (id_class)
			where lower(word) = '".strtolower($value)."'";
			
			$hsl2 = $conn->query($sql);
			
			while ($row2 = $hsl2->fetch_assoc()) {
				$dt_hasil[$value] = $row2['id_class'];
			}

		$output   = $stemmer->stem($value);
		
		if(!array_key_exists($value,$dt_hasil) and $value != $output){
				$imbuhan = str_replace($output, '-', $value);
				
				$sql = "select id_class from word_class_rule wc
				join master_class mc using (id_class)
				where charachteristic = '$imbuhan'";
				$hsl = $conn->query($sql);
				
				while($row = $hsl->fetch_assoc()) {
					    if(!empty($row['id_class']))
							$dt_hasil[$value] = $row['id_class'];
				}
		}

		if(!array_key_exists($value,$dt_hasil)){

			$imbuhan = substr($value, 0,2)."-";

			$sql = "select id_class from word_class_rule wc
			join master_class mc using (id_class)
			where charachteristic = '$imbuhan'";
			$hsl3 = $conn->query($sql);
			while($row3 = $hsl3->fetch_assoc()) {
						$dt_hasil[$value] = $row3['id_class'];
			}
			
		}

	/*}*/
}

print_r($dt_hasil);
for ($i=0; $i < count($result); $i++) {
    if($i>0){ 
 		$predata[$result[$i]]['before']=$predata[$result[$i-1]]['class'];
 	}
		$predata[$result[$i]]['after']=$predata[$result[$i+1]]['class'];
		$predata[$result[$i]]['class']=$dt_hasil[$result[$i]];
 }
 
 foreach ($predata as $key => $value) {
	 $class = $value['class'];
	 $before = $value['before'];
	 $after = $value['after'];

	$adodb = ADONewConnection($driver); # eg. 'mysql' or 'oci8'
	$adodb->debug = true;
	$adodb->Connect($servername, $username, $password, $db);
	$sql = "select detail_name from word_sentence_structure_detail where id_class = '$class' ";
	if($before)
		$sql .= " and before_class = '$before' ";
	if($after)
	    $sql .= " and after_class = '$after' ";
	$rs = $adodb->GetOne($sql);

	print_r($rs);
 }

print_r($predata);
print_r($dt_hasil);
}

/**
cara 2
**/
if(false){
	foreach ($result as $key => $value) {
		$sql = "select tag from corpus_tag wc
			where word = '$value' limit 1";
			$hsl = $conn->query($sql);
			print_r($sql);
			while($row = $hsl->fetch_assoc()) {
				$dt_hasil[$value] = $row['tag'];
			}
	}
}
die();
//header("Location: http://localhost/Project%20TA/hasil.php?keyword=$text");

//$sentence = "mengatakan halo untuk pemimpin dunia"; 
//$sentence = 'Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan,Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan';

?>