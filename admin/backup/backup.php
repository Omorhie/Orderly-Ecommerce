<?php
date_default_timezone_set('Asia/Jakarta');

session_start();
if ($_SESSION['officer_role'] != 'admin') {
    die("Akses ditolak");
}

$backupDir = "backups/";

// ambil semua file backup
$files = glob($backupDir . "*.sql");
$role = $_SESSION['officer_role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Backup - Orderly Admin</title>
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

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; background-color: var(--bg-color); color: var(--text-dark); }

        /* SIDEBAR */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); padding: 30px 20px; box-shadow: 2px 0 10px rgba(0,0,0,0.03); display: flex; flex-direction: column; z-index: 10; }
        .sidebar h2 { text-align: center; font-size: 26px; font-weight: 700; color: var(--primary); margin-bottom: 40px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .sidebar h2 span { color: var(--accent); }
        .sidebar ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .sidebar ul li a { text-decoration: none; color: var(--text-gray); display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; font-size: 15px; font-weight: 500; transition: var(--transition); }
        .sidebar ul li a i { width: 20px; height: 20px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: var(--primary); color: #ffffff; transform: translateX(5px); box-shadow: 0 10px 15px -3px rgba(30, 41, 59, 0.2); }
        .sidebar ul li:last-child { margin-top: auto; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .sidebar ul li:last-child a:hover { background-color: #ef4444; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3); }

        /* CONTENT */
        .content { flex: 1; padding: 40px; overflow-y: auto; }
        
        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: var(--sidebar-bg); padding: 20px 30px; border-radius: 20px; box-shadow: var(--card-shadow); }
        .header h2 { font-size: 22px; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 10px;}

        .btn-backup { background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(30, 41, 59, 0.2); }
        .btn-backup:hover { background: #0f172a; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(30, 41, 59, 0.3); }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 20px; overflow: hidden; box-shadow: var(--card-shadow); }
        th, td { padding: 16px 20px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: rgba(248, 250, 252, 0.8); color: var(--text-gray); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { color: var(--text-dark); font-size: 14.5px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        .filename { font-family: monospace; color: var(--accent); font-weight: 600; background: rgba(59, 130, 246, 0.1); padding: 4px 8px; border-radius: 6px; }

        /* Action Buttons */
        .action-group { display: flex; align-items: center; gap: 8px; }
        .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; text-decoration: none; transition: var(--transition); }
        .btn-action i { width: 18px; height: 18px; }
        .btn-action.download { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .btn-action.download:hover { background: #10b981; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
        .btn-action.delete { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .btn-action.delete:hover { background: #ef4444; color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }

        /* RESTORE SIDE PANEL */
        .panel {
            background: var(--sidebar-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            height: fit-content;
        }

        .panel-header {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            background: #f8fafc;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .upload-area:hover, .upload-area.dragover {
            border-color: var(--accent);
            background: rgba(59, 130, 246, 0.05);
        }

        .upload-area i { color: var(--text-gray); transition: var(--transition); }
        .upload-area:hover i { color: var(--accent); transform: translateY(-5px); }

        .file-name-display {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
            margin-top: 10px;
            background: rgba(59, 130, 246, 0.1);
            padding: 4px 12px;
            border-radius: 20px;
            display: none;
        }

        .btn-restore { background: var(--text-gray); color: white; border: none; padding: 14px; width: 100%; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: not-allowed; transition: var(--transition); display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-restore.active { background: #f59e0b; cursor: pointer; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2); }
        .btn-restore.active:hover { background: #d97706; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(245, 158, 11, 0.3); }

    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2><i data-lucide="shield-check"></i> Order<span>ly</span></h2>
    <ul>
        <li><a href="../dashboard.php"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
        <li><a href="../product/index.php"><i data-lucide="package"></i> Products</a></li>
        <li><a href="../transaksi/index.php"><i data-lucide="shopping-cart"></i> Transactions</a></li>
        <li><a href="../laporan/index.php"><i data-lucide="file-bar-chart"></i> Reports</a></li>
        <?php if($role === 'admin') { ?>
            <li><a href="../user/index.php"><i data-lucide="users"></i> Officers</a></li>
            <li><a href="backup.php" class="active"><i data-lucide="database"></i> Backup</a></li>
        <?php } ?>
        <li><a href="../notifications.php"><i data-lucide="bell"></i> Notifications <span class="notif-badge" style="background:#ef4444; color:white; font-size:11px; padding:2px 6px; border-radius:10px; margin-left:auto; display:none;">0</span></a></li>
            <li><a href="../chat_admin.php"><i data-lucide="message-square"></i> Support</a></li>
        <li><a href="../../auth/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
    </ul>
</div>

<div class="content">

    <div class="grid-layout">
        
        <!-- LEFT: BACKUP LIST -->
        <div>
            <div class="header">
                <h2><i data-lucide="server"></i> Database Archives</h2>
                <form method="post" action="create_backup.php">
                    <button type="submit" class="btn-backup">
                        <i data-lucide="database-backup"></i> Create Backup Now
                    </button>
                </form>
            </div>

            <table>
                <tr>
                    <th>SQL File</th>
                    <th>Creation Date</th>
                    <th>File Size</th>
                    <th>Options</th>
                </tr>

                <?php if($files): ?>
                    <?php foreach($files as $file): ?>
                    <tr>
                        <td><span class="filename"><?= basename($file) ?></span></td>
                        <td><?= date("d M Y H:i", filemtime($file)) ?></td>
                        <td><div style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; display: inline-block; font-size:12px; font-weight:600; color:var(--text-gray);"><?= round(filesize($file)/1024, 2) ?> KB</div></td>
                        <td>
                            <div class="action-group">
                                <a href="download.php?file=<?= urlencode(basename($file)) ?>" class="btn-action download" title="Download SQL">
                                    <i data-lucide="download"></i>
                                </a>
                                <a href="delete_backup.php?file=<?= urlencode(basename($file)) ?>" onclick="return confirm('Erase this backup permanently?')" class="btn-action delete" title="Delete Archive">
                                    <i data-lucide="trash-2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding: 40px; color: var(--text-gray);">No SQL backups recorded yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- RIGHT: RESTORE PANEL -->
        <div class="panel">
            <div class="panel-header">
                <i data-lucide="upload-cloud"></i> System Restore
            </div>
            
            <form action="restore.php" method="post" enctype="multipart/form-data">
                <label class="upload-area">
                    <i data-lucide="file-sql" style="width: 48px; height: 48px;"></i>
                    <p style="color: var(--primary); font-weight: 500; font-size: 15px;">Click to select SQL file</p>
                    <span style="color: #94a3b8; font-size: 12px;">Valid .sql backup file required</span>
                    <input type="file" name="backup_file" id="fileInput" hidden required accept=".sql">
                    <div id="fileText" class="file-name-display"></div>
                </label>

                <button type="submit" class="btn-restore" id="restoreBtn" disabled>
                    <i data-lucide="refresh-cw"></i> Execute Restore
                </button>
                <p style="font-size: 12px; color: var(--text-gray); text-align: center; margin-top: 15px;">
                    Warning: Restoring will overwrite existing data. Proceed with caution.
                </p>
            </form>
        </div>

    </div>

</div>

<script>
    lucide.createIcons();

    const fileInput = document.getElementById("fileInput");
    const fileText = document.getElementById("fileText");
    const restoreBtn = document.getElementById("restoreBtn");

    fileInput.addEventListener("change", function(){
        if(this.files && this.files[0]) {
            let fileName = this.files[0].name;
            if(fileName.length > 25) fileName = fileName.substring(0,25) + "...";
            fileText.textContent = fileName;
            fileText.style.display = 'inline-block';
            
            restoreBtn.classList.add('active');
            restoreBtn.disabled = false;
        } else {
            fileText.style.display = 'none';
            restoreBtn.classList.remove('active');
            restoreBtn.disabled = true;
        }
    });

    // Basic Drag and Drop styling
    const uploadArea = document.querySelector('.upload-area');
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    function preventDefaults (e) { e.preventDefault(); e.stopPropagation(); }
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => uploadArea.classList.add('dragover'), false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('dragover'), false);
    });
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