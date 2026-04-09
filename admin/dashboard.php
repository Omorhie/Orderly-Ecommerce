<?php
session_start();
if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

$qRecent = $conn->query("
    SELECT transactions.*, users.username 
    FROM transactions 
    JOIN users ON transactions.user_id = users.id
    ORDER BY transactions.id DESC
    LIMIT 5
");


// total produk
$qProduk = $conn->query("SELECT COUNT(*) AS total FROM products");
$totalProduk = $qProduk->fetch_assoc()['total'];

// total user (role user saja)
$qUser = $conn->query("SELECT COUNT(*) AS total FROM officer");
$totalUser = $qUser->fetch_assoc()['total'];

// total transaksi
$qTransaksi = $conn->query("SELECT COUNT(*) AS total FROM transactions");
$totalTransaksi = $qTransaksi->fetch_assoc()['total'];

// total pendapatan (dari order yang Confirmed / selesai)
$qRevenue = $conn->query("SELECT COALESCE(SUM(total_price), 0) AS total FROM orders WHERE status = 'Confirmed'");
$totalRevenue = $qRevenue->fetch_assoc()['total'];
?>

<?php
$role = $_SESSION['officer_role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Orderly</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            /* Admin Slate Palette */
            --primary: #1e293b;
            --secondary: #334155;
            --accent: #3b82f6;
            --bg-color: #f8fafc;
            --sidebar-bg: #ffffff;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar h2 span {
            color: var(--accent);
        }

        .sidebar ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            transition: var(--transition);
        }

        .sidebar ul li a i {
            width: 20px;
            height: 20px;
        }

        .sidebar ul li a:hover, 
        .sidebar ul li a.active {
            background-color: var(--primary);
            color: #ffffff;
            transform: translateX(5px);
            box-shadow: 0 10px 15px -3px rgba(30, 41, 59, 0.2);
        }

        .sidebar ul li:last-child {
            margin-top: auto; /* push logout to bottom */
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .sidebar ul li:last-child a:hover {
            background-color: #ef4444;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
        }

        /* CONTENT */
        .content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: var(--sidebar-bg);
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        .header-title p {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary);
        }

        .header-title span {
            color: var(--accent);
        }

        #datetime {
            color: var(--text-gray);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* METRIC CARDS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: var(--sidebar-bg);
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent);
        }

        .card-icon.products { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .card-icon.users { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .card-icon.transactions { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .card-icon.revenue { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

        .card-info {
            display: flex;
            flex-direction: column;
        }

        .card-info h3 {
            font-size: 15px;
            color: var(--text-gray);
            font-weight: 500;
        }

        .card-info .number {
            font-size: 28px;
            color: var(--primary);
            font-weight: 700;
            line-height: 1.2;
        }

        /* CHART */
        .chart-container {
            background: var(--sidebar-bg);
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            width: 100%;
            height: 400px;
        }

        .chart-header {
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2><i data-lucide="shield-check"></i> Order<span>ly</span></h2>
        <ul>
            <li><a href="dashboard.php" class="active"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
            <li><a href="product/index.php"><i data-lucide="package"></i> Products</a></li>
            <li><a href="transaksi/index.php"><i data-lucide="shopping-cart"></i> Transactions</a></li>
            <li><a href="laporan/index.php"><i data-lucide="file-bar-chart"></i> Reports</a></li>
            <?php if($role === 'admin') { ?>
                <li><a href="user/index.php"><i data-lucide="users"></i> Officers</a></li>
                <li><a href="backup/backup.php"><i data-lucide="database"></i> Backup</a></li>
            <?php } ?>
            <li><a href="notifications.php"><i data-lucide="bell"></i> Notifications <span class="notif-badge" style="background:#ef4444; color:white; font-size:11px; padding:2px 6px; border-radius:10px; margin-left:auto; display:none;">0</span></a></li>
            <li><a href="chat_admin.php"><i data-lucide="message-square"></i> Support</a></li>
            <li><a href="../auth/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <div class="header">
            <div class="header-title">
                <p>Welcome back, <span><?= $_SESSION['officer_username']; ?>!</span></p>
            </div>
            <div id="datetime"><i data-lucide="clock"></i> <span></span></div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="card-icon products">
                    <i data-lucide="box" style="width: 28px; height: 28px;"></i>
                </div>
                <div class="card-info">
                    <h3>Total Products</h3>
                    <div class="number"><?= $totalProduk ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-icon users">
                    <i data-lucide="users" style="width: 28px; height: 28px;"></i>
                </div>
                <div class="card-info">
                    <h3>Total Officers</h3>
                    <div class="number"><?= $totalUser ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-icon transactions">
                    <i data-lucide="activity" style="width: 28px; height: 28px;"></i>
                </div>
                <div class="card-info">
                    <h3>Total Transactions</h3>
                    <div class="number"><?= $totalTransaksi ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-icon revenue">
                    <i data-lucide="wallet" style="width: 28px; height: 28px;"></i>
                </div>
                <div class="card-info">
                    <h3>Total Income</h3>
                    <div class="number" style="font-size: 22px;">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <i data-lucide="bar-chart-2"></i> System Metrics Overview
            </div>
            <canvas id="myChart"></canvas>
        </div>
    </div>

    <script>
        // Init Icons
        lucide.createIcons();

        // Time Updates
        function updateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.querySelector("#datetime span").innerHTML = now.toLocaleDateString('en-US', options);
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Chart Data
        const ctx = document.getElementById('myChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Products', 'Officers', 'Transactions'],
                datasets: [{
                    label: 'System Data',
                    data: [<?= $totalProduk ?>, <?= $totalUser ?>, <?= $totalTransaksi ?>],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)'
                    ],
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    </script>

    <!-- NOTIF LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('api_notif.php')
            .then(r => r.json())
            .then(data => {
                let badge = document.querySelector('.notif-badge');
                if(badge && data.count > 0) {
                    badge.style.display = 'inline-block';
                    badge.textContent = data.count;
                }
            }).catch(e=>console.log(e));
        });
    </script>
</body>
</html>
