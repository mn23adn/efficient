<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pickup Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .login-box, .register-box {
	width: 676px;
}


</style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
   
    
 
    <div class="card">
    <div class="login-logo">
    <br> <br> <br>
   
        <a href="#"><b>Pickup</b> Request</a>
    </div>
        <div class="card-body login-card-body">
            <p class="login-box-msg">Welcome, <?php echo $_SESSION['user']; ?> | <a href="logout.php">Logout</a></p>
            <div style="">
                <form id="pickupForm" enctype="multipart/form-data">
                    <div class="input-group mb-3">
                        <input type="text" name="title" class="form-control" placeholder="Title" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="type" class="form-control" placeholder="Type" required>
                    </div>
                    <div class="form-group">
                        <label>Is Dangerous?</label><br>
                        <input type="radio" name="is_dangerous" value="Yes" required> Yes
                        <input type="radio" name="is_dangerous" value="No" required> No
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="size" class="form-control" placeholder="Size" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="weight" class="form-control" placeholder="Weight" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Photo (Image Upload)</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" id="address" name="address" class="form-control" placeholder="Enter Address..." required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" id="pincode" name="pincode" class="form-control" placeholder="Fetching Pincode..." required readonly>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Fetching Latitude..." required readonly>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Fetching Longitude..." required readonly>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="vehicle_id" class="form-control" placeholder="Vehicle ID" required>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </div>
                </form>
            </div>  
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Address Autocomplete using OpenStreetMap API
        $("#address").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "https://nominatim.openstreetmap.org/search",
                    data: {
                        q: request.term,
                        format: "json",
                        addressdetails: 1,
                        limit: 5
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.display_name,
                                value: item.display_name,
                                lat: item.lat,
                                lon: item.lon,
                                postcode: item.address.postcode || "Not Available"
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $("#latitude").val(ui.item.lat);
                $("#longitude").val(ui.item.lon);
                $("#pincode").val(ui.item.postcode);
            }
        });

        // Fetch user's location details (Latitude, Longitude, and Pincode)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                $("#latitude").val(position.coords.latitude);
                $("#longitude").val(position.coords.longitude);

                // Use OpenStreetMap Nominatim API to get the pincode
                $.get(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`, function(data) {
                    if (data.address.postcode) {
                        $("#pincode").val(data.address.postcode);
                    } else {
                        $("#pincode").val("Not Found");
                    }
                });
            });
        }

        // AJAX Form Submission
        $("#pickupForm").on("submit", function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: "pickup_process.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status == "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: res.message
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: res.message
                        });
                    }
                }
            });
        });
    });
</script>

</body>
</html>
