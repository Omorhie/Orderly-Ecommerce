<?php
session_start();
if ($_SESSION['officer_role'] != 'admin') {
    die("Akses ditolak");
}

require_once "../../config/database.php";
$result = $conn->query("SELECT * FROM officer ORDER BY id DESC");
$role = $_SESSION['officer_role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Officer Management - Orderly Admin</title>
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
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: var(--sidebar-bg); padding: 20px 30px; border-radius: 20px; box-shadow: var(--card-shadow); }
        .header h2 { font-size: 22px; font-weight: 600; color: var(--primary); }

        .btn-add { background: var(--accent); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2); }
        .btn-add:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3); }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 20px; overflow: hidden; box-shadow: var(--card-shadow); }
        th, td { padding: 16px 20px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: rgba(248, 250, 252, 0.8); color: var(--text-gray); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { color: var(--text-dark); font-size: 14.5px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        .badge-role { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-admin { background: rgba(59, 130, 246, 0.1); color: var(--accent); }
        .badge-petugas { background: rgba(16, 185, 129, 0.1); color: #10b981; }

        /* DROPDOWN ACTIONS */
        .dropdown { position: relative; display: inline-block; }
        .dot-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: var(--text-gray); width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: var(--transition); }
        .dot-btn:hover { background: #f1f5f9; color: var(--primary); }
        .dropdown-content { display: none; position: absolute; right: 0; top: 100%; background: white; min-width: 150px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden; z-index: 50; border: 1px solid #e2e8f0; }
        .dropdown:hover .dropdown-content, .dropdown:focus-within .dropdown-content { display: block; animation: dropFade 0.2s ease; }
        @keyframes dropFade { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-item { width: 100%; padding: 12px 16px; border: none; background: none; text-align: left; cursor: pointer; display: flex; align-items: center; gap: 10px; color: var(--text-dark); font-size: 14px; font-weight: 500; text-decoration: none; transition: var(--transition); }
        .dropdown-item i { width: 16px; height: 16px; }
        .dropdown-item:hover { background: #f1f5f9; color: var(--accent); }
        .dropdown-item.delete:hover { color: #ef4444; background: #fef2f2; }

        /* MODAL */
        .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; }
        .modal-content { background: var(--sidebar-bg); width: 100%; max-width: 500px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); display: flex; flex-direction: column; animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        
        .modal-header { padding: 24px 30px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
        .modal-header h3 { font-size: 18px; font-weight: 600; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 10px; }
        .close-btn { background: none; border: none; color: var(--text-gray); cursor: pointer; transition: var(--transition); }
        .close-btn:hover { color: #ef4444; transform: rotate(90deg); }

        .modal-body { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: var(--text-gray); margin-bottom: 8px; }
        .form-control { width: 100%; padding: 14px 16px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 15px; color: var(--text-dark); background: var(--bg-color); transition: var(--transition); outline: none; }
        .form-control:focus { border-color: var(--accent); background: #ffffff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        select.form-control { padding-right: 40px; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; background-size: 16px; }

        .modal-footer { padding: 20px 30px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 15px; background: var(--bg-color); }
        .btn-cancel { background: white; color: var(--text-gray); border: 1px solid #e2e8f0; padding: 12px 24px; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; transition: var(--transition); }
        .btn-cancel:hover { background: #f1f5f9; color: var(--text-dark); }
        .btn-save { background: var(--accent); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2); }
        .btn-save:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3); }

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
            <li><a href="index.php" class="active"><i data-lucide="users"></i> Officers</a></li>
            <li><a href="../backup/backup.php"><i data-lucide="database"></i> Backup</a></li>
        <?php } ?>
        <li><a href="../notifications.php"><i data-lucide="bell"></i> Notifications <span class="notif-badge" style="background:#ef4444; color:white; font-size:11px; padding:2px 6px; border-radius:10px; margin-left:auto; display:none;">0</span></a></li>
            <li><a href="../chat_admin.php"><i data-lucide="message-square"></i> Support</a></li>
        <li><a href="../../auth/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
    </ul>
</div>

<!-- CONTENT -->
<div class="content">

    <div class="header">
        <h2>Officer Management</h2>
        <button class="btn-add" onclick="openModal('userModal')">
            <i data-lucide="user-plus"></i> Add New Officer
        </button>
    </div>

    <table>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Password</th>
            <th>Contact Number</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td style="font-weight: 600; color: var(--primary);"><?= htmlspecialchars($row['username']) ?></td>
            <td style="color: var(--text-gray);"><i data-lucide="mail" style="width:14px; margin-bottom:-2px;"></i> <?= htmlspecialchars($row['email']) ?></td>
            <td style="color: #cbd5e1;">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</td>
            <td><?= htmlspecialchars($row['no_hp']) ?></td>
            <td>
                <?php if($row['role'] == 'admin'): ?>
                    <span class="badge-role badge-admin">Admin</span>
                <?php else: ?>
                    <span class="badge-role badge-petugas">Petugas</span>
                <?php endif; ?>
            </td>
            <td>
                <div class="dropdown">
                    <button class="dot-btn"><i data-lucide="more-vertical"></i></button>
                    <div class="dropdown-content">
                        <button type="button" class="dropdown-item"
                                onclick="openEditModal(
                                    '<?= $row['id'] ?>',
                                    '<?= htmlspecialchars($row['username']) ?>',
                                    '<?= htmlspecialchars($row['email']) ?>',
                                    '<?= htmlspecialchars($row['no_hp']) ?>',
                                    '<?= $row['role'] ?>'
                                )">
                            <i data-lucide="edit-2"></i> Edit Record
                        </button>
                        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Remove this officer account?')" class="dropdown-item delete">
                            <i data-lucide="user-minus"></i> Remove
                        </a>
                    </div>
                </div>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

<!-- CREATE MODAL -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i data-lucide="user-plus"></i> Create Officer</h3>
            <button class="close-btn" onclick="closeModal('userModal')"><i data-lucide="x"></i></button>
        </div>
        <form action="store.php" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Identifier name">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="officer@orderly.com">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="no_hp" class="form-control" required placeholder="08xxxxxxxxxx">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
                    </div>
                    <div class="form-group">
                        <label>Access Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">Select Role...</option>
                            <option value="admin">Administrator</option>
                            <option value="petugas">Petugas Server</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('userModal')">Cancel</button>
                <button type="submit" class="btn-save"><i data-lucide="check"></i> Create Account</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i data-lucide="edit"></i> Update Officer</h3>
            <button class="close-btn" onclick="closeModal('editModal')"><i data-lucide="x"></i></button>
        </div>
        <form action="update.php" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="edit_username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="no_hp" id="edit_nohp" class="form-control" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>New Password <span style="color: #94a3b8; font-weight: normal;">(Optional)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Leave empty to keep current">
                    </div>
                    <div class="form-group">
                        <label>Access Role</label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <option value="admin">Administrator</option>
                            <option value="petugas">Petugas Server</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn-save"><i data-lucide="save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();

    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }

    function openEditModal(id, username, email, nohp, role) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_username").value = username;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_nohp").value = nohp;
        document.getElementById("edit_role").value = role;
        openModal('editModal');
    }
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
