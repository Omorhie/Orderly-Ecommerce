<?php
session_start();
if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../../auth/login.php");
    exit;
}

require_once "../../config/database.php";

$result = $conn->query("
    SELECT 
        transactions.*, 
        users.username,
        orders.method,
        orders.address,
        orders.delivery_status,
        orders.status
    FROM transactions
    JOIN users ON transactions.user_id = users.id
    JOIN orders ON transactions.order_id = orders.id
    ORDER BY transactions.id DESC
");

$role = $_SESSION['officer_role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transactions Management - Orderly Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
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

        .header h2 {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary);
        }

        .btn-refund-control {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
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

        .btn-refund-control:hover {
            background: #f59e0b;
            color: white;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
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

        .order-id {
            font-family: monospace;
            font-weight: 600;
            color: var(--accent);
            background: rgba(59, 130, 246, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
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
        .badge-confirmed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .badge-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .btn-view-proof {
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent);
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view-proof:hover {
            background: var(--accent);
            color: white;
        }

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

        .btn-action i {
            width: 18px;
            height: 18px;
        }

        .btn-action.confirm { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .btn-action.confirm:hover { background: #10b981; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }

        .btn-action.reject { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .btn-action.reject:hover { background: #f59e0b; color: white; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2); }

        .btn-action.delete { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .btn-action.delete:hover { background: #ef4444; color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--sidebar-bg);
            width: 90%;
            max-width: 600px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            display: flex;
            flex-direction: column;
            animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-gray);
            cursor: pointer;
            transition: var(--transition);
        }

        .close-btn:hover {
            color: #ef4444;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--bg-color);
        }

        #proofImage {
            max-width: 100%;
            max-height: 60vh;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- PROOF MODAL -->
<div id="proofModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i data-lucide="image"></i> Payment Proof</h3>
            <button class="close-btn" onclick="closeProof()"><i data-lucide="x"></i></button>
        </div>
        <div class="modal-body">
            <img id="proofImage">
        </div>
    </div>
</div>

<!-- SIDEBAR -->
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
        <li><a href="../chat_admin.php"><i data-lucide="message-square"></i> Support</a></li>
        <li><a href="../../auth/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
    </ul>
</div>

<div class="content">

    <div class="header">
        <h2>Transactions Overview</h2>
        <a href="refund_control.php" class="btn-refund-control">
            <i data-lucide="refresh-ccw"></i> Refund Requests
        </a>
    </div>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Shipping</th>
            <th>Delivery Track</th>
            <th>Method</th>
            <th>Total (Rp)</th>
            <th>Payment Proof</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><span class="order-id">#<?= str_pad($row['order_id'], 5, '0', STR_PAD_LEFT) ?></span></td>
            <td style="font-weight: 500; color: var(--primary);"><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td>
                <?php
                if (preg_match('/\(Shipping: (.*?) - (.*?) \[(.*?)\]\)/', $row['address'], $matches)) {
                    echo "<div style='font-size:13px; color:var(--primary); font-weight:600;'>".$matches[1]." <span style='font-weight:500;'>(".$matches[2].")</span></div>";
                    echo "<div style='font-size:12px; color:var(--text-gray);'>".$matches[3]."</div>";
                } else {
                    echo "<span style='color:#94a3b8; font-size:12px;'>Standard</span>";
                }
                $delivery = $row['delivery_status'];
                $status = strtolower($row['status']);
                ?>
            </td>
            <td>
                <?php if($status == 'confirmed' && !empty($delivery)): ?>
                    <?php if($_SESSION['officer_role'] == 'petugas'): ?>
                        <form action="update_delivery.php" method="POST" style="margin: 0; display:flex; gap:6px; align-items:center;">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <select name="delivery_status" onchange="this.form.submit()" style="padding: 6px 4px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 12px; font-weight:600; outline:none; cursor:pointer; color:var(--primary); background:var(--bg-color);">
                                <option value="Packaging" <?= $delivery == 'Packaging' ? 'selected' : '' ?>>Packaging</option>
                                <option value="Dalam Perjalanan" <?= $delivery == 'Dalam Perjalanan' ? 'selected' : '' ?>>Dalam Perjalanan</option>
                                <option value="Selesai" <?= $delivery == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </form>
                    <?php else: ?>
                        <span style="font-weight:600; font-size:13px; color:var(--accent);"><?= htmlspecialchars($delivery) ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <span style="color:#e2e8f0; font-size:13px; font-weight:600;">-</span>
                <?php endif; ?>
            </td>
            <td><span style="font-weight:500;"><?= htmlspecialchars($row['method']) ?></span></td>
            <td style="font-weight: 600;">Rp <?= number_format($row['price']) ?></td>
            
            <td>
                <?php if($row['method'] == 'Transfer' && !empty($row['proof_payment'])): ?>
                    <button class="btn-view-proof" onclick="viewProof('<?= $row['proof_payment'] ?>')">
                        <i data-lucide="eye" style="width:14px;"></i> View
                    </button>
                <?php else: ?>
                    <span style="color:#94a3b8;">N/A</span>
                <?php endif; ?>
            </td>

            <td>
                <?php 
                if($status == 'pending') {
                    echo '<span class="badge badge-pending"><div class="status-dot-mini" style="width:6px;height:6px;border-radius:50%;background:#f59e0b;"></div> Pending</span>';
                } elseif($status == 'confirmed') {
                    echo '<span class="badge badge-confirmed"><i data-lucide="check-circle" style="width:12px;"></i> Confirmed</span>';
                } elseif($status == 'rejected') {
                    echo '<span class="badge badge-rejected"><i data-lucide="x-circle" style="width:12px;"></i> Rejected</span>';
                } else {
                    echo '<span class="badge">'.ucfirst($status).'</span>';
                }
                ?>
            </td>

            <td>
                <div class="action-group">
                    <?php if($_SESSION['officer_role'] == 'petugas'): ?>
                        
                        <?php if($status == 'pending'): ?>
                            <a href="confirm.php?id=<?= $row['order_id'] ?>" onclick="return confirm('Confirm this transaction?')" class="btn-action confirm" title="Confirm">
                                <i data-lucide="check"></i>
                            </a>
                            <a href="reject.php?id=<?= $row['order_id'] ?>" onclick="return confirm('Reject this transaction?')" class="btn-action reject" title="Reject">
                                <i data-lucide="x"></i>
                            </a>
                        <?php elseif($status == 'confirmed'): ?>
                            <a href="javascript:void(0)" class="btn-action" style="background: rgba(16, 185, 129, 0.05); color: #10b981; cursor: default;">
                                <i data-lucide="check-circle"></i>
                            </a>
                        <?php elseif($status == 'rejected'): ?>
                            <a href="javascript:void(0)" class="btn-action" style="background: #f1f5f9; color: #94a3b8; cursor: default;">
                                <i data-lucide="minus"></i>
                            </a>
                        <?php endif; ?>

                        <div style="width:1px; height:20px; background:#e2e8f0; margin: 0 4px;"></div>
                        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this transaction record?')" class="btn-action delete" title="Delete">
                            <i data-lucide="trash-2"></i>
                        </a>

                    <?php endif; ?>

                    <?php if($_SESSION['officer_role'] == 'admin'): ?>
                        <span style="font-size: 13px; color: #94a3b8; font-weight: 500;"><i data-lucide="eye" style="width:14px; margin-bottom:-2px;"></i> View Only</span>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

<script>
    lucide.createIcons();

    function viewProof(image){
        if(!image) {
            alert("No proof uploaded.");
            return;
        }
        const modal = document.getElementById("proofModal");
        const img = document.getElementById("proofImage");

        img.src = "../../" + image;
        modal.style.display = "flex";
    }

    function closeProof(){
        document.getElementById("proofModal").style.display = "none";
        document.getElementById("proofImage").src = "";
    }

    window.onclick = function(event){
        const modal = document.getElementById("proofModal");
        if(event.target === modal) closeProof();
    }

    document.addEventListener("keydown", function(event){
        if(event.key === "Escape") closeProof();
    });
</script>

</body>
</html>
