<?php 
require_once('check_login.php');
include('head.php');
include('header.php');
include('sidebar.php');
include('connect.php');

// Handle form submission
if (isset($_POST["btn_mail"])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mail_driver = mysqli_real_escape_string($conn, $_POST['mail_driver']);
    $mail_port = mysqli_real_escape_string($conn, $_POST['mail_port']);
    $mail_username = mysqli_real_escape_string($conn, $_POST['mail_username']);
    $mail_password = mysqli_real_escape_string($conn, $_POST['mail_password']);
    $mail_encryption = mysqli_real_escape_string($conn, $_POST['mail_encryption']);

    $updateQuery = "UPDATE `tbl_email_config` 
                    SET 
                        `name` = '$name',
                        `mail_driver_host` = '$mail_driver',
                        `mail_port` = '$mail_port',
                        `mail_username` = '$mail_username',
                        `mail_password` = '$mail_password',
                        `mail_encrypt` = '$mail_encryption'";

    if ($conn->query($updateQuery) === TRUE) {
        header("Location: email_config.php"); // better than JavaScript redirect
        exit();
    } else {
        echo "<script>alert('Failed to update email settings.');</script>";
    }
}

// Fetch existing configuration
$que = "SELECT * FROM tbl_email_config LIMIT 1";
$query = $conn->query($que);

if ($query && $query->num_rows > 0) {
    $row = $query->fetch_assoc();
    $name = $row['name'];
    $mail_driver = $row['mail_driver_host'];
    $mail_port = $row['mail_port'];
    $mail_username = $row['mail_username'];
    $mail_password = $row['mail_password'];
    $mail_encryption = $row['mail_encrypt'];
} else {
    // Default values in case no config exists
    $name = $mail_driver = $mail_port = $mail_username = $mail_password = $mail_encryption = '';
}
?>

<!-- Page wrapper -->
<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Email Management</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Email Management</li>
            </ol>
        </div>
    </div>

    <!-- Container fluid -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8" style="margin-left: 10%;">
                <div class="card">
                    <div class="card-title"></div>
                    <div class="card-body">
                        <div class="input-states">
                            <form class="form-horizontal" method="POST">
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="name" value="<?php echo $name; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Mail Driver Mail Host</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="mail_driver" value="<?php echo $mail_driver; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Mail Port</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="mail_port" value="<?php echo $mail_port; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Mail Username</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="mail_username" value="<?php echo $mail_username; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Mail Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" name="mail_password" value="<?php echo $mail_password; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Mail Encryption</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="mail_encryption" value="<?php echo $mail_encryption; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="btn_mail" class="btn btn-primary btn-flat m-b-30 m-t-30">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('footer.php'); ?>
