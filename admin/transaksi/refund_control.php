<?php
session_start();

if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../../auth/login.php");
    exit;
}

require_once "../../config/database.php";

// ambil data refund + join order + user
$query = $conn->query("
    SELECT 
        refunds.*, 
        orders.product_name,
        orders.total_price,
        users.username
    FROM refunds
    JOIN orders ON refunds.order_id = orders.id
    JOIN users ON orders.user_id = users.id
    ORDER BY refunds.id DESC
");

$role = $_SESSION['officer_role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Refund Control - Orderly Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
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
            margin-top: auto; 
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
            margin-bottom: 30px;
            background: var(--sidebar-bg);
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        .header-title {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .header-title h2 {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary);
        }

        .header-title div {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-gray);
            font-size: 14px;
        }

        .btn-back {
            background: #f1f5f9;
            color: var(--text-gray);
            border: 1px solid #e2e8f0;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-back:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(30, 41, 59, 0.2);
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }

        th {
            background: rgba(248, 250, 252, 0.8);
            color: var(--text-gray);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            color: var(--text-dark);
            font-size: 14px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .refund-id {
            font-family: monospace;
            font-weight: 600;
            color: var(--text-gray);
        }

        /* BADGES */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .badge-approved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .badge-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        /* Action Buttons */
        .action-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-action i { width: 18px; height: 18px; }

        .btn-action.approve { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .btn-action.approve:hover { background: #10b981; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }

        .btn-action.reject { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .btn-action.reject:hover { background: #ef4444; color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }

    </style>
</head>
<body>

<div class="sidebar">
    <h2><i data-lucide="shield-check"></i> Order<span>ly</span></h2>
    <ul>
        <li><a href="../dashboard.php"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
        <li><a href="../product/index.php"><i data-lucide="package"></i> Products</a></li>
        <li><a href="index.php" class="active"><i data-lucide="shopping-cart"></i> Transactions</a></li>
        <li><a href="../laporan/index.php"><i data-lucide="file-bar-chart"></i> Reports</a></li>
        <?php if($role === 'admin') { ?>
            <li><a href="../user/index.php"><i data-lucide="users"></i> Officers</a></li>
            <li><a href="../backup/backup.php"><i data-lucide="database"></i> Backup</a></li>
        <?php } ?>
        <li><a href="../notifications.php"><i data-lucide="bell"></i> Notifications <span class="notif-badge" style="background:#ef4444; color:white; font-size:11px; padding:2px 6px; border-radius:10px; margin-left:auto; display:none;">0</span></a></li>
            <li><a href="../chat_admin.php"><i data-lucide="message-square"></i> Support</a></li>
        <li><a href="../../auth/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
    </ul>
</div>

<div class="content">

    <div class="header">
        <div class="header-title">
            <h2>Refund Control</h2>
            <div><i data-lucide="refresh-ccw" style="width:14px; height:14px;"></i> Customer Refund Requests</div>
        </div>
        <a href="index.php" class="btn-back">
            <i data-lucide="arrow-left"></i> Back to Transactions
        </a>
    </div>

    <table>
        <tr>
            <th>Refund ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Total Price</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while($row = $query->fetch_assoc()){ ?>
        <tr>
            <td><span class="refund-id">#RFD-<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
            <td style="font-weight: 500; color: var(--primary);"><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td style="font-weight: 600;">Rp <?= number_format($row['total_price']) ?></td>
            <td>
                <div style="font-size: 13px; color: var(--text-gray); line-height: 1.4; max-width: 200px;">
                    <?= htmlspecialchars($row['reason']) ?>
                </div>
            </td>
            <td>
                <?php
                if($row['status'] == 'pending'){
                    echo '<span class="badge badge-pending"><div class="status-dot-mini" style="width:6px;height:6px;border-radius:50%;background:#f59e0b;"></div> Pending</span>';
                }elseif($row['status'] == 'approved'){
                    echo '<span class="badge badge-approved"><i data-lucide="check-circle" style="width:12px;"></i> Approved</span>';
                }else{
                    echo '<span class="badge badge-rejected"><i data-lucide="x-circle" style="width:12px;"></i> Rejected</span>';
                }
                ?>
            </td>
            <td>
                <?php if($row['status'] == 'pending'){ ?>
                    <div class="action-group">
                        <a href="approve_refund.php?id=<?= $row['id'] ?>" 
                           onclick="return confirm('Approve this refund request?')"
                           class="btn-action approve" title="Approve">
                            <i data-lucide="check"></i>
                        </a>
                        <a href="reject_refund.php?id=<?= $row['id'] ?>" 
                           onclick="return confirm('Reject this refund request?')"
                           class="btn-action reject" title="Reject">
                            <i data-lucide="x"></i>
                        </a>
                    </div>
                <?php } else { ?>
                    <span style="color: #cbd5e1;"><i data-lucide="minus"></i></span>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

<script>
    lucide.createIcons();
</script>


    <!-- NOTIF LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../api_notif.php')
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