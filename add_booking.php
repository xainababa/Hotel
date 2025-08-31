<?php
// --- FILE 1: connect.php (Merged) ---
// --- Database Configuration: UPDATE THESE DETAILS ---
$servername = "127.0.0.1";
$username = "root";     // Replace with your actual database username
$password = "";         // Replace with your actual database password
$dbname = "db_hotel";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- End of connect.php content ---

require_once('check_login.php'); // Assuming this file handles session/login checks

// Ensure session is started safely
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Kolkata');

// --- FILE 2: get_room_prices.php (Merged into a new section) ---
// This part handles the AJAX request for real-time price calculation.
// It will only run if a POST request with 'get_prices' is detected.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['get_prices'])) {
    header('Content-Type: application/json');
    $response = ['per_adult_price' => 0, 'per_kid_price' => 0];

    if (isset($_POST['room_id'])) {
        $room_id = intval($_POST['room_id']);
        $stmt = $conn->prepare("SELECT per_adult_price, per_kid_price FROM tbl_rooms WHERE id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $room = $result->fetch_assoc();
            $response['per_adult_price'] = floatval($room['per_adult_price']);
            $response['per_kid_price'] = floatval($room['per_kid_price']);
        }
        $stmt->close();
    }
    echo json_encode($response);
    $conn->close();
    exit(); // Stop script execution after sending JSON response
}

// --- End of get_room_prices.php content ---


// Handle form submission for adding a new booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Collect and sanitize form data
    $user_id = intval($_POST['user_id']);
    $room_id = intval($_POST['room_id']);
    $adults = max(1, intval($_POST['adults']));
    $children = max(0, intval($_POST['children']));
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $payment_method = $_POST['payment_method'];
    $special_request = !empty($_POST['special_request']) ? htmlspecialchars($_POST['special_request']) : null;

    $today = date('Y-m-d');

    // Basic server-side validations
    if ($check_in < $today) {
        $_SESSION['error'] = 'Check-in date cannot be in the past';
        header("Location: add_booking.php");
        exit();
    }
    if ($check_out <= $check_in) {
        $_SESSION['error'] = 'Check-out date must be after check-in';
        header("Location: add_booking.php");
        exit();
    }

    // Check room availability using a prepared statement
    $stmt = $conn->prepare("
        SELECT booking_id 
        FROM tbl_booking 
        WHERE room_id = ? AND status != 'cancelled' 
          AND (? < check_out AND ? > check_in) 
        LIMIT 1
    ");
    $stmt->bind_param("iss", $room_id, $check_in, $check_out);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'Room is already booked for these dates';
        header("Location: add_booking.php");
        exit();
    }
    $stmt->close();

    // Fetch room price for final calculation before insertion
    $stmt = $conn->prepare("SELECT per_adult_price, per_kid_price FROM tbl_rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $_SESSION['error'] = 'Invalid room selected';
        header("Location: add_booking.php");
        exit();
    }
    $room = $result->fetch_assoc();
    $stmt->close();

    // Calculate total amount
    $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    $total_amount = ($room['per_adult_price'] * $adults + $room['per_kid_price'] * $children) * $nights;

    // Insert booking using a prepared statement
    $stmt = $conn->prepare("
        INSERT INTO tbl_booking 
        (user_id, room_id, adults, children, check_in, check_out, total_amount, special_request, payment_method, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->bind_param("iiisssdss", $user_id, $room_id, $adults, $children, $check_in, $check_out, $total_amount, $special_request, $payment_method);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Booking Successfully Added! Booking ID: ' . $conn->insert_id;
        header("Location: view_booking.php");
        exit();
    } else {
        $_SESSION['error'] = 'Database Error: ' . $stmt->error;
        header("Location: add_booking.php");
        exit();
    }
    $stmt->close();
}
?>

