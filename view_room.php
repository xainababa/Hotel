<?php
require_once('check_login.php');
include('connect.php');
date_default_timezone_set('Asia/Kolkata');
?>

<?php include('head.php'); ?>
<?php include('header.php'); ?>
<link rel="stylesheet" href="popup_style.css">
<?php include('sidebar.php'); ?>

<?php if (isset($_GET['delete_id'])) { ?>
<div class="popup popup--icon -question js_question-popup popup--visible">
    <div class="popup__background"></div>
    <div class="popup__content">
        <h3 class="popup__content__title">Sure</h3>
        <p>Are You Sure To Delete This Record?</p>
        <p>
            <a href="del_room.php?id=<?php echo $_GET['delete_id']; ?>" class="button button--success" data-for="js_success-popup">Yes</a>
            <a href="view_room.php" class="button button--error" data-for="js_success-popup">No</a>
        </p>
    </div>
</div>
<?php } ?>

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
        max-width: 100%;
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

    .table-responsive {
        margin-top: 20px;
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead th {
        background: #f8fafc;
        color: #2d3748;
        font-weight: 600;
        padding: 15px;
        border-bottom: 2px solid #e2e8f0;
    }

    .table tbody tr {
        transition: background 0.3s ease;
    }

    .table tbody tr:hover {
        background: #f1f5f9;
    }

    .table tbody td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #2d3748;
        font-size: 14px;
    }

    .btn-primary, .btn-danger {
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(37, 117, 252, 0.4);
    }

    .btn-danger {
        background: #e53e3e;
        border: none;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(229, 62, 62, 0.4);
    }

    .btn-add {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        display: inline-block;
        margin-bottom: 20px;
    }

    .btn-add:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(37, 117, 252, 0.4);
    }

    .text-danger {
        color: #e53e3e;
        font-weight: 600;
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
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            font-size: 12px;
        }
        .btn-primary, .btn-danger {
            padding: 6px 10px;
            font-size: 12px;
        }
    }
</style>

<div class="page-wrapper">
    <!-- Bread crumb -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">View Rooms</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">View Rooms</li>
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
                        <a href="add_room.php" class="btn-add">Add New Room</a>
                        <div class="table-responsive m-t-40">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Room Number</th>
                                        <th>Room Type</th>
                                        <th>Price per Adult</th>
                                        <th>Price per Kid</th>
                                        <th>Amenities</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Room Number</th>
                                        <th>Room Type</th>
                                        <th>Price per Adult</th>
                                        <th>Price per Kid</th>
                                        <th>Amenities</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM `tbl_rooms`";
                                    $result = $conn->query($sql);
                                    $i = 1;
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo htmlspecialchars($row['roomno']); ?></td>
                                            <td><?php echo htmlspecialchars($row['roomname']); ?></td>
                                            <td><?php echo number_format($row['per_adult_price'], 2); ?></td>
                                            <td><?php echo number_format($row['per_kid_price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($row['amenities'] ?? ''); ?></td>
                                            <td>
                                                <a href="edit_room.php?id=<?php echo $row['id']; ?>" class="btn btn-xs btn-primary" title="Edit Room"><i class="fa fa-pencil"></i></a>
                                                <a href="view_room.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-xs btn-danger" title="Delete Room"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div> <!-- col -->
        </div> <!-- row -->
    </div> <!-- container-fluid -->
</div> <!-- page-wrapper -->

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