<?php
session_start();

// DEVELOPMENT MODE: Enable error reporting (disable on live server)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
include('connect.php');

// Salt function
function createSalt() {
    return '2123293dsj2hu2nikhiljdsd';
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_login'])) {
    $email = trim($_POST['email']);
    $raw_password = trim($_POST['password']);

    // Hash password with salt
    $hashed_pass = hash('sha256', $raw_password);
    $salt = createSalt();
    $final_password = hash('sha256', $salt . $hashed_pass);

    // Prepare and execute query securely
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $email, $final_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Set session variables
        $_SESSION["id"] = $row['id'];
        $_SESSION["username"] = $row['username'];
        $_SESSION["email"] = $row['email'];
        $_SESSION["fname"] = $row['fname'];
        $_SESSION["lname"] = $row['lname'];
        $_SESSION["image"] = $row['image'];

        session_regenerate_id(true); // Prevent session fixation

        // Success popup and redirect after 1.5 seconds
        echo '
        <link rel="stylesheet" href="popup_style.css">
        <div class="popup popup--icon -success js_success-popup popup--visible">
          <div class="popup__background"></div>
          <div class="popup__content">
            <h3 class="popup__content__title">Success</h3>
            <p>Login Successfully</p>
            <p><script>setTimeout(() => { window.location.href = "index.php"; }, 1500);</script></p>
          </div>
        </div>';
    } else {
        // Invalid login error popup
        echo '
        <link rel="stylesheet" href="popup_style.css">
        <div class="popup popup--icon -error js_error-popup popup--visible">
          <div class="popup__background"></div>
          <div class="popup__content">
            <h3 class="popup__content__title">Error</h3>
            <p>Invalid Email or Password</p>
            <p><a href="login.php"><button class="button button--error">Try Again</button></a></p>
          </div>
        </div>';
    }

    $stmt->close();
}
?>

<?php include('head.php'); ?>
<link rel="stylesheet" href="popup_style.css">

<!-- Main wrapper -->
<div id="main-wrapper">
    <div class="unix-login">
        <?php
        $sql_login = "SELECT * FROM manage_website LIMIT 1";
        $result_login = $conn->query($sql_login);
        $row_login = $result_login ? $result_login->fetch_assoc() : null;
        ?>
        <div class="container-fluid"
             style="background-image: url('uploadImage/Logo/<?php echo htmlspecialchars($row_login['background_login_image'] ?? ''); ?>'); background-color: #cccccc;">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="login-content card">
                        <div class="login-form">
                            <center>
                                <img src="uploadImage/Logo/<?php echo htmlspecialchars($row_login['login_logo'] ?? ''); ?>" style="width:50%;">
                            </center><br>
                            <form method="POST" autocomplete="off" novalidate>
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input id="email" type="email" name="email" class="form-control" placeholder="Email" required autofocus>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input id="password" type="password" name="password" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="checkbox d-flex justify-content-between">
                                    <label><input type="checkbox" name="remember_me" value="1"> Remember Me</label>
                                    <label><a href="forgot_password.php">Forgotten Password?</a></label>
                                </div>
                                <button type="submit" name="btn_login" class="btn btn-primary btn-flat m-b-30 m-t-30 w-100">Sign in</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="js/lib/jquery/jquery.min.js"></script>
<script src="js/lib/bootstrap/js/popper.min.js"></script>
<script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
<script src="js/jquery.slimscroll.js"></script>
<script src="js/sidebarmenu.js"></script>
<script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
<script src="js/custom.min.js"></script>
