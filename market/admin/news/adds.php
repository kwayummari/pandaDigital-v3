<?php
$connect=$db =mysqli_connect('localhost','u750269652_singida','Gudboy24@','u750269652_singida')
or die("connection Failed");
if (isset($_POST['submit'])) {
    $date=$_POST['date'];
    $caption=$_POST['caption'];
    $news=$_POST['news'];
    $author='Singida Big Stars';


  $file = $_FILES["cv"]["name"];
   $path = $_FILES['cv']['tmp_name'];
   $folder = "../cv/";
   $final_name = str_replace(" ", "-", $file);
   
//   var_dump ($final_name);

  $query="INSERT INTO news (date,caption,news,author,image)
    VALUES('$date', '$caption', '$news', '$author', '$final_name')";

     
     $insert=mysqli_query($connect,$query);
     

  if($insert) {
    move_uploaded_file($path, $folder . $final_name);
    echo "News Added Successfully";
} else {
    echo "Failed To Add News";
}

}
 ?>