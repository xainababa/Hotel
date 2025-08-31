<?php
require_once('check_login.php');
include('connect.php');
date_default_timezone_set('Asia/Kolkata');
?>

<?php include('head.php'); ?>
<?php include('header.php'); ?>
<link rel="stylesheet" href="popup_style.css">
<?php include('sidebar.php'); ?>

<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $booking_id = intval($_POST['booking_id']);
    $amount = floatval($_POST['amount']);
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $payment_status = $_POST['payment_status'];
    $transaction_id = !empty($_POST['transaction_id']) ? $_POST['transaction_id'] : null;
    
    // Insert payment
    $stmt = $conn->prepare("INSERT INTO tbl_payment (bookingid, amount, datee, payment_method, payment_status, transaction_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $booking_id, $amount, $payment_date, $payment_method, $payment_status, $transaction_id);
    
    if ($stmt->execute()) {
        // Update booking paid amount
        $update_stmt = $conn->prepare("UPDATE tbl_booking SET paid_amount = paid_amount + ? WHERE booking_id = ?");
        $update_stmt->bind_param("di", $amount, $booking_id);
        $update_stmt->execute();
        
        // Update booking status if payment is completed
        if ($payment_status == 'completed') {
            $status_stmt = $conn->prepare("UPDATE tbl_booking SET status = 'confirmed' WHERE booking_id = ?");
            $status_stmt->bind_param("i", $booking_id);
            $status_stmt->execute();
            $status_stmt->close();
        }
        
        $_SESSION['success'] = 'Payment successfully recorded!';
        header("Location: payment.php?booking_id=" . $booking_id);
        exit();
    } else {
        $_SESSION['error'] = 'Error recording payment: ' . $stmt->error;
    }
    $stmt->close();
}

// Get booking details if booking_id is provided
$booking_details = null;
$payments = [];
$total_paid = 0;
$balance = 0;

if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    
    // Get booking details
    $sql = "SELECT b.*, c.name as customer_name, r.roomname, r.per_adult_price, r.per_kid_price 
            FROM tbl_booking b 
            JOIN tbl_customer c ON b.user_id = c.id 
            JOIN tbl_rooms r ON b.room_id = r.id 
            WHERE b.booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking_details = $result->fetch_assoc();
    $stmt->close();
    
    if ($booking_details) {
        // Get payments for this booking
        $payment_sql = "SELECT * FROM tbl_payment WHERE bookingid = ? ORDER BY datee DESC";
        $payment_stmt = $conn->prepare($payment_sql);
        $payment_stmt->bind_param("i", $booking_id);
        $payment_stmt->execute();
        $payment_result = $payment_stmt->get_result();
        
        while ($payment = $payment_result->fetch_assoc()) {
            $payments[] = $payment;
            $total_paid += $payment['amount'];
        }
        $payment_stmt->close();
        
        $balance = $booking_details['total_amount'] - $total_paid;
    }
}
?>

<style>
    /* Payment page specific styles */
    .booking-summary {
        background: #f8fafc;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #2575fc;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 5px 0;
    }

    .summary-label {
        font-weight: 600;
        color: #2d3748;
    }

    .summary-value {
        color: #4a5568;
    }

    .payment-form {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 5px;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #2575fc;
        outline: none;
        box-shadow: 0 0 0 3px rgba(37, 117, 252, 0.1);
    }

    .amount-positive {
        color: #38a169;
        font-weight: 600;
    }

    .amount-negative {
        color: #e53e3e;
        font-weight: 600;
    }
