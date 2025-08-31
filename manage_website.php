<?php require_once('check_login.php');?>
<?php include('head.php');?>
<?php include('header.php');?>
<?php include('sidebar.php');?>

<?php
include('connect.php');
if(isset($_POST["btn_web"])) {
    // Your existing PHP processing code remains the same
    extract($_POST);
    $target_dir = "uploadImage/Logo/";
    $website_logo = basename($_FILES["website_image"]["name"]);
    
    if($_FILES["website_image"]["tmp_name"]!=''){
        $image = $target_dir . basename($_FILES["website_image"]["name"]);
        if (move_uploaded_file($_FILES["website_image"]["tmp_name"], $image)) {
            @unlink("uploadImage/Logo/".$_POST['old_website_image']);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        $website_logo =$_POST['old_website_image'];
    }

    $login_logo = basename($_FILES["login_image"]["name"]);
    if($_FILES["login_image"]["tmp_name"]!=''){
        $image = $target_dir . basename($_FILES["login_image"]["name"]);
        if (move_uploaded_file($_FILES["login_image"]["tmp_name"], $image)) {
            @unlink("uploadImage/Logo/".$_POST['old_login_image']);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        $login_logo =$_POST['old_login_image'];
    }

    $invoice_logo = basename($_FILES["invoice_image"]["name"]);
    if($_FILES["invoice_image"]["tmp_name"]!=''){
        $image = $target_dir . basename($_FILES["invoice_image"]["name"]);
        if (move_uploaded_file($_FILES["invoice_image"]["tmp_name"], $image)) {
            @unlink("uploadImage/Logo/".$_POST['old_invoice_image']);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        $invoice_logo =$_POST['old_invoice_image'];
    }

    $background_login_image = basename($_FILES["back_login_image"]["name"]);
    if($_FILES["back_login_image"]["tmp_name"]!=''){
        $image = $target_dir . basename($_FILES["back_login_image"]["name"]);
        if (move_uploaded_file($_FILES["back_login_image"]["tmp_name"], $image)) {
            @unlink("uploadImage/Logo/".$_POST['old_back_login_image']);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        $background_login_image =$_POST['old_back_login_image'];
    }
    
    $q1="UPDATE `manage_website` SET `title`='$title',`short_title`='$short_title',`logo`='$website_logo',`footer`='$footer' ,`currency_code`= '$currency_code',`currency_symbol`= '$currency_symbol',`login_logo`='$login_logo',`invoice_logo`='$invoice_logo' , `background_login_image` = '$background_login_image'";
    
    if ($conn->query($q1) === TRUE) {
        $_SESSION['success']='Record Successfully Updated';
        ?>
        <script type="text/javascript">
            window.location = "manage_website.php";
        </script>
        <?php 
    } else {
        $_SESSION['error']='Something Went Wrong';
    }
}

$que="select * from manage_website";
$query=$conn->query($que);
while($row=mysqli_fetch_array($query)) {
    extract($row);
    $title = $row['title'];
    $short_title = $row['short_title'];
    $footer = $row['footer'];
    $currency_code = $row['currency_code'];
    $currency_symbol = $row['currency_symbol'];
    $website_logo = $row['logo'];
    $login_logo = $row['login_logo'];
    $invoice_logo = $row['invoice_logo'];
    $background_login_image = $row['background_login_image'];
}
?> 

<!-- Page wrapper  -->
<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Website Management</h3> 
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Website Management</li>
            </ol>
        </div>
    </div>
    <!-- End Bread crumb -->
    
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Website Settings</h4>
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" id="websiteForm">
                            
                            <!-- Website Logo Section -->
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Website Logo</label>
                                <div class="col-lg-9">
                                    <div class="image-upload-container">
                                        <div class="image-preview mb-3" id="websitePreview">
                                            <img src="uploadImage/Logo/<?=$website_logo?>" alt="Website Logo" class="img-thumbnail">
                                        </div>
                                        <input type="hidden" value="<?=$website_logo?>" name="old_website_image">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="website_image" id="websiteImage" accept="image/*">
                                            <label class="custom-file-label" for="websiteImage">Choose new website logo</label>
                                        </div>
                                        <small class="form-text text-muted">Recommended size: 250x80 pixels</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Login Logo Section -->
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Login Page Logo</label>
                                <div class="col-lg-9">
                                    <div class="image-upload-container">
                                        <div class="image-preview mb-3" id="loginPreview">
                                            <img src="uploadImage/Logo/<?=$login_logo?>" alt="Login Logo" class="img-thumbnail">
                                        </div>
                                        <input type="hidden" value="<?=$login_logo?>" name="old_login_image">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="login_image" id="loginImage" accept="image/*">
                                            <label class="custom-file-label" for="loginImage">Choose new login logo</label>
                                        </div>
                                        <small class="form-text text-muted">Recommended size: 200x60 pixels</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Background Login Image Section -->
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Login Background Image</label>
                                <div class="col-lg-9">
                                    <div class="image-upload-container">
                                        <div class="image-preview mb-3" id="backgroundPreview">
                                            <img src="uploadImage/Logo/<?=$background_login_image?>" alt="Background Image" class="img-thumbnail">
                                        </div>
                                        <input type="hidden" value="<?=$background_login_image?>" name="old_back_login_image">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="back_login_image" id="backgroundImage" accept="image/*">
                                            <label class="custom-file-label" for="backgroundImage">Choose new background image</label>
                                        </div>
                                        <small class="form-text text-muted">Recommended size: 1920x1080 pixels</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-lg-9 ml-auto">
                                    <button type="submit" name="btn_web" class="btn btn-primary">Update Settings</button>
                                    <button type="reset" class="btn btn-light">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
</div>

<style>
.image-upload-container {
    padding: 15px;
    border: 1px solid #e4e4e4;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.image-preview {
    text-align: center;
    padding: 10px;
}

.image-preview img {
    max-height: 150px;
    max-width: 100%;
}

.custom-file-label::after {
    content: "Browse";
}

.card {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border: none;
    border-radius: 5px;
}

.card-title {
    color: #3d405c;
    font-weight: 600;
    padding: 20px 20px 0;
}

.form-group {
    margin-bottom: 25px;
}
</style>

<script>
// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    // Website logo preview
    document.getElementById('websiteImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#websitePreview img').src = e.target.result;
            }
            reader.readAsDataURL(file);
            document.querySelector('label[for="websiteImage"]').textContent = file.name;
        }
    });
    
    // Login logo preview
    document.getElementById('loginImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#loginPreview img').src = e.target.result;
            }
            reader.readAsDataURL(file);
            document.querySelector('label[for="loginImage"]').textContent = file.name;
        }
    });
    
    // Background image preview
    document.getElementById('backgroundImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#backgroundPreview img').src = e.target.result;
            }
            reader.readAsDataURL(file);
            document.querySelector('label[for="backgroundImage"]').textContent = file.name;
        }
    });
    
    // Update custom file input labels to show selected file names
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            let fileName = this.files[0] ? this.files[0].name : 'Choose file';
            this.nextElementSibling.textContent = fileName;
        });
    });
});
</script>

