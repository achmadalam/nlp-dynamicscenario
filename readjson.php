<?php

$servername = "localhost";
$username = "root";
$password = "";
$db = "search_engine";

// Create connection
$conn = new mysqli($servername, $username, $password,$db);

$string = file_get_contents("data.json");


$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($string, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
    if(is_array($val)) {
        echo "$key:\n";
    } else {
        echo "$key => $val\n";
    }
    echo "<br/>";
}
?>