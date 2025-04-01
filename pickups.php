<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
require "../db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Pickups</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light">E-Waste Admin</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="pickups.php" class="nav-link active">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Pickups</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="trucks.php" class="nav-link">
                            <i class="nav-icon fas fa-truck-moving"></i>
                            <p>Trucks</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="drivers.php" class="nav-link">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>Drivers</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="routes.php" class="nav-link">
                            <i class="nav-icon fas fa-map"></i>
                            <p>Pickup Route Map</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Manage Pickups</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pickup Table -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Pickup Requests</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pickupsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Dangerous</th>
                                        <th>Size</th>
                                        <th>Weight</th>
                                        <th>Image</th>
                                        <th>Address</th>
                                        <th>Pincode</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Vehicle</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
    <?php
    $sql = "SELECT * FROM pickups ORDER BY created_at DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $imagePath = "uploads/" . $row['image'];

        echo "<tr id='row_{$row['id']}'>
                <td>{$row['id']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='title'>{$row['title']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='type'>{$row['type']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='is_dangerous'>{$row['is_dangerous']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='size'>{$row['size']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='weight'>{$row['weight']}</td>
                <td><img src='$imagePath' width='50' height='50' onerror=\"this.src='assets/no-image.png'\"></td>
                <td class='editable' data-id='{$row['id']}' data-field='address'>{$row['address']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='pincode'>{$row['pincode']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='latitude'>{$row['latitude']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='longitude'>{$row['longitude']}</td>
                <td class='editable' data-id='{$row['id']}' data-field='vehicle_id'>{$row['vehicle_id']}</td>
                <td>{$row['user_email']}</td>
                <td>{$row['created_at']}</td>
                <td>
                    <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>
                        <i class='fas fa-trash'></i>
                    </button>
                </td>
            </tr>";
    }
    ?>
</tbody>


                            </table>
                        </div>  
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
$(document).ready(function () {
    var table = $("#pickupsTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        "ordering": true,
        "pageLength": 10,
        "scrollX": true,
        "scrollY": "60vh",
        "dom": 'Bfrtip',
        "buttons": ["copy", "csv", "excel", "print"]
    });

    // DELETE FUNCTION
    $("#pickupsTable").on("click", ".delete-btn", function () {
        var pickupId = $(this).data("id");
        var row = $(this).closest("tr");

        Swal.fire({
            title: "Are you sure?",
            text: "This record will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "delete_pickup.php",
                    type: "POST",
                    data: { id: pickupId },
                    success: function (response) {
                        var res = JSON.parse(response);
                        if (res.status === "success") {
                            Swal.fire("Deleted!", res.message, "success");
                            table.row(row).remove().draw(false);
                        } else {
                            Swal.fire("Error!", res.message, "error");
                        }
                    }
                });
            }
        });
    });

    // INLINE EDIT FUNCTION
    $("#pickupsTable").on("dblclick", ".editable", function () {
        var cell = $(this);
        var originalValue = cell.text();
        var field = cell.data("field");
        var id = cell.data("id");

        var input = $("<input>", {
            type: "text",
            value: originalValue,
            class: "form-control form-control-sm"
        });

        cell.html(input);
        input.focus();

        // Save when focus is lost
        input.blur(function () {
            var newValue = $(this).val();
            if (newValue !== originalValue) {
                $.ajax({
                    url: "update_pickup.php",
                    type: "POST",
                    data: { id: id, field: field, value: newValue },
                    success: function (response) {
                        var res = JSON.parse(response);
                        if (res.status === "success") {
                            cell.html(newValue);
                            Swal.fire("Updated!", res.message, "success");
                        } else {
                            cell.html(originalValue);
                            Swal.fire("Error!", res.message, "error");
                        }
                    }
                });
            } else {
                cell.html(originalValue);
            }
        });

        // Save when pressing Enter
        input.keypress(function (e) {
            if (e.which === 13) {
                $(this).blur();
            }
        });
    });
});


</script>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
</body>
</html>
