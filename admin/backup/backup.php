<?php
date_default_timezone_set('Asia/Jakarta');

session_start();
if ($_SESSION['officer_role'] != 'admin') {
    die("Akses ditolak");
}

$backupDir = "backups/";

// ambil semua file backup
$files = glob($backupDir . "*.sql");
?>

<?php
$role = $_SESSION['officer_role'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Backup Database</title>

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

/* CONTENT WRAPPER */
.content{
    flex:1;
    padding:40px;
}

.backup-wrapper{
    width:100%;
    max-width:1000px;
    margin:auto;
}

/* HEADER */
.backup-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    color:white;
    padding-top: 20px;
    font-size: 25px;
}

.backup-header h2 span{
    color:#526D82;
}

.backup-header h2 {
        font-weight: 540;

}

/* BUTTON BACKUP */
.btn-backup{
    background:#d9d9d9;
    border:none;
    padding:15px 12px;
    border-radius:16px;
    cursor:pointer;
    font-weight:bold;
    height: 55px;
    font-size: 18px;
    text-align: center;
    width: 220px;
    display: inline-block;
    transition: all 300ms;
    color: #526D82;
}

.btn-backup:hover{
    background-color: #526D82;
    color: #D9D9D9;
    width: 250px;
}

/* TABLE BOX */
.table-box{
    background:#d9d9d9;
    border-radius:16px;
    overflow:hidden;
}

/* TABLE */
table{
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

th,td{
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

/* ICON BUTTON */
.actions{
    display:flex;
    justify-content:center;
    gap:15px;
}

.icon{
    text-decoration:none;
    font-size:20px;
}

.download{ color:#22c55e; }
.delete{ color:#ef4444; }

/* RESTORE SECTION */
.restore-wrapper{
    margin-top:60px;
    display:flex;
    justify-content:right;
}

.restore-form{
    display:flex;
    gap:30px;
    align-items:center;
}

/* OUTER BOX (abu besar) */
.file-upload{
    background:#cfcfcf;
    padding:15px;
    border-radius:16px;
    display:flex;          /* ubah dari inline-block */
    align-items:center;    /* center vertikal */
    justify-content:flex-start; /* posisi kiri */
    width:300px;
    height:55px;
    cursor:pointer;
}

/* sembunyikan input asli */
.file-upload input{
    display:none;
}

/* tombol capsule dalam */
.upload-wrapper{
    background:#2e3f55;
    color:#9fb3c8; 
    height:30px;
    border-radius:999px;
    font-weight:500;

    display:inline-flex;     /* auto width sesuai isi */
    align-items:center;
    justify-content:center;

    padding:0 16px;          /* ruang kiri kanan */
    width:fit-content;       /* ikut panjang text */
    max-width:240px;         /* biar ga terlalu panjang */
    white-space:nowrap;      /* text tidak turun */
    overflow:hidden;
    text-overflow:ellipsis;  /* kalau kepanjangan jadi ... */

    transition:0.3s;
}

/* hover effect */
.file-upload:hover .upload-wrapper{
    background:#27374D;
    color:white;
}

/* RESTORE BUTTON */
.btn-restore{
  background:#d9d9d9;
    border:none;
    padding:15px 12px;
    border-radius:16px;
    cursor:pointer;
    font-weight:bold;
    height: 55px;
    font-size: 18px;
    text-align: center;
    width: 220px;
    display: inline-block;
    transition: all 300ms;
    color: #526D82;
}

.btn-restore:hover{
    background-color: #526D82;
    color: #D9D9D9;
    width: 250px;
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

    <li><a href="../transaksi/index.php">Transactions</a></li>
    <li><a href="../laporan/index.php" >Report</a></li>
        <?php if($role === 'admin') { ?>
        <li><a href="../user/index.php">Officer Management</a></li>
        <li><a href="../backup/backup.php" class="active">Backup/Restore</a></li>
    <?php } ?>
    <li><a href="../../auth/admin/logout.php">Logout</a></li>
</ul>

    </div>

<div class="content">

<div class="backup-wrapper">

<!-- HEADER -->
<div class="backup-header">
    <h2>Restore/<span>Backup</span></h2>

    <form method="post" action="create_backup.php">
        <button class="btn-backup">
        Back Up Now
        </button>
    </form>
</div>


<!-- TABLE -->
<div class="table-box">
<table>
<tr>
<th>Backup File</th>
<th>Date</th>
<th>Size</th>
<th>Actions</th>
</tr>

<?php if($files): ?>
<?php foreach($files as $file): ?>
<tr>
<td><?= basename($file) ?></td>
<td><?= date("d M Y H:i", filemtime($file)) ?></td>
<td><?= round(filesize($file)/1024,2) ?> KB</td>

<td class="actions">
<a href="download.php?file=<?= basename($file) ?>" class="icon download">⬇</a>
<a href="delete_backup.php?file=<?= basename($file) ?>" class="icon delete">🗑</a>
</td>

</tr>
<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="4">No backup file</td></tr>
<?php endif; ?>

</table>
</div>


<!-- RESTORE -->
<div class="restore-wrapper">

<form action="restore.php" method="post" enctype="multipart/form-data" class="restore-form">

<label class="file-upload">
    <input type="file" name="backup_file" id="fileInput" required>
    <div class="upload-wrapper">
        <span id="fileText">Choose File</span>
    </div>
</label>

<button class="btn-restore">
Restore Now
</button>

</form>

</div>

</div>
</div>

</body>

<script>
document.getElementById("fileInput").addEventListener("change", function(){
    let fileName = this.files[0]?.name || "Choose File";

    // batasi panjang text
    if(fileName.length > 25){
        fileName = fileName.substring(0,25) + "...";
    }

    document.getElementById("fileText").textContent = fileName;
});
</script>
</html>