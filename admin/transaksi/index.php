<?php
session_start();
if (!isset($_SESSION['officer_role']) || $_SESSION['officer_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../../config/database.php";

$result = $conn->query("
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

            .btn {
                padding: 17px 12px;
                text-decoration: none;
                border-radius: 16px;
                font-size: 18px;
                display: inline-block;
                width: 220px;
                text-align: center;
                font-weight: bold;
                height: 55px;
            }

            .btn-add {
                background: #D9D9D9;
                color: #526D82;
                transition: all 300ms;
            }

            .btn-add:hover{
                background-color: #526D82;
                color: #D9D9D9;
                width: 250px;
            }
            .btn-edit {
                background: #2563eb;
                color: white;
            }

            .btn-delete {
                background: none;
                color: #B61E1E;
                text-decoration: none;
            } 

            table {
                width: 100%;
                border-collapse: collapse;
                background: white;
                border-radius: 16px;
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

            img {
                width: 60px;
                height: 60px;
                object-fit: cover;
                border-radius: 5px;
            }

            .actions a {
                margin-right: 5px;
            }

    .btn-view{
    background:none;
    color:#77D42F;
    border:none;
    padding:8px 12px;
    border-radius:8px;
    cursor:pointer;
    font-size: 16px;
    font-weight: bold;

}


/* MODAL BACKGROUND */
.modal {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
}

/* MODAL BOX */
.modal-content {
        background: #27374D;
        width: 1000px;
        margin: 3% auto;
        padding: 0px 0px 30px 0px;
        border-radius: 16px;
        position: relative;
        animation: fadeIn 0.3s ease;
}

/* TITLE */
.modal-title {
    color: white;
    text-align: center;
    margin-bottom: 20px;
    background: #fff;
}

/* IMAGE CONTAINER */
.modal-body {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* IMAGE */
#proofImage {
    width: 100%;
    height: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 12px;
}

/* CLOSE BUTTON */
.close {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 30px;
    cursor: pointer;
    color: #9DB2BF;
    transition: 0.2s;
}

.close:hover {
    color: white;
}

    .modal-navbar {
        background: #D9D9D9;
        color: white;
        padding: 15px;
        border-radius: 16px 16px 0px 0px;
        width: 100%;
        margin-bottom: 50px;
        height: 60px;
    }

    .modal-navbar h3 {
        margin: 0;
        color: #526D82;
        padding-top: 2px;
        padding-left: 15px;
        font-size: 25px;
    }

/* ANIMATION */
@keyframes fadeIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

</style>

</head>
<body>

<div id="proofModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeProof()">&times;</span>

        <div class="modal-navbar">
        <h3>Proof Of Payment</h3>
        </div>

        <div class="modal-body">
            <img id="proofImage">
        </div>
    </div>
</div>


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
    <li><a href="../transaksi/index.php" class="active">Transactions</a></li>
    <li><a href="../laporan/index.php">Report</a></li>
    <li><a href="../backup/backup.php">Backup/Restore</a></li>
    <li><a href="../../auth/admin/logout.php">Logout</a></li>
</ul>

    </div>

    <div class="content">

        <div class="header">
            <div class="header">
             <h2>Transaction <span>Management</span></h2>
            </div>

            <br>

        </div>

<table>
<tr>
    <th>Customer</th>
    <th>Product Name</th>
    <th>Method</th>
    <th>Price</th>
    <th>Proof Of Payment</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>

<td><?= htmlspecialchars($row['username']) ?></td>
<td><?= htmlspecialchars($row['product_name']) ?></td>
<td><?= htmlspecialchars($row['method']) ?></td>
<td>Rp <?= number_format($row['price']) ?></td>

<td>
<?php if($row['method'] == 'Transfer' && !empty($row['proof_payment'])): ?>
    <button class="btn-view"
    onclick="viewProof('<?= $row['proof_payment'] ?>')">
    View
    </button>

<?php else: ?>
    -
<?php endif; ?>
</td>

<td>
<a href="delete.php?id=<?= $row['id'] ?>"
onclick="return confirm('Hapus transaksi?')"
class="btn-delete">
Delete
</a>
</td>

</tr>
<?php } ?>
</table>


    </div>

    </body>

<script>
function viewProof(image){
    if(!image){
        alert("No proof uploaded.");
        return;
    }

    const modal = document.getElementById("proofModal");
    const img = document.getElementById("proofImage");

    img.src = "../../" + image;
    modal.style.display = "flex";
}

function closeProof(){
    const modal = document.getElementById("proofModal");
    const img = document.getElementById("proofImage");

    modal.style.display = "none";
    img.src = "";
}

/* Close when click outside */
window.onclick = function(event){
    const modal = document.getElementById("proofModal");
    if(event.target === modal){
        closeProof();
    }
}

/* Close when press ESC */
document.addEventListener("keydown", function(event){
    if(event.key === "Escape"){
        closeProof();
    }
});
</script>


    </html>