<?php
session_start();
$user_id = $_SESSION['id'];
$status = $_SESSION['role'];
$username = $_SESSION['name'];
$connect=$db =mysqli_connect('localhost','u750269652_singida','Gudboy24@','u750269652_singida')
or die("connection Failed");

  $query="select * from news";
  $result4=mysqli_query($connect,$query);

 ?>




<!DOCTYPE html>
<html lang="en">

<?php include "head/head.php" ?>

<body>
  <?php include "../header/header.php" ?>
  <?php include "aside/aside.php" ?>

  <main id="main" class="main">

    

            

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">


                <div class="card-body">
                  <h5 class="card-title">All Data <span>| Today</span> <a href="add.php"><span class="badge bg-success text-white">+ Add News</span></a></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Author</th>
                        <th scope="col">Title</th>
                        <th scope="col">Image</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php   // LOOP TILL END OF DATA
                                                    while($rows4=$result4->fetch_assoc())
                                                         {
                                                       ?>
                      <tr>
                        <td><?php echo $rows4['date'];?></td>
                        <td><?php echo $rows4['author'];?></td>
                        <td><?php echo $rows4['caption'];?></td>
                        <td><img src="../cv/<?php echo $rows4['image'];?>" alt="Profile" class="rounded-circle" style="height: 50px; width: 50px"></td>
                        <td><a href="update.php?id=<?php echo $rows4['id']; ?>"><i class="bi bi-pen" style="color: green; padding-right: 15px;"></i></a>
                        <a href="delete.php?id=<?php echo $rows4['id'];?> ?>"><i class="bi bi-archive-fill" style="color: red;"></i></a></td>
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
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">




        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include "../footer/footer.php" ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.min.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>

</body>

</html>