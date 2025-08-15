<?php
session_start();
$user_id = $_SESSION['id'];
$status = $_SESSION['role'];
$username = $_SESSION['name'];
include '../connection/index.php';

  $query="select * from blog";
  $result4=mysqli_query($connect,$query);
  // $count=mysqli_fetch_assoc($result4);
  $total_users = mysqli_num_rows($result4);

 ?>




<!DOCTYPE html>
<html lang="en">

<?php include "../head/head.php"; ?>

<body>

  <!-- ======= Header ======= -->
  <?php include "../header/header.php"; ?>

  <?php include "../aside/index.php" ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Users</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../home.php">Home</a></li>
          <li class="breadcrumb-item active">Users</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <!-- <div class="col-lg-8"> -->
          

            

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">


                <div class="card-body">
                  <h5 class="card-title">Blog <span>| Today</span> <a href="add.php"><span class="badge bg-success text-white">+ Add Blog</span></a></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">Image</th>
                        <th scope="col">Title</th>
                        <th scope="col">Date</th>
                        <th scope="col">Joining Word</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php   // LOOP TILL END OF DATA
                                                    while($rows4=$result4->fetch_assoc())
                                                         {
                                                       ?>
                      <tr>
                        <td><img src="../cv/<?php echo $rows4['image'];?>" alt="Profile" class="rounded-circle" style="height: 50px; width: 50px"></td>
                        <td><a href="#" class="text-primary"><?php echo $rows4['title'];?></a></td>
                        <td><?php echo $rows4['date'];?></td>
                        <td><a href="update_blog.php?id=<?php echo $rows4['id']; ?>"><i class="bi bi-pen" style="color: green;"></i></a></td>
                        <td><a href="delete_blog.php?id=<?php echo $rows4['id']; ?>"><i class="bi bi-archive-fill" style="color: red;"></i></a></td>
                      </tr>
                      <?php
                                                     }
                                                     ?>
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->

            <!-- Top Selling -->
            

          </div>
        <!-- </div> -->
        <!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">




        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <?php include "../footer/index.php"; ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <?php include "../js/index.php"; ?>

</body>

</html>