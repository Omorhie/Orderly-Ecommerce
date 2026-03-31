<?php
session_start();
if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";


// total produk
$qProduk = $conn->query("SELECT COUNT(*) AS total FROM products");
$totalProduk = $qProduk->fetch_assoc()['total'];

// total user (role user saja)
$qUser = $conn->query("SELECT COUNT(*) AS total FROM officer");
$totalUser = $qUser->fetch_assoc()['total'];

// total transaksi
$qTransaksi = $conn->query("SELECT COUNT(*) AS total FROM transactions");
$totalTransaksi = $qTransaksi->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
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
                width: 100%;
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
        .content {
            flex: 1;
            padding: 30px;
        }

        .header {
            padding: 20px;
            margin-bottom: 20px;
            font-size: 36px;
            color: #fff;
            text-align: center;
        }

        .header p {
            font-weight: 540;
        }

        .header span {
            color: #526D82;
        }

        .cards {
    display: flex;
    gap: 20px;
    align-items: stretch;
}


.card {
    width: 300px;
    height: 200px;
    background-color: #D9D9D9;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    transition: 0.3s;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transform: translateX(150px);
}

.card:hover{
    height: 250px;
}

.card h3 {
    font-size: 20px;
    color: #64748b;
    margin-bottom: 10px;
    text-align: center;
    font-weight: bold;
}

.card .number {
    font-size: 28px;
    color: #27374D;
    font-weight: bold;
    text-align: center
}

    </style>
</head>
<body>

<!-- SIDEBAR -->
  <div class="sidebar">
        <h2>Ordely</h2>
<ul>
    <li><a href="dashboard.php" class="active">Home</a></li>

    <li>
        <a href="product/index.php">
            Product Management
        </a>
    </li>

    <li><a href="user/index.php">Officer Management</a></li>
    <li><a href="transaksi/index.php">Transactions</a></li>
    <li><a href="laporan/index.php">Report</a></li>
    <li><a href="backup/backup.php">Backup/Restore</a></li>
    <li><a href="../auth/admin/logout.php">Logout</a></li>
</ul>

    </div>

<!-- CONTENT -->
<div class="content">
    <div class="header">
        <p>Welcome, <span><?= $_SESSION['officer_username']; ?>!</span></p>
    </div>

   <div class="cards">
    <div class="card">
        <h3>Total Produk</h3>
        <div class="number"><?= $totalProduk?></div>
    </div>

    <div class="card">
        <h3>Total User</h3>
        <div class="number"><?= $totalUser?></div>
    </div>

    <div class="card">
        <h3>Total Transaksi</h3>
        <div class="number"><?= $totalTransaksi?></div>
    </div>
</div>



</div>

</body>
</html>
