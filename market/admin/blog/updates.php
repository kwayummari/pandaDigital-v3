<?php
$connect=$db =mysqli_connect('localhost','u750269652_tda','Tda@2022','u750269652_tda')
or die("connection Failed");
if (isset($_POST['submit'])) {
    $id=$_POST['id'];
    $title=$_POST['title'];
    $date=$_POST['date'];
    $description=$_POST['description'];


  $file = $_FILES["cv"]["name"];
   $path = $_FILES['cv']['tmp_name'];
   $folder = "../cv/";
   $final_name = str_replace(" ", "-", $file);
   

//   $query="INSERT INTO feeds (title,date,author,caption,description,image)
//     VALUES('$title','$date', '$author','$caption','$description','$final_name')";
    $query = "UPDATE blog SET title='$title', date = '$date', description = '$description', image='$final_name' WHERE id = '$id'";

     
     $insert=mysqli_query($connect,$query);
     move_uploaded_file($path, $folder . $final_name);

  if($insert) {
    
    echo "Blog Updated Successfully";
} else {
    echo "Failed To Update Blog";
}

}
 ?>