<?php
$user ='root';
$pass ='';

try{

    $db = new PDO ('mysql:host=localhost;dbname=lemina',$user,$pass);

           foreach($db->query('SELECT*FROM articles')as $row){

                     print_r($row);
           }
} 
catch(PDOException $e){

print "Erreur :". $e->getMessage() . "<br/>";
die;

}


?>