<?php
require_once('check_login.php');
include('connect.php');
date_default_timezone_set('Asia/Kolkata');

// Handle form submission
if (isset($_POST["submit"])) {
    $floorno = $_POST['floorno'];
    $roomname = $_POST['roomname'];
    $per_adult_price = $_POST['per_adult_price'];
    $per_kid_price = $_POST['per_kid_price'];
    $amenities = $_POST['amenities'] ?: null; // Handle nullable amenities

    // Prepared statement for insert
    $stmt = $conn->prepare("INSERT INTO `tbl_rooms` (`floorno`, `roomname`, `per_adult_price`, `per_kid_price`, `amenities`) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdds", $floorno, $roomname, $per_adult_price, $per_kid_price, $amenities);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Room Successfully Added';
        echo '<script>window.location="view_room.php";</script>';
        exit();
    } else {
        $_SESSION['error'] = 'Something Went Wrong: ' . $stmt->error;
        echo '<script>window.location="add_room.php";</script>';
        exit();
    }
    $stmt->close();
}
?>

<?php include('head.php'); ?>
<?php include('header.php'); ?>
<link rel="stylesheet" href="popup_style.css">
<?php include('sidebar.php'); ?>

<style>
    /* General styling */
    .page-wrapper {
        background: #f4f7fa;
        min-height: 100vh;
        padding: 20px;
    }

    .card {
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        border: none;
        margin: 30px auto;
        max-width: 700px;
        background: #fff;
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        text-align: center;
        padding: 15px;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .card-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 15px;
    }

    .form-group label {
        flex: 0 0 30%;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0;
    }

    .form-group .col-input {
        flex: 1;
        min-width: 200px;
        position: relative;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: #2575fc;
        box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
        outline: none;
    }

    .form-control.is-invalid {
        border-color: #e53e3e;
        background: #fff5f5;
    }

    .invalid-feedback {
        color: #e53e3e;
        font-size: 14px;
        margin-top: 5px;
        display: none;
    }

    .form-control.is-invalid ~ .invalid-feedback {
        display: block;
    }

    /* Enhanced Room Name Select Styling */
    .form-control#roomname {
        appearance: none;
        background: #f8fafc url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%232d3748" width="18px" height="18px"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 15px center;
        background-size: 18px;
        padding: 12px 40px 12px 15px;
        font-size: 16px;
        font-weight: 500;
        color: #2d3748;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.3s ease;
        height: 48px;
        line-height: normal;
    }

    .form-control#roomname:focus {
        border-color: #2575fc;
        box-shadow: 0 0 10px rgba(37, 117, 252, 0.4);
        background-color: #fff;
    }

    .form-control#roomname option {
        font-size: 16px;
        padding: 10px;
        background: #fff;
        color: #2d3748;
    }

    .form-control#roomname option:disabled {
        color: #6b7280;
    }

    .form-group.room-name-group {
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    /* Textarea for amenities */
    .form-control#amenities {
        resize: vertical;
        min-height: 100px;
    }

    /* Helper text for amenities */
    .helper-text {
        font-size: 12px;
        color: #6b7280;
        margin-top: 5px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        width: 100%;
        max-width: 200px;
        margin: 20px auto;
        display: block;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(37, 117, 252, 0.4);
    }

    .btn-primary:active {
        transform: translateY(0);
        box-shadow: 0 4px 10px rgba(37, 117, 252, 0.2);
    }

    .btn-cancel {
        background: #e53e3e;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        width: 100%;
        max-width: 200px;
        margin: 20px 10px;
        display: inline-block;
        color: #fff;
    }

    .btn-cancel:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(229, 62, 62, 0.4);
    }

    .btn-cancel:active {
        transform: translateY(0);
        box-shadow: 0 4px 10px rgba(229, 62, 62, 0.2);
    }

    .text-danger {
        color: #e53e3e;
        font-weight: 600;
        margin-left: 5px;
    }

    .breadcrumb {
        background: transparent;
        padding: 10px 0;
        margin-bottom: 20px;
    }

    .breadcrumb-item a {
        color: #2575fc;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .form-group {
            flex-direction: column;
            align-items: flex-start;
        }
        .form-group label {
            flex: none;
            margin-bottom: 8px;
        }
        .form-group .col-input {
            width: 100%;
        }
        .form-control#roomname {
            padding-right: 35px;
        }
    }
