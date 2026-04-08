<?php
session_start();

if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../../auth/login.php");
    exit;
}

require_once "../../config/database.php";

if(!isset($_GET['id'])){
    die("ID tidak ditemukan");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT 
        transactions.*,
        users.username
    FROM transactions
    JOIN users ON transactions.user_id = users.id
    WHERE transactions.id = ?
");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if(!$data){
    die("Data tidak ditemukan");
}

$total = $data['price'] * ($data['qty'] ?? 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?= $id ?> | Orderly Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #1e293b;
            --secondary: #334155;
            --accent: #3b82f6;
            --bg-color: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            background-color: var(--primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .receipt-container {
            background: #ffffff;
            width: 100%;
            max-width: 450px;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            position: relative;
        }

        /* Decorative top accent */
        .receipt-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 8px;
            background: var(--accent);
            border-radius: 20px 20px 0 0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px dashed #e2e8f0;
            padding-bottom: 20px;
        }

        .header h2 {
            color: var(--primary);
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .header p {
            color: #64748b;
            font-size: 14px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            font-size: 15px;
        }

        .row .label { color: #64748b; font-weight: 500; }
        .row .value { color: var(--primary); font-weight: 600; text-align: right; }

        .total-row {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-row .label {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .total-row .value {
            font-size: 22px;
            font-weight: 700;
            color: var(--accent);
        }

        .actions {
            margin-top: 40px;
            display: flex;
            gap: 15px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s;
            border: none;
        }

        .btn-print {
            background: var(--primary);
            color: white;
        }

        .btn-print:hover { background: #0f172a; }

        .btn-back {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .btn-back:hover {
            background: #e2e8f0;
            color: var(--primary);
        }

        /* PRINT STYLES */
        @media print {
            body { background: white; padding: 0; }
            .receipt-container { 
                box-shadow: none; 
                max-width: 100%; 
                margin: 0; 
                padding: 20px;
            }
            .receipt-container::before { display: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="header">
        <h2><i data-lucide="check-circle" style="color: #10b981; width:28px; height:28px;"></i> Official Receipt</h2>
        <p>Orderly E-Commerce System</p>
    </div>

    <div class="row">
        <span class="label">Transaction ID</span>
        <span class="value">#TXN-<?= str_pad($data['id'], 4, '0', STR_PAD_LEFT) ?></span>
    </div>
    
    <div class="row">
        <span class="label">Date</span>
        <span class="value"><?= date('d F Y, H:i', strtotime($data['created_at'])) ?></span>
    </div>

    <div class="row">
        <span class="label">Customer</span>
        <span class="value"><?= htmlspecialchars($data['username']) ?></span>
    </div>

    <div class="row" style="margin-top: 30px;">
        <span class="label">Product Item</span>
        <span class="value" style="color: var(--accent);"><?= htmlspecialchars($data['product_name']) ?></span>
    </div>

    <div class="row">
        <span class="label">Qty</span>
        <span class="value"><?= $data['qty'] ?? 1 ?>x</span>
    </div>

    <div class="row">
        <span class="label">Unit Price</span>
        <span class="value">Rp <?= number_format($data['price']) ?></span>
    </div>

    <div class="total-row">
        <span class="label">Grand Total</span>
        <span class="value">Rp <?= number_format($total) ?></span>
    </div>

    <div class="actions">
        <a href="index.php" class="btn btn-back"><i data-lucide="arrow-left" style="width:18px;"></i> Back</a>
        <button onclick="window.print()" class="btn btn-print"><i data-lucide="printer" style="width:18px;"></i> Print Receipt</button>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>