<?php include('head.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<link rel="stylesheet" href="popup_style.css">

<style>
    .page-wrapper {
        background: #f4f7fa;
        padding: 20px;
    }
    .card {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin: 20px auto;
        background: #fff;
    }
    .card-title {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        text-align: center;
        padding: 10px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .card-body {
        padding: 20px;
    }
    .form-group {
        margin-bottom: 15px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }
    .col-form-label {
        flex: 0 0 30%;
        font-weight: 600;
        color: #2d3748;
    }
    .col-lg-6 {
        flex: 1;
        min-width: 200px;
    }
    .form-control {
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        font-size: 14px;
        width: 100%;
    }
    .form-control:focus {
        border-color: #2575fc;
        outline: none;
    }
    .form-control.is-invalid {
        border-color: #e53e3e;
        background: #fff5f5;
    }
    .invalid-feedback {
        color: #e53e3e;
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }
    .form-control.is-invalid ~ .invalid-feedback {
        display: block;
    }
    .form-control#user_id,
    .form-control#room_id,
    .form-control#payment_method {
        background: #f8fafc url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%232d3748" width="16px" height="16px"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center;
        padding-right: 30px;
        height: 40px;
    }
    .form-control#special_request {
        min-height: 80px;
        resize: vertical;
    }
    .text-danger {
        color: #e53e3e;
        font-weight: 600;
    }
    .breadcrumb {
        background: transparent;
        padding: 10px 0;
        margin-bottom: 15px;
    }
    .breadcrumb-item a {
        color: #2575fc;
        text-decoration: none;
    }
    .btn-primary {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
    }
    .btn-cancel {
        background: #e53e3e;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        margin-left: 10px;
    }
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #2575fc;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @media (max-width: 768px) {
        .form-group {
            flex-direction: column;
            align-items: flex-start;
        }
        .col-form-label {
            margin-bottom: 5px;
        }
        .col-lg-6 {
            width: 100%;
        }
    }
</style>

<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Add Booking</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Add Booking</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-title">Booking Details</div>
                    <div class="card-body">
                        <div class="form-validation">
                            <form class="form-valide" method="post" id="addBookingForm">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="user_id">Customer <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" id="user_id" name="user_id" required>
                                            <option value="" disabled selected>Select customer</option>
                                            <?php
                                            $sql = "SELECT id, name FROM `tbl_customer` ORDER BY name LIMIT 50";
                                            $result = $conn->query($sql);
                                            if ($result) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>Error: ".$conn->error."</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a customer.</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="room_id">Room <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" id="room_id" name="room_id" required>
                                            <option value="" disabled selected>Select room</option>
                                            <?php
                                            $sql = "SELECT id, roomno, roomname FROM `tbl_rooms` ORDER BY roomno LIMIT 50";
                                            $result = $conn->query($sql);
                                            if ($result) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['roomname'])." - Room ".htmlspecialchars($row['roomno'])."</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>Error: ".$conn->error."</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a room.</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="adults">Adults <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" id="adults" name="adults" value="1" min="1" max="10" required>
                                        <div class="invalid-feedback">Enter 1-10 adults.</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="children">Children <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" id="children" name="children" value="0" min="0" max="10" required>
                                        <div class="invalid-feedback">Enter 0-10 children.</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="check_in">Check-in Date <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="date" class="form-control" id="check_in" name="check_in" min="<?php echo date('Y-m-d'); ?>" required>
                                        <div class="invalid-feedback">Select a valid check-in date.</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="check_out">Check-out Date <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="date" class="form-control" id="check_out" name="check_out" required>
                                        <div class="invalid-feedback">Select a check-out date after check-in.</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Total Amount</label>
                                    <div class="col-lg-6">
                                        <p id="total_amount" class="text-primary">NPR 0.00</p>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select class="form-control" id="payment_method" name="payment_method" required>
                                            <option value="" disabled selected>Select payment method</option>
                                            <option value="cash">Cash</option>
                                            <option value="esewa">eSewa</option>
                                            <option value="khalti">Khalti</option>
                                            <option value="card">Card</option>
                                        </select>
                                        <div class="invalid-feedback">Select a payment method.</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="special_request">Special Request</label>
                                    <div class="col-lg-6">
                                        <textarea class="form-control" id="special_request" name="special_request" placeholder="e.g., Extra pillows, Late check-out"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-8 ml-auto">
                                        <button type="submit" name="submit" class="btn btn-primary">Submit Booking</button>
                                        <a href="view_booking.php" class="btn btn-cancel">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="loading-spinner"></div>
    </div>

    <?php if (!empty($_SESSION['success'])) { ?>
        <div class="popup popup--icon -success js_success-popup popup--visible">
            <div class="popup__background"></div>
            <div class="popup__content">
                <h3 class="popup__content__title">Success</h3>
                <p><?php echo $_SESSION['success']; ?></p>
                <p><button class="button button--success" data-for="js_success-popup">Close</button></p>
            </div>
        </div>
    <?php unset($_SESSION["success"]); ?>
    <?php } ?>
    <?php if (!empty($_SESSION['error'])) { ?>
        <div class="popup popup--icon -error js_error-popup popup--visible">
            <div class="popup__background"></div>
            <div class="popup__content">
                <h3 class="popup__content__title">Error</h3>
                <p><?php echo $_SESSION['error']; ?></p>
                <p><button class="button button--error" data-for="js_error-popup">Close</button></p>
            </div>
        </div>
    <?php unset($_SESSION["error"]); ?>
    <?php } ?>