</style>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Payment Management</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item"><a href="view_booking.php">Bookings</a></li>
                <li class="breadcrumb-item active">Payment</li>
            </ol>
        </div>
    </div>
    <!-- End Bread crumb -->

    <!-- Container fluid -->
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if ($booking_details): ?>
                <!-- Booking Summary -->
                <div class="card">
                    <div class="card-header">Booking Summary</div>
                    <div class="card-body">
                        <div class="booking-summary">
                            <div class="summary-item">
                                <span class="summary-label">Booking ID:</span>
                                <span class="summary-value">#<?php echo $booking_details['booking_id']; ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Customer:</span>
                                <span class="summary-value"><?php echo htmlspecialchars($booking_details['customer_name']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Room:</span>
                                <span class="summary-value"><?php echo htmlspecialchars($booking_details['roomname']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Check-in:</span>
                                <span class="summary-value"><?php echo htmlspecialchars($booking_details['check_in']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Check-out:</span>
                                <span class="summary-value"><?php echo htmlspecialchars($booking_details['check_out']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Amount:</span>
                                <span class="summary-value amount-positive">₨ <?php echo number_format($booking_details['total_amount'], 2); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Amount Paid:</span>
                                <span class="summary-value amount-positive">₨ <?php echo number_format($total_paid, 2); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Balance:</span>
                                <span class="summary-value <?php echo $balance > 0 ? 'amount-negative' : 'amount-positive'; ?>">
                                    ₨ <?php echo number_format($balance, 2); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="card">
                    <div class="card-header">Add Payment</div>
                    <div class="card-body">
                        <form method="post" class="payment-form">
                            <input type="hidden" name="booking_id" value="<?php echo $booking_details['booking_id']; ?>">
                            
                            <div class="form-group">
                                <label class="form-label">Amount (₨)</label>
                                <input type="number" class="form-control" name="amount" min="0" max="<?php echo $balance; ?>" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Payment Date</label>
                                <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Payment Method</label>
                                <select class="form-control" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="esewa">eSewa</option>
                                    <option value="khalti">Khalti</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Transaction ID (if applicable)</label>
                                <input type="text" class="form-control" name="transaction_id" placeholder="Enter transaction ID">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Payment Status</label>
                                <select class="form-control" name="payment_status" required>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">Record Payment</button>
                        </form>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card">
                    <div class="card-header">Payment History</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount (₨)</th>
                                        <th>Method</th>
                                        <th>Transaction ID</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($payments) > 0): ?>
                                        <?php foreach ($payments as $payment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($payment['datee']); ?></td>
                                                <td class="amount-positive">₨ <?php echo number_format($payment['amount'], 2); ?></td>
                                                <td><?php echo ucfirst($payment['payment_method']); ?></td>
                                                <td><?php echo $payment['transaction_id'] ? htmlspecialchars($payment['transaction_id']) : 'N/A'; ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo $payment['payment_status']; ?>">
                                                        <?php echo ucfirst($payment['payment_status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No payments recorded yet</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Booking Selection -->
                <div class="card">
                    <div class="card-header">Select Booking</div>
                    <div class="card-body">
                        <form method="get" class="payment-form">
                            <div class="form-group">
                                <label class="form-label">Select Booking</label>
                                <select class="form-control" name="booking_id" required onchange="this.form.submit()">
                                    <option value="">-- Select Booking --</option>
                                    <?php
                                    $sql = "SELECT b.booking_id, c.name, b.check_in, b.check_out 
                                            FROM tbl_booking b 
                                            JOIN tbl_customer c ON b.user_id = c.id 
                                            ORDER BY b.booking_id DESC";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $selected = isset($_GET['booking_id']) && $_GET['booking_id'] == $row['booking_id'] ? 'selected' : '';
                                        echo "<option value='{$row['booking_id']}' $selected>#{$row['booking_id']} - {$row['name']} ({$row['check_in']} to {$row['check_out']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- End Container fluid -->
</div>
<!-- End Page wrapper -->

<?php if (!empty($_SESSION['success'])) { ?>
<div class="popup popup--icon -success js_success-popup popup--visible">
    <div class="popup__background"></div>
    <div class="popup__content">
        <h3 class="popup__content__title">Success</h3>
        <p><?php echo $_SESSION['success']; ?></p>
        <p>
            <button class="button button--success" data-for="js_success-popup">Close</button>
        </p>
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
        <p>
            <button class="button button--error" data-for="js_error-popup">Close</button>
        </p>
    </div>
</div>
<?php unset($_SESSION["error"]); ?>
<?php } ?>

<script>
var addButtonTrigger = function addButtonTrigger(el) {
    el.addEventListener('click', function () {
        var popupEl = document.querySelector('.' + el.dataset.for);
        popupEl.classList.toggle('popup--visible');
    });
};

Array.from(document.querySelectorAll('button[data-for]')).forEach(addButtonTrigger);
</script>

<?php include('footer.php'); ?>
<?php $conn->close(); ?>