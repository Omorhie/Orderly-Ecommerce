<?php
session_start();

if(!isset($_SESSION['username_user'])){
    header("Location: ../auth/user/login.php");
    exit;
}


require_once "../config/database.php";
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE user_id='$user_id'
    ORDER BY order_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>History - Orderly</title>

<style>
body{
    min-height:100vh;
    display:flex;
    flex-direction:column;
}
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:30px 30px;
    background:#0F2854;
    color:white;
}

.logo{
    font-size:24px;
}

.logo span{
    color:#4988C4;
}

/* NAV CENTER */
.nav-center{
    display:flex;
    gap:40px;
}

.nav-center a{
    color:#4988C4;
    text-decoration:none;
    font-size:18px;
    transition:.3s;
}

.nav-center a:hover{
    color:#fff;
}

.nav-center a.active{
    color:#fff;
    font-weight:bold;
}

/* NAV RIGHT */
.nav-right a{
    color:white;
    text-decoration:none;
    font-size:18px;
    transition:.3s;
}

.nav-right a:hover{
    color:#4988C4;
}

/* CONTAINER */
.container{
    width:85%;
    margin:50px auto;
    flex:1;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
    border-radius:16px;
    overflow:hidden;
}

thead{
    background:#0F2854;
    color:white;
}

th, td{
    padding:18px;
    text-align:left;
}

th{
    font-weight:600;
}



tbody tr{
    border-bottom:1px solid #eee;
    transition:0.2s;
}

tbody tr:hover{
    background:#f5f9ff;
}

.price{
    color:#0F2854;
    font-weight:500;
}

</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">Order<span>ly</span></div>

    <div class="nav-center">
        <a href="cart.php">Cart</a>
        <a href="checkout.php">Checkout</a>
        <a href="#" class="active">History</a>
    </div>

    <div class="nav-right">
        <a href="dashboard.php">Back</a>
    </div>
</div>

<!-- CONTENT -->
<div class="container">

<table>
<thead>
    <tr>
        <th>ID</th>
        <th>Product Name</th>
        <th>Address</th>
        <th>Method</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
</thead>
<tbody>
<?php 
$no = 1;
while($row = mysqli_fetch_assoc($query)){ 
?>
<tr>
    <td>#<?= $no++; ?></td>
    <td><?= $row['product_name']; ?></td>
    <td><?= $row['address']; ?></td>
    <td><?= $row['method']; ?></td>
    <td class="price">Rp <?= number_format($row['total_price']); ?></td>
    <td>
        <?php
            if($row['status'] == 'Pending'){
                echo "<span style='color:orange;font-weight:bold;'>Pending</span>";
            } elseif($row['status'] == 'Paid'){
                echo "<span style='color:green;font-weight:bold;'>Paid</span>";
            } elseif($row['status'] == 'Shipped'){
                echo "<span style='color:blue;font-weight:bold;'>Shipped</span>";
            } else {
                echo "<span style='color:gray;font-weight:bold;'>".$row['status']."</span>";
            }
        ?>
    </td>
    <td><?= date("d F Y", strtotime($row['order_date'])); ?></td>
</tr>
<?php } ?>
</tbody>
</table>

</div>


</body>
</html>