</style>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Add Room</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Add Room</li>
            </ol>
        </div>
    </div>
    <!-- End Bread crumb -->

    <!-- Container fluid -->
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">Room Details</div>
                    <div class="card-body">
                        <form class="form-valide" method="post" enctype="multipart/form-data" id="addRoomForm">
                            <!-- Room No -->
                            <div class="form-group">
                                <label for="floorno">Room No <span class="text-danger">*</span></label>
                                <div class="col-input">
                                    <input type="number" class="form-control" id="floorno" name="floorno" placeholder="Enter room number" min="1" max="999" required>
                                    <div class="invalid-feedback">Please enter a valid room number (1-999).</div>
                                </div>
                            </div>

                            <!-- Room Name -->
                            <div class="form-group room-name-group">
                                <label for="roomname">Room Name <span class="text-danger">*</span></label>
                                <div class="col-input">
                                    <select class="form-control" id="roomname" name="roomname" required>
                                        <option value="" disabled selected>Please select room type</option>
                                        <option value="Deluxe">Deluxe</option>
                                        <option value="Superior">Superior</option>
                                        <option value="Single">Single</option>
                                        <option value="Double">Double</option>
                                        <option value="Triple">Triple</option>
                                        <option value="Quad">Quad</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a room type.</div>
                                </div>
                            </div>

                            <!-- Per Adult Price -->
                            <div class="form-group">
                                <label for="per_adult_price">Per Adult Price <span class="text-danger">*</span></label>
                                <div class="col-input">
                                    <input type="number" step="0.01" class="form-control" id="per_adult_price" name="per_adult_price" placeholder="Enter price per adult" min="0" max="999999.99" required>
                                    <div class="invalid-feedback">Please enter a valid price (0 or higher).</div>
                                </div>
                            </div>

                            <!-- Per Kid Price -->
                            <div class="form-group">
                                <label for="per_kid_price">Per Kid Price <span class="text-danger">*</span></label>
                                <div class="col-input">
                                    <input type="number" step="0.01" class="form-control" id="per_kid_price" name="per_kid_price" placeholder="Enter price per kid" min="0" max="999999.99" required>
                                    <div class="invalid-feedback">Please enter a valid price (0 or higher).</div>
                                </div>
                            </div>

                            <!-- Amenities -->
                            <div class="form-group">
                                <label for="amenities">Amenities <span class="text-danger">*</span></label>
                                <div class="col-input">
                                    <textarea class="form-control" id="amenities" name="amenities" placeholder="e.g., Wi-Fi, TV, AC" required></textarea>
                                    <div class="helper-text">Enter amenities separated by commas</div>
                                    <div class="invalid-feedback">Please enter at least one amenity.</div>
                                </div>
                            </div>

                            <!-- Submit and Cancel Buttons -->
                            <div class="form-group mt-4">
                                <div class="col-input text-center">
                                    <button type="submit" name="submit" class="btn btn-primary">Add Room</button>
                                    <a href="view_room.php" class="btn btn-cancel">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div> <!-- col -->
        </div> <!-- row -->
    </div> <!-- container-fluid -->
</div> <!-- page-wrapper -->

<script>
document.getElementById('addRoomForm').addEventListener('submit', function(event) {
    let isValid = true;
    const form = this;

    // Reset validation states
    form.querySelectorAll('.form-control').forEach(input => {
        input.classList.remove('is-invalid');
    });

    // Validate Room No
    const floorno = form.querySelector('#floorno');
    if (!floorno.value || floorno.value < 1 || floorno.value > 999) {
        floorno.classList.add('is-invalid');
        isValid = false;
    }

    // Validate Room Name
    const roomname = form.querySelector('#roomname');
    if (!roomname.value) {
        roomname.classList.add('is-invalid');
        isValid = false;
    }

    // Validate Per Adult Price
    const per_adult_price = form.querySelector('#per_adult_price');
    if (!per_adult_price.value || per_adult_price.value < 0 || per_adult_price.value > 999999.99) {
        per_adult_price.classList.add('is-invalid');
        isValid = false;
    }

    // Validate Per Kid Price
    const per_kid_price = form.querySelector('#per_kid_price');
    if (!per_kid_price.value || per_kid_price.value < 0 || per_kid_price.value > 999999.99) {
        per_kid_price.classList.add('is-invalid');
        isValid = false;
    }

    // Validate Amenities
    const amenities = form.querySelector('#amenities');
    if (!amenities.value.trim()) {
        amenities.classList.add('is-invalid');
        isValid = false;
    } else if (amenities.value.length > 65535) { // MySQL text column max length
        amenities.classList.add('is-invalid');
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }
});
</script>

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