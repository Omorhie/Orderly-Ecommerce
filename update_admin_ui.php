<?php
$admin_files = [
    'admin/dashboard.php',
    'admin/product/index.php',
    'admin/transaksi/index.php',
    'admin/transaksi/refund_control.php',
    'admin/laporan/index.php',
    'admin/user/index.php',
    'admin/backup/backup.php',
    'admin/chat_admin.php'
];

foreach ($admin_files as $file) {
    if(!file_exists($file)) { echo "Not found: $file\n"; continue; }
    $content = file_get_contents($file);
    
    // Add Notification menu
    if(preg_match('/<li><a href="([^"]*chat_admin\.php)".*?<\/a><\/li>/ism', $content, $matches)){
        $supportLine = $matches[0];
        $chatPath = $matches[1];
        $baseDepth = dirname($chatPath);
        if($baseDepth == '.') $baseDepth = ''; else $baseDepth .= '/';
        $notifPath = str_replace('chat_admin.php', 'notifications.php', $chatPath);
        
        $notifLine = "<li><a href=\"$notifPath\"><i data-lucide=\"bell\"></i> Notifications <span class=\"notif-badge\" style=\"background:#ef4444; color:white; font-size:11px; padding:2px 6px; border-radius:10px; margin-left:auto; display:none;\">0</span></a></li>";
        
        if(strpos($content, 'notifications.php') === false){
            $content = str_replace($supportLine, $notifLine . "\n            " . $supportLine, $content);
        }
    }

    // Add JS logic to fetch notifs
    $jsLogic = "
    <!-- NOTIF LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{$baseDepth}api_notif.php')
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
</body>";

    if(strpos($content, '<!-- NOTIF LOGIC -->') === false){
        $content = str_replace('</body>', $jsLogic, $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
?>
