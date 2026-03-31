<?php
session_start();
if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../auth/login.php");
    exit;
}

require_once "../../config/database.php";

// ambil data
$stock = $conn->query("SELECT id, name, stock, price, created_at FROM products ORDER BY id DESC");
$sales = $conn->query("
    SELECT 
        transactions.id,
        transactions.order_id,
        transactions.product_name,
        transactions.qty,
        transactions.created_at
    FROM transactions
    ORDER BY transactions.order_id DESC
");
$transaction = $conn->query("
    SELECT 
        transactions.*, 
        users.username,
        orders.method,
        orders.status
    FROM transactions
    JOIN users ON transactions.user_id = users.id
    JOIN orders ON transactions.order_id = orders.id
    ORDER BY transactions.id DESC
");


?>

<!DOCTYPE html>
<html>
<head>
<title>Laporan</title>

<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

            body {
            display: flex;
            min-height: 100vh;
            background-color: #27374D;
        }

                /* SIDEBAR */
        .sidebar {
            width: 250px;
            background-color: #D9D9D9;
            color: #fff;
            padding-top: 50px;
            padding-bottom: 20px;
            border-radius: 0px 16px 16px 0px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 30px;
            font-weight: 550;
            color: #27374D;
        }

        .sidebar ul {
            list-style: none;
            
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #27374D;
            display: block;
            padding: 20px;
            transition: 0.3s;
            text-align: center;
            font-size: 20px;
            height: 60px;
            font-weight: 540;
        }

        .sidebar a {
            width: 100%;
        }

        .sidebar ul li a:hover {
            background-color: #27374D;
            color: #fff;
            height: 100px;
            text-align: center;
            padding-top: 40px;
        }

                    .sidebar ul li a:hover {
                background-color: #27374D;
                color: #fff;
                height: 100px;
                text-align: center;
                    font-weight: bold;
                padding-top: 40px;
            }

            /* ACTIVE SIDEBAR */
.sidebar ul li a.active {
    background-color: #27374D;
    color: white;   
    font-weight: bold;
    height: 100px;
    text-align: center;
    padding-top: 40px
}

/* CONTENT */
.content{
    flex:1;
    padding:30px;
}

            .header {
                padding-top: 10px;
                margin-bottom: 20px;
                font-size: 25px;
                color: #fff;
                justify-content: space-between;
                display: flex;
            }

            .header span {
                color: #526D82;
            }

            .header h2  {
                padding-top: 10px;
                font-weight: 540;
            }

/* BUTTON SWITCH */
.switch-btn{
    padding:15px 50px;
    border:none;
    border-radius:16px;
    background:#526D82;

    cursor:pointer;
    font-weight:bold;
    font-size: 16px;
    color: #D9D9D9;
    transition: all 300ms;
}

.switch-btn.active{
    background:#D9D9D9;
    color:#526D82;
}

.switch-btn:hover{
    background: #D9D9D9;
    color: #526D82;
}

            table {
                width: 100%;
                border-collapse: collapse;
                background: white;
                border-radius: 0 16px 16px 16px;
                overflow: hidden;
                box-shadow: 0 4px 8px rgba(0,0,0,0.05);
                transition: all 300ms;
            }


            th, td {
                padding: 12px;
                border-bottom: 1px solid #bdbdbd;
                text-align: center;
                background-color: #D9D9D9;
                color: #526D82;
            }

            td {
                border-right: 1px solid #bdbdbd;
                font-weight: bold;
            }

            th {
                background: #D9D9D9;
                color: #526D82;
                height: 50px;
                font-weight: bold;
                text-align: center;
                font-size: 19px;

            }

.btn-delete{
    background:none;
    color: #B61E1E;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
}

.btn-view{
    background:none;
    color:#77D42F;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
}

/* hide table */
.table-section{
    display:none;
}

.table-section.active{
    display:block;
}

/* CONTAINER BUTTON */
.switch-container{
    display:flex;
    gap:0; /* tanpa jarak */
    margin-bottom:0; /* nempel ke table */
}

/* biar benar-benar nempel */
.switch-btn{
    border-radius:0;
}

/* button pertama kiri rounded */
.switch-btn:first-child{
    border-radius:10px 0 0 0;
}

/* button terakhir kanan rounded */
.switch-btn:last-child{
    border-radius:0 10px 0 0;
    border-right:none;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
  <div class="sidebar">
        <h2>Ordely</h2>
<ul>
    <li><a href="../dashboard.php">Home</a></li>

    <li>
        <a href="../product/index.php">
            Product Management
        </a>
    </li>

    <li><a href="../user/index.php">Officer Management</a></li>
    <li><a href="../transaksi/index.php">Transactions</a></li>
    <li><a href="../laporan/index.php" class="active">Report</a></li>
    <li><a href="../backup/backup.php">Backup/Restore</a></li>
    <li><a href="../../auth/admin/logout.php">Logout</a></li>
</ul>

    </div>

    <div class="content">

        <div class="header">
            <div class="header">
             <h2>Product <span>Report</span></h2>
            </div>

            <br>

        </div>


<!-- BUTTON SWITCH -->
<div class="switch-container">
<button class="switch-btn active" onclick="showTable('stock',this)">Stock</button>
<button class="switch-btn" onclick="showTable('sales',this)">Sales</button>
<button class="switch-btn" onclick="showTable('transaction',this)">Transaction</button>
</div>

<!-- ================= STOCK ================= -->
<div id="stock" class="table-section active">

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Stock</th>
<th>Price</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php while($row=$stock->fetch_assoc()){ ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['stock'] ?></td>
<td>Rp <?= number_format($row['price']) ?></td>
<td><?= $row['created_at'] ?? '-' ?></td>
<td>
<a href="delete_stock.php?id=<?= $row['id'] ?>" class="btn-delete">Delete</a>
</td>
</tr>
<?php } ?>

</table>
</div>

<!-- ================= SALES ================= -->
<div id="sales" class="table-section">

<table>
<tr>
<th>Order ID</th>
<th>Product Name</th>
<th>Qty</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php while($row=$sales->fetch_assoc()){ ?>
<tr>
<td><?= $row['order_id'] ?></td>
<td><?= htmlspecialchars($row['product_name']) ?></td>
<td><?= $row['qty'] ?></td>
<td><?= $row['created_at'] ?></td>
<td>
<a href="delete_sales.php?id=<?= $row['id'] ?>"
   onclick="return confirm('Hapus data sales ini?')"
   class="btn-delete">
Delete
</a>
</td>
</tr>
<?php } ?>

</table>
</div>

<!-- ================= TRANSACTION ================= -->
<div id="transaction" class="table-section">

<table>
<tr>
<th>ID</th>
<th>Username</th>
<th>Product Name</th>
<th>Price</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php while($row=$transaction->fetch_assoc()){ ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['username'] ?></td>
<td><?= $row['product_name'] ?></td>
<td>Rp <?= number_format($row['price']) ?></td>
<td><?= $row['created_at'] ?></td>
<td>
<a href="receipt.php?id=<?= $row['id'] ?>" class="btn-view">Receipt</a>
</td>
</tr>
<?php } ?>

</table>
</div>

</div>

<script>
function showTable(id,btn){

    // hide semua table
    document.querySelectorAll(".table-section")
    .forEach(t=>t.classList.remove("active"));

    // remove active button
    document.querySelectorAll(".switch-btn")
    .forEach(b=>b.classList.remove("active"));

    // tampilkan table
    document.getElementById(id).classList.add("active");

    // aktifkan button
    btn.classList.add("active");
}
</script>

</body>
</html>
