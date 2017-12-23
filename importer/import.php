<?php
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
			$extracted_word[$word[0]] = $word[0];
			//print_r($line."<br>");
		
		}
		
	}
}
echo "<pre>";
foreach($extracted_word as $ew)
{
	print_r($ew."<br>");
}
print_r(count($extracted_word));

echo "</pre>";

fclose($fh);
?>
