<?php
require 'vendor/autoload.php';

$servername = "localhost";
$username = "root";
$password = "";
$db = "corpus_bhsindo";

// Create connection
$conn = new mysqli($servername, $username, $password,$db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";

$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
$stemmer  = $stemmerFactory->createStemmer();
 


/*$sql = "SELECT * FROM tb_artikel";
$result = $conn->query($sql);

$sql2 = "SELECT * FROM tb_stoplist";
$result2 = $conn->query($sql2);

$sql3 = "SELECT * FROM tb_katadasar";
$result3 = $conn->query($sql3);

while($row3 = $result3->fetch_assoc()) {
	$tb_katadasar[$row2['katadasar']] = $row2['katadasar']; 
}

while($row2 = $result2->fetch_assoc()) {
	$tb_stoplist[$row2['stoplist']] = $row2['stoplist']; 
}

$kt_katadasar = array();

while($row = $result->fetch_assoc()) {
	$output   = $stemmer->stem($row['isi_artikel']);
	$katadasar = explode(" ", $output);
	
	foreach ($katadasar as $key => $value) {
		if(!in_array($value, $tb_stoplist)){
			$kt_katadasar[$row['id_artikel']][$value] = $value;
		}
	}

}


foreach ($kt_katadasar as $key => $value) {
	foreach ($value as $val) {
		if(!in_array($val, $tb_katadasar)){
			$sql = "INSERT INTO tb_katadasar (katadasar) VALUES ('$val')";
			mysqli_query($conn, $sql);
		}
	}
}

*/

//$sentence = "mengatakan halo untuk pemimpin dunia"; 
//$sentence = 'Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan,Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan';

?>