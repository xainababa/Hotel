<?php 
require_once('check_login.php');
include('head.php');
include('header.php');
include('sidebar.php');

include('connect.php');

if(isset($_POST["btn_update"])) {
    extract($_POST);

    $target_dir = "uploadImage/Profile/";
    $image1 = basename($_FILES["image"]["name"]);
    
    if($_FILES["image"]["tmp_name"] != '') {
        $image = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            @unlink("uploadImage/Profile/".$_POST['old_image']);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        $image1 = $_POST['old_image'];
    }
    
    $q1 = "UPDATE `admin` SET `fname`='$fname',`lname`='$lname',`email`='$email',`contact`='$contact',`dob`='$dob',`gender`='$gender',`image`='$image1' where id = '".$_SESSION["id"]."'";
    
    if ($conn->query($q1) === TRUE) {
        $_SESSION['success'] = 'Profile Successfully Updated';
        ?>
        <script type="text/javascript">
            window.location = "profile.php";
        </script>
        <?php
    } else {
        $_SESSION['error'] = 'Something Went Wrong';
    }
}

$que = "select * from admin where id = '".$_SESSION["id"]."'";
$query = $conn->query($que);
while($row = mysqli_fetch_array($query)) {
    extract($row);
    $fname = $row['fname'];
    $lname = $row['lname'];
    $email = $row['email'];
    $contact = $row['contact'];
    $dob1 = $row['dob'];
    $gender = $row['gender'];
    $image = $row['image'];
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Hotel Booking System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #3c8dbc;
            --secondary-color: #f39c12;
            --success-color: #00a65a;
            --warning-color: #f39c12;
            --danger-color: #dd4b39;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), #2c3e50);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .profile-img-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid rgba(255, 255, 255, 0.3);
        }
        
        .profile-upload-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--secondary-color);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .profile-upload-btn:hover {
            background: #e67e22;
            transform: scale(1.1);
        }
        
        .profile-body {
            padding: 30px;
            background: white;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #2c6a9c;
            border-color: #2c6a9c;
            transform: translateY(-2px);
        }
        
        .input-group-text {
            background-color: #f8f9fa;
        }
        
        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f4f6f9;
        }
        
        @media (max-width: 768px) {
            .profile-header {
                padding: 20px;
            }
            
            .profile-img {
                width: 120px;
                height: 120px;
            }
            
            .profile-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Page wrapper -->
    <div class="page-wrapper">
        <!-- Bread crumb -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary"><i class="fas fa-user me-2"></i> Profile Settings</h3> 
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>
        </div>
        <!-- End Bread crumb -->
        
        <!-- Container fluid -->
        <div class="container-fluid">
            <!-- Start Page Content -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="profile-img-container">
                                <img class="profile-img" src="uploadImage/Profile/<?= $image ?>" alt="Profile Image">
                                <label for="imageUpload" class="profile-upload-btn">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                            <h4><?php echo $fname . ' ' . $lname; ?></h4>
                            <p>Administrator</p>
                        </div>
                        
                        <div class="profile-body">
                            <h4 class="section-title">Personal Information</h4>
                            
                            <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                                <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*">
                                <input type="hidden" value="<?= $image ?>" name="old_image">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" value="<?php echo $fname; ?>" name="fname" class="form-control" onkeydown="return alphaOnly(event);" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" value="<?php echo $lname; ?>" name="lname" class="form-control" onkeydown="return alphaOnly(event);" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" value="<?php echo $email; ?>" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" value="<?php echo $contact; ?>" name="contact" class="form-control" id="tbNumbers" minlength="10" maxlength="10" onkeypress="javascript:return isNumber(event)" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Gender</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                            <select name="gender" class="form-control" required>
                                                <option value="Male" <?php if($gender=="Male") echo "selected"; ?>>Male</option>
                                                <option value="Female" <?php if($gender=="Female") echo "selected"; ?>>Female</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date of Birth</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            <input type="date" value="<?php echo $dob1; ?>" name="dob" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        <button type="submit" name="btn_update" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i> Update Profile
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Page Content -->
        </div>
        <!-- End Container fluid -->
    </div>
    <!-- End Page wrapper -->

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Image preview when selecting a new file
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-img').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Input validation functions
        function alphaOnly(event) {
            var key = event.keyCode;
            return ((key >= 65 && key <= 90) || key == 8 || key == 32);
        };
        
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
        
        // Show notification popups
        <?php if(!empty($_SESSION['success'])): ?>
            setTimeout(function() {
                alert("Success: <?php echo $_SESSION['success']; ?>");
            }, 500);
        <?php unset($_SESSION["success"]); endif; ?>
        
        <?php if(!empty($_SESSION['error'])): ?>
            setTimeout(function() {
                alert("Error: <?php echo $_SESSION['error']; ?>");
            }, 500);
        <?php unset($_SESSION["error"]); endif; ?>
    </script>
</body>
</html>

<?php include('footer.php'); ?>