<?php
// --- Database Configuration ---
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "db_hotel";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$message = "";
$booking_data = [];

// --- Handle Update ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id     = validate_input($_POST['booking_id']);
    $user_id        = validate_input($_POST['user_id']);
    $room_id        = validate_input($_POST['room_id']);
    $adults         = validate_input($_POST['adults']);
    $children       = validate_input($_POST['children']);
    $check_in       = validate_input($_POST['check_in']);
    $check_out      = validate_input($_POST['check_out']);
    $total_amount   = validate_input($_POST['total_amount']);
    $special_request= validate_input($_POST['special_request']);
    $payment_method = validate_input($_POST['payment_method']);
    $status         = validate_input($_POST['status']);

    if (empty($booking_id) || empty($user_id) || empty($room_id) || empty($check_in) || empty($check_out) || empty($total_amount)) {
        $message = "<div class='alert alert-danger'>❌ Required fields are missing.</div>";
    } else {
        $sql = "UPDATE tbl_booking SET 
                    user_id=?, room_id=?, adults=?, children=?, 
                    check_in=?, check_out=?, total_amount=?, special_request=?, 
                    payment_method=?, status=? 
                WHERE booking_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiisssssssi", 
            $user_id, $room_id, $adults, $children, $check_in, $check_out, 
            $total_amount, $special_request, $payment_method, $status, $booking_id
        );
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Booking updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// --- Fetch Booking Data ---
if (isset($_GET['id'])) {
    $booking_id = validate_input($_GET['id']);
    $sql = "SELECT b.*, c.name AS customer_name, r.roomname AS room_name, r.per_adult_price, r.per_kid_price
            FROM tbl_booking b
            INNER JOIN tbl_customer c ON b.user_id = c.id
            INNER JOIN tbl_rooms r ON b.room_id = r.id
            WHERE b.booking_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) { $booking_data = $result->fetch_assoc(); }
    else { $message = "<div class='alert alert-danger'>❌ Booking not found.</div>"; }
    $stmt->close();
} else {
    $message = "<div class='alert alert-danger'>❌ No booking ID provided.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color:#f8f9fa; }
    .container { max-width: 750px; margin-top: 40px; }
    .card { border-radius: 12px; box-shadow: 0px 4px 8px rgba(0,0,0,0.05); }
  </style>
</head>
<body>
<div class="container">
  <div class="card p-4">
    <h3 class="text-center mb-3">✏️ Edit Booking</h3>
    <?php echo $message; ?>

    <?php if (!empty($booking_data)): ?>
    <form action="" method="POST">
      <input type="hidden" name="booking_id" value="<?php echo $booking_data['booking_id']; ?>">
      <input type="hidden" name="user_id" value="<?php echo $booking_data['user_id']; ?>">
      <input type="hidden" name="room_id" value="<?php echo $booking_data['room_id']; ?>">

      <div class="mb-3">
        <label class="form-label">Customer Name</label>
        <input type="text" class="form-control" value="<?php echo $booking_data['customer_name']; ?>" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">Room</label>
        <input type="text" class="form-control" value="<?php echo $booking_data['room_name']; ?>" disabled>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Adults</label>
          <input type="number" name="adults" class="form-control" value="<?php echo $booking_data['adults']; ?>" min="1" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Children</label>
          <input type="number" name="children" class="form-control" value="<?php echo $booking_data['children']; ?>" min="0">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Check-in Date</label>
          <input type="date" name="check_in" class="form-control" value="<?php echo $booking_data['check_in']; ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Check-out Date</label>
          <input type="date" name="check_out" class="form-control" value="<?php echo $booking_data['check_out']; ?>" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Total Amount (Rs.)</label>
        <input type="number" step="0.01" name="total_amount" class="form-control" value="<?php echo $booking_data['total_amount']; ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Special Request</label>
        <textarea name="special_request" class="form-control" rows="3"><?php echo $booking_data['special_request']; ?></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Payment Method</label>
          <select name="payment_method" class="form-select" required>
            <option value="cash"   <?php if($booking_data['payment_method']=="cash") echo "selected"; ?>>Cash</option>
            <option value="esewa"  <?php if($booking_data['payment_method']=="esewa") echo "selected"; ?>>eSewa</option>
            <option value="khalti" <?php if($booking_data['payment_method']=="khalti") echo "selected"; ?>>Khalti</option>
            <option value="card"   <?php if($booking_data['payment_method']=="card") echo "selected"; ?>>Card</option>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select" required>
            <option value="pending"   <?php if($booking_data['status']=="pending") echo "selected"; ?>>Pending</option>
            <option value="confirmed" <?php if($booking_data['status']=="confirmed") echo "selected"; ?>>Confirmed</option>
            <option value="cancelled" <?php if($booking_data['status']=="cancelled") echo "selected"; ?>>Cancelled</option>
            <option value="completed" <?php if($booking_data['status']=="completed") echo "selected"; ?>>Completed</option>
          </select>
        </div>
      </div>

      <button type="submit" class="btn btn-success w-100">💾 Save Changes</button>
    </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