<?php include('footer.php');?>

<!-- Your existing popup code remains the same -->
<link rel="stylesheet" href="popup_style.css">
<?php if(!empty($_SESSION['success'])) {  ?>
<div class="popup popup--icon -success js_success-popup popup--visible">
  <div class="popup__background"></div>
  <div class="popup__content">
    <h3 class="popup__content__title">
      Success 
    </h1>
    <p><?php echo $_SESSION['success']; ?></p>
    <p>
      <button class="button button--success"  data-for="js_success-popup">Close</button>
    </p>
  </div>
</div>
<?php unset($_SESSION["success"]);  
} ?>
<?php if(!empty($_SESSION['error'])) {  ?>
<div class="popup popup--icon -error js_error-popup popup--visible">
  <div class="popup__background"></div>
  <div class="popup__content">
    <h3 class="popup__content__title">
      Error 
    </h1>
    <p><?php echo $_SESSION['error']; ?></p>
    <p>
      <button class="button button--error" data-for="js_error-popup">Close</button>
    </p>
  </div>
</div>
<?php unset($_SESSION["error"]);  } ?>
<script>
  var addButtonTrigger = function addButtonTrigger(el) {
    el.addEventListener('click', function () {
      var popupEl = document.querySelector('.' + el.dataset.for);
      popupEl.classList.toggle('popup--visible');
    });
  };

  Array.from(document.querySelectorAll('button[data-for]')).
  forEach(addButtonTrigger);
</script>