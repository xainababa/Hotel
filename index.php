<!DOCTYPE html>
<html lang="en">
<?php require_once('check_login.php');?>
<?php include('head.php');?>
<?php include 'connect.php';?>
<?php include('header.php');?>
<?php include('sidebar.php');?>
<?php //echo  $_SESSION["email"];
 date_default_timezone_set('Asia/Kolkata');
 $current_date = date('Y-m-d');?>
   
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Dashboard</h3> </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fa fa-address-card f-s-40 color-primary"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <?php $sql="SELECT COUNT(*) FROM `tbl_rooms`";
                                $res = $conn->query($sql);
                                $row=mysqli_fetch_array($res);?> 
                                    <h2 class="color-black"><?php echo $row[0];?></h2>
                                    <p class="m-b-0">Total rooms</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fa fa-users f-s-40 color-success"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <?php $sql="SELECT COUNT(*) FROM `tbl_customer`";
                                $res = $conn->query($sql);
                                $row=mysqli_fetch_array($res);?> 
                                    <h2 class="color-black"><?php echo $row[0];?></h2>
                                    <p class="m-b-0">total customers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fa fa-book f-s-40 color-warning"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <?php $sql="SELECT COUNT(*) FROM `tbl_booking`";
                                $res = $conn->query($sql);
                                $row=mysqli_fetch_array($res);?> 
                                    <h2 class="color-black"><?php echo $row[0];?></h2>
                                    <p class="m-b-0">total bookings</p>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
           
                
        
                <div class="row justify">
                    <div class="col-md-10">
                        <div class="card p-30" style="width:1000px;height: 900px">
                            <div class="box box-danger">
                                <div class="box-body ">
                                  <!-- THE CALENDAR -->
                                  <div id="calendar">
                                      
                                  </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /. box -->
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- End Container fluid  -->
                <!-- End PAge Content -->
      <!-- footer -->
             <?php include('footer.php');?>
<script>
  $(function () {
    <?php
    include 'connect.php';
    $sql = "SELECT * FROM `tbl_booking`";
     $result = $conn->query($sql);
  $i=0;
  $display_appoint=array();
   while($row = $result->fetch_assoc()) { 
    $sql2 = "SELECT * FROM `tbl_customer` WHERE id='".$row['name']."'";
    $result2=$conn->query($sql2);
    $row2=$result2->fetch_assoc();
    $sql3 = "SELECT * FROM `tbl_rooms` WHERE id='".$row['roomname']."'";
    $result3=$conn->query($sql3);
    $row3=$result3->fetch_assoc();
    $display_appoint[$i]['name']=$row2['name'];
    $display_appoint[$i]['fromdate']=$row['fromdate'];
    $display_appoint[$i]['todate']=$row['todate'];
    $display_appoint[$i]['roomname']=$row3['roomname'];
    $display_appoint[$i]['color']=$row3['color'];
    $i++;
}
    ?>
