<?php
require("../../Inc/function.php");
session_start();
if (!isset($_SESSION['aID'])) {
    header("location:./adminlogin.php");
}


$a_id = $_SESSION['aID'];

$query = "SELECT username FROM admin  WHERE id = $a_id";
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $list = mysqli_fetch_assoc($result);
    $name = $list['username'];
}


?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../CSS SCRIPTS/admin_panel/orderListDesign.css">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" type="image/x-icon" href="../../ICONS/adminbro3.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>

</head>

<body>
    <div class="sidebar">
        <div class="logo-details">
            <i class='bx bxs-building-house'></i>
            <span class="logo_name">HANDYMAN</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="admindashboard.php">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">Admin Dashboard</span>
                </a>
            </li>
            <li>
                <a href="AdminProfile.php">
                    <i class='bx bx-box'></i>
                    <span class="links_name">Profile</span>
                </a>
            </li>
            <!--add new admin-->
            <li>
                <a href="addAdmin.php" >
                    <i class='bx bx-user-plus'></i>
                    <span class="links_name">Add Admin</span>
                </a>
            </li>
            <!--add anew admin-->
            <li>
                <a href="#" class="active">
                    <i class='bx bx-list-ul'></i>
                    <span class="links_name">Order list</span>
                </a>
            </li>
            <li>
                <a href="customerlist.php">
                    <i class='bx bx-pie-chart-alt-2'></i>
                    <span class="links_name">Customer List</span>
                </a>
            </li>
            <li>
                <a href="workerlist.php">
                    <i class='bx bxs-user'></i>
                    <!-- <i class='bx bx-coin-stack'></i> -->
                    <span class="links_name">Worker List</span>
                </a>
            </li>
            <li>
                <a href="ComplaintList.php">
                    <i class='bx bxs-file-import'></i>
                    <span class="links_name">Complaint List</span>
                </a>
            </li>
            <li>
                <a href="ServiceList.php">
                    <!-- <i class='bx bxs-user-voice'></i> -->
                    <i class='bx bx-coin-stack'></i>
                    <span class="links_name">Service List</span>
                </a>
            </li>
            <!--Service Modification-->
            <li>
                <a href="modifyServices.php">
                    <!-- <i class='bx bxs-user-voice'></i> -->
                    <i class='bx bx-wrench'></i>
                    <span class="links_name">Service Modification</span>
                </a>
            </li>
            <!--Service modification-->

            <!--Banned list link-->
            <li>
                <a href="BannedList.php">
                    <i class='bx bxs-error'></i>
                    <span class="links_name">Banned List</span>
                </a>
            </li>
            <!--Banned list link-->

            <!--send message link-->
            <li>
                <a href="AdminMessageBox.php">
                    <i class='bx bxs-message-alt-edit'></i>
                    <span class="links_name">Message Box</span>
                </a>
            </li>
            <!--send message link-->
            <!--Message history link-->
            <li>
                <a href="AdminMessageHistory.php">
                    <i class='bx bx-history'></i>
                    <span class="links_name">Complaint History</span>
                </a>
            </li>
            <!--Message History link-->
            <!--Appeal history link-->
            <li>
                <a href="AppealHistory.php">
                    <i class='bx bx-user-voice'></i>
                    <span class="links_name">Appeal History</span>
                </a>
            </li>
            <!--Appeal History link-->

            <li class="log_out">
                <a href="../../Inc/admin_logout.php">
                    <i class='bx bx-log-out'></i>
                    <span class="links_name">Log out</span>
                </a>
            </li>
        </ul>
    </div>
    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class='bx bx-menu sidebarBtn'></i>
                <span class="dashboard">Order List</span>
            </div>

            <form method="POST">
                <div class="search-box">
                    <input type="text" name="input" placeholder="Search by Order ID..." required>
                    <button name="search"><i class='bx bx-search'></i></button>
                </div>
            </form>

            <div class="profile-details">
                <img src="../../ICONS/adminboss.png" alt="adminaccount">
                <span class="admin_name"><?php echo $name ?></span>
            </div>
        </nav>


        <div class="home-content">

            <div class="sales-boxes">
                <div class="recent-sales box">
                    <div class="title">Order Details</div>
                    <div class="sales-details">

                        <!--changed part-->
                        <div class="sales-details" style="margin-top: 23px;">

                            <table>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Customer Address</th>
                                    <th>Worker Name</th>
                                    <th>Worker ID</th>
                                    <th>Worker Type</th>
                                    <th>Working Date</th>
                                    <th>Working Shift</th>
                                    <th>Worker Payment</th>
                                    <th>Status</th>
                                    <th>Rating</th>
                                </tr>
                                <?php

                                if (isset($_POST['search'])) {
                                    $o_id = $_POST['input'];

                                    $query = "SELECT * FROM orderlist WHERE id = $o_id ";
                                    $result = mysqli_query($connect, $query);
                                } else {
                                    $query = "SELECT * FROM orderlist";
                                    $result = mysqli_query($connect, $query);
                                }


                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($data = mysqli_fetch_assoc($result)) {
                                        $id = $data['id'];
                                        $e_name = $data['e_name'];
                                        $e_id = $data['e_id'];
                                        $u_name = $data['u_name'];
                                        $u_address = $data['u_address'];
                                        $date = $data['date'];
                                        $shift = $data['shift'];
                                        $type = $data['e_type'];
                                        $payment = $data['payment'];
                                        $status = $data['status'];
                                        $rating = $data['rating'];

                                        echo "
                                        <tr>
                                            <td>$id</td>
                                            <td>$u_name</td>
                                            <td>$u_address</td>
                                            <td>$e_name</td>
                                            <td>$e_id</td>
                                            <td>$type</td>
                                            <td>$date</td>
                                            <td>$shift</td>
                                            <td>$payment</td>
                                            <td>$status</td>
                                            <td>$rating</td>
                                        </tr>
                                        ";
                                    }
                                }

                                ?>
                            </table>
                        </div>
                        <!-- <div class="button" style="margin-top: 12px;">
                            <a href="#">See All</a>
                        </div> -->
                    </div>
                </div>
                <!--changed part-->

    </section>
</body>

</html>