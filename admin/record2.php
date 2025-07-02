<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header('Location: ../staff_login.php');
    exit();
}

include 'conn.php'; // Database connection

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];

// Fetch the total number of drivers registered by this staff member
$total_registered_by_staff = 0;
$query = "SELECT COUNT(*) AS total FROM driver_info WHERE staff_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $total_registered_by_staff = $row['total'];
}
$stmt->close();

// Fetch all drivers registered by this staff member
$drivers = [];
$query = "SELECT id, driver_name, driver_photo, phone, dl_number, area_postal_code, address, vehicle_type FROM driver_info WHERE staff_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $drivers[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Records</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .content {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .action-btn:hover {
            background-color: #0056b3;
        }

        .center {
            text-align: center;
        }

        .driver-photo {
            max-width: 50px;
            max-height: 50px;
        }
    </style>
</head>

<body>
    <div class="content">
        <h2>Drivers Registered by <b style="color:red;">
            <?php echo htmlspecialchars($staff_name); ?>
        </b></h2>

        <h3 class="center">Total Drivers Registered: <?php echo htmlspecialchars($total_registered_by_staff); ?></h3>

        <table>
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Id</th>
                    <th>Driver Name</th>
                    <th>Driver Photo</th>
                    <th>Phone</th>
                    <th>DL Number</th>
                    <th>Area/Postal Code</th>
                    <th>Address</th>
                    <th>Vehicle Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drivers as $index => $driver): ?>
                <tr>
                    <td><?php echo htmlspecialchars($index + 1); ?></td>
                    <td><?php echo htmlspecialchars($driver['id']); ?></td>
                    <td><?php echo htmlspecialchars($driver['driver_name']); ?></td>
                    <!-- <td><img src="<?php echo htmlspecialchars($driver['driver_photo']); ?>" alt="Driver Photo" class="driver-photo"></td> -->
                    <td>
                                <img src="../images/<?php echo htmlspecialchars ($driver['driver_photo']); ?>" alt="Driver Photo" style="width: 50px; height: 50px;">
                            </td>
                    <td><?php echo htmlspecialchars($driver['phone']); ?></td>
                    <td><?php echo htmlspecialchars($driver['dl_number']); ?></td>
                    <td><?php echo htmlspecialchars($driver['area_postal_code']); ?></td>
                    <td><?php echo htmlspecialchars($driver['address']); ?></td>
                    <td><?php echo htmlspecialchars($driver['vehicle_type']); ?></td>
                    <td><a href="view_driver.php?id=<?php echo htmlspecialchars($driver['id']); ?>" class="action-btn">View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
