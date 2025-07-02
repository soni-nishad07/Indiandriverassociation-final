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
$stmt->bind_param("s", $staff_id); // 's' for string type (adjusted for your staff_id type)
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $total_registered_by_staff = $row['total'];
}
$stmt->close();

// Fetch detailed driver information registered by this staff member
$drivers_data = [];
$query = "SELECT driver_name, registration_date FROM driver_info WHERE staff_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $drivers_data[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Records</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f4f4f4;
            color: #333;
        }

        h2, h3 {
            text-align: center;
            color: #333;
        }

        h2 {
            margin-bottom: 20px;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="content">
        <h2>Forms Filled by <b style="color:red;">
                <?php echo htmlspecialchars($_SESSION['staff_name']); ?></b>
        </h2>

        <?php
        if (!empty($_SESSION['error'])) {
            echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <h3>Total Drivers Registered: <?php echo htmlspecialchars($total_registered_by_staff); ?></h3>

        <?php if (!empty($drivers_data)) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Driver Name</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($drivers_data as $driver) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($driver['driver_name']); ?></td>
                            <td><?php echo htmlspecialchars($driver['registration_date']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p style="text-align: center; color: #666;">No drivers registered yet.</p>
        <?php } ?>
    </div>
</body>

</html>