</div>

<?php include('footer.php'); ?>
<?php $conn->close(); ?>

<script>
    document.getElementById('addBookingForm').addEventListener('submit', function(event) {
        let isValid = true;
        const today = new Date().toISOString().split('T')[0];
        this.querySelectorAll('.form-control').forEach(input => input.classList.remove('is-invalid'));

        const user_id = this.querySelector('#user_id');
        if (!user_id.value) {
            user_id.classList.add('is-invalid');
            isValid = false;
        }
        const room_id = this.querySelector('#room_id');
        if (!room_id.value) {
            room_id.classList.add('is-invalid');
            isValid = false;
        }
        const adults = this.querySelector('#adults');
        if (!adults.value || adults.value < 1 || adults.value > 10) {
            adults.classList.add('is-invalid');
            isValid = false;
        }
        const children = this.querySelector('#children');
        if (children.value < 0 || children.value > 10) {
            children.classList.add('is-invalid');
            isValid = false;
        }
        const check_in = this.querySelector('#check_in');
        if (!check_in.value || check_in.value < today) {
            check_in.classList.add('is-invalid');
            isValid = false;
        }
        const check_out = this.querySelector('#check_out');
        if (!check_out.value || check_out.value <= check_in.value) {
            check_out.classList.add('is-invalid');
            isValid = false;
        }
        const payment_method = this.querySelector('#payment_method');
        if (!payment_method.value) {
            payment_method.classList.add('is-invalid');
            isValid = false;
        }
        if (!isValid) {
            event.preventDefault();
        } else {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
    });

    // Live total amount calculation using AJAX to the same file
    function updateTotalAmount() {
        const room_id = document.getElementById('room_id').value;
        const adults = document.getElementById('adults').value;
        const children = document.getElementById('children').value;
        const check_in = document.getElementById('check_in').value;
        const check_out = document.getElementById('check_out').value;

        if (room_id && adults && children && check_in && check_out) {
            // Send request to the same file, with a new parameter to trigger the price fetch logic
            fetch('add_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `get_prices=1&room_id=${room_id}`
            })
            .then(response => response.json())
            .then(data => {
                const checkInDate = new Date(check_in);
                const checkOutDate = new Date(check_out);
                const nights = (checkOutDate - checkInDate) / (1000 * 60 * 60 * 24);

                if (nights > 0) {
                    const total = (data.per_adult_price * adults + data.per_kid_price * children) * nights;
                    document.getElementById('total_amount').textContent = `NPR ${total.toFixed(2)}`;
                } else {
                    document.getElementById('total_amount').textContent = "NPR 0.00";
                }
            })
            .catch(error => {
                console.error('Error fetching room prices:', error);
                document.getElementById('total_amount').textContent = "NPR 0.00";
            });
        } else {
            document.getElementById('total_amount').textContent = "NPR 0.00";
        }
    }

    // Add event listeners to trigger the calculation when inputs change
    ['room_id', 'adults', 'children', 'check_in', 'check_out'].forEach(id => {
        document.getElementById(id).addEventListener('change', updateTotalAmount);
        document.getElementById(id).addEventListener('input', updateTotalAmount);
    });

    // Initial calculation on page load if values are pre-filled
    window.onload = updateTotalAmount;

    document.querySelectorAll('button[data-for]').forEach(el => {
        el.addEventListener('click', () => {
            document.querySelector('.' + el.dataset.for).classList.toggle('popup--visible');
        });
    });
</script>