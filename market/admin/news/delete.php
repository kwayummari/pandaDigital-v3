<?php
$connect=$db =mysqli_connect('localhost','u750269652_singida','Gudboy24@','u750269652_singida');
$id = $_GET['id'];
$sql= "DELETE FROM news WHERE id= '$id'";
$result=mysqli_query($connect,$sql);

if ($result) {
                header('location: index.php');
             }
mysqli_close($connect);
?>