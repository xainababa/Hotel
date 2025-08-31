<?php 
require_once('check_login.php');
include('head.php');
include('header.php');
include('sidebar.php');

include('connect.php');
date_default_timezone_set('Asia/Kolkata');
$current_date = date('Y-m-d');

if (isset($_POST["btn_update"])) {
    // Sanitize and extract POST data
    $bookingid = $_POST['bookingid'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $datee = $_POST['datee'] ?? $current_date;

    // Calculate paid amount so far for this booking
    $sql4 = "SELECT SUM(amount) as amt FROM tbl_payment WHERE bookingid = ?";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param("i", $_GET["id"]);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $row4 = $result4->fetch_assoc();
    $paid_amount = $row4['amt'] ?? 0;
    $stmt4->close();

    // Insert new payment
    $sql = "INSERT INTO tbl_payment (bookingid, amount, datee) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $bookingid, $amount, $datee);
    $success_insert = $stmt->execute();
    $stmt->close();

    if ($success_insert) {
        // Update paid amount in booking table
        $new_paid_amount = $paid_amount + $amount;
        $q1 = "UPDATE tbl_booking SET paid = ? WHERE id = ?";
        $stmt2 = $conn->prepare($q1);
        $stmt2->bind_param("di", $new_paid_amount, $_GET['id']);
        $success_update = $stmt2->execute();
        $stmt2->close();

        if ($success_update) {
            $_SESSION['success'] = 'Record Successfully Added and Updated';
        } else {
            $_SESSION['error'] = 'Payment added but updating booking failed';
        }
    } else {
        $_SESSION['error'] = 'Something Went Wrong While Adding Payment';
    }

    echo '<script>window.location="view_booking.php";</script>';
    exit;
}

// Fetch booking, customer, payment details
$bookingid = intval($_GET["id"] ?? 0);

$que = "SELECT * FROM tbl_booking WHERE id = ?";
$stmt3 = $conn->prepare($que);
$stmt3->bind_param("i", $bookingid);
$stmt3->execute();
$query = $stmt3->get_result();

$row = $query->fetch_assoc();
$stmt3->close();

if ($row) {
    // Fetch customer name
    $sql2 = "SELECT * FROM tbl_customer WHERE id = ?";
    $stmt4 = $conn->prepare($sql2);
    $stmt4->bind_param("i", $row['name']);
    $stmt4->execute();
    $result2 = $stmt4->get_result();
    $row2 = $result2->fetch_assoc();
    $stmt4->close();

    // Fetch latest payment details for this booking
    $sql3 = "SELECT * FROM tbl_payment WHERE bookingid = ? ORDER BY id DESC LIMIT 1";
    $stmt5 = $conn->prepare($sql3);
    $stmt5->bind_param("i", $bookingid);
    $stmt5->execute();
    $result3 = $stmt5->get_result();
    $row3 = $result3->fetch_assoc();
    $stmt5->close();

    // Sum of paid amount so far
    $sql4 = "SELECT SUM(amount) as amt FROM tbl_payment WHERE bookingid = ?";
    $stmt6 = $conn->prepare($sql4);
    $stmt6->bind_param("i", $bookingid);
    $stmt6->execute();
    $result4 = $stmt6->get_result();
    $row4 = $result4->fetch_assoc();
    $stmt6->close();

    $name = $row2['name'] ?? '';
    $amount = $row3['amount'] ?? 0;
    $datee = $row3['datee'] ?? '';
    $totalamount = $row['totalamount'] ?? 0;
    $taxamount = $row['taxamount'] ?? 0;
    $paid_amount = $row4['amt'] ?? 0;
    $remainamount = $taxamount - $paid_amount;
} else {
    echo '<div class="alert alert-danger">Booking not found!</div>';
    exit;
}

?>

<!-- Page wrapper -->
<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Payment Management</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Add Payment</li>
            </ol>
        </div>
    </div>
    <!-- End Bread crumb -->

    <!-- Container fluid -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4>Payment Details</h4>
                        <div class="form-validation">
                            <form class="form-valide" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="current_date" class="form-control" value="<?php echo htmlspecialchars($current_date); ?>">

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="val-name">Customer Name:<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="val-bookingid">Booking ID:<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="bookingid" value="<?php echo htmlspecialchars($bookingid); ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="val-totalamount">Total Amount:<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="totalamount" value="रू <?php echo number_format($totalamount, 2); ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="val-taxamount">Payable Amount:<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="taxamount" value="रू <?php echo number_format($taxamount, 2); ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="val-remainamount">Remaining Amount:<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="remainamount" value="रू <?php echo number_format($remainamount, 2); ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="val-insertamount">Insert Amount:<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" class="form-control" name="amount" max="<?php echo htmlspecialchars($remainamount); ?>" min="0" step="0.01" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="val-paidamount">Paid Amount:<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="paid_amount" value="रू <?php echo number_format($paid_amount, 2); ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label" for="val-date">Date:<span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="date" class="form-control" name="datee" value="<?php echo htmlspecialchars($current_date); ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-8 ml-auto">
                                        <button type="submit" name="btn_update" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Payment History</h4>
                        <div class="table-responsive m-t-40">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Booking No</th>
                                        <th>Amount (रू)</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Booking No</th>
                                        <th>Amount (रू)</th>
                                        <th>Date</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php 
                                    $sql = "SELECT * FROM tbl_payment WHERE bookingid = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $bookingid);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['bookingid']) . "</td>";
                                        echo "<td>रू " . number_format($row['amount'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['datee']) . "</td>";
                                        echo "</tr>";
                                    }
                                    $stmt->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Payment History -->
    </div>
</div>

<?php include('footer.php');?>
