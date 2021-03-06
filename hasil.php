<?php 
include('include/adodb/adodb.inc.php');

$servername = "localhost";
$username = "root";
$password = "";
$db = "corpus_bhsindo";
$driver = 'mysqli';

session_start();

// Create connection
$conn = new mysqli($servername, $username, $password,$db);

$sql = "SELECT * FROM tb_artikel where count > 0 order by rank asc";
$result = $conn->query($sql);

$sql = "select id_class,name_class from master_class";

$hsl = $conn->query($sql);
         
?>
<html>
<head>
<title>Search Engine</title>
<style type="text/css">
textarea {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
    resize:none;
	width: 100%;
}
@font-face {
    font-family: Reckoner;
    src: url(ui/css/Reckoner_Bold.ttf);
}
#wrapper{
    width: 100%;
    padding-left: 50px;
    margin-top: 20px;
}
#title a{
    font-size: 30px;
    /*font-family: Reckoner;*/
    color : #000000;
    text-decoration: none;
}

#result_content {
    font-size: 14px;
    border-radius: 4px;
    line-height: 20px;
    background-color: #FFFFFF;
    border: 1px solid #CCCCCC;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    transition: border 0.2s linear 0s, box-shadow 0.2s linear 0s;
}
.result-title{
    margin-bottom: 5px;    
}
.result-title a{
    font-family:fantasy;
    margin-bottom: 5px;
    color: #0080FF;
    text-decoration: none;    
}
.xtime{
    margin-top: 5px;
    font-size: 13px;
    color: black;    
}

p.counting{
    margin-top: 0px;
    font-size: 13px;
    color: #969696;    
}
</style>
</head>
<body>
<div id="wrapper">
	<div id="title"><a href="home">Codeception Code Generator</a></div>
    <div id="content">
        <form action="search_text.php" method="post" enctype="multipart/form-data">
            <table width="100%">
            <tr>
                <td>
                    <textarea rows="10" class="text-input" id="keyword" name="keyword" >
                    <?php echo trim($_SESSION['teksasli'])?>
                    </textarea>
                </td>
                <td>    
                    <textarea rows="10" class="text-input" id="result_content" name="result_content"><?php echo $_SESSION["hasil"]?></textarea>
                </td>
            </tr>
            
            <tr><td><input type="file" id="file-upload" name="file-upload"></td></tr>
            <tr>
                <td>
                    <input type="submit" name="submit" value="submit">
                </td>
                </tr>
            </table>
        </form>
        </div>
        <div id="result">
        <?php
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()) {
                    echo "<span class='result-title'><a>".$row['judul_artikel']."</a></span><br>";
                    echo "<p align='justify'>".limit_text($row['isi_artikel'],20)."...</p>";
                    echo "<p class='counting' align='justify'>Word found : ".$row['count']." times</p><br>";
                }
            }
        ?>
        </div>
        <div>
            <?php
            if($hsl->num_rows>0){
                while($row = $hsl->fetch_assoc()) {
                    echo "<span class='xtime'>".$row['id_class']."-".$row['name_class']."<br/></span>";
                }
            }
        ?>
        </div>
    </div>
</div>
</body>
</html>
