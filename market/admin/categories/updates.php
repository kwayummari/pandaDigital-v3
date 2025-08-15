<?php
include '../connection/index.php';
if (isset($_POST['submit'])) {
    $id=$_POST['id'];
    $date=$_POST['date'];
    $caption=$_POST['caption'];
    $news=$_POST['news'];
    $author='Singida Big Stars';


  $file = $_FILES["cv"]["name"];
   $path = $_FILES['cv']['tmp_name'];
   $folder = "../cv/";
   $final_name = str_replace(" ", "-", $file);
   

    $query = "UPDATE events SET date='$date', caption = '$caption', news = '$news', image='$final_name' WHERE id = '$id'";

     
     $insert=mysqli_query($connect,$query);
     move_uploaded_file($path, $folder . $final_name);

  if($insert) {
    
    echo "News Updated Successfully";
} else {
    echo "Failed To Update News";
}

}
 ?>