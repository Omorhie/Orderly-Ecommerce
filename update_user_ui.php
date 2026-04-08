<?php
$files = [
    'user/dashboard.php',
];

foreach ($files as $file) {
    if(!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    $search = '<a href="chat.php"';
    $notifBtn = '<a href="notifications.php" class="icon-btn" title="Notifications" style="position:relative;">
                    <i data-lucide="bell" style="width: 20px;"></i>
                    <span class="user-notif-badge" style="position:absolute; top:-2px; right:-2px; background:var(--danger); color:white; font-size:10px; padding:2px 5px; border-radius:50%; display:none; font-weight:bold;">0</span>
                </a>';

    if(strpos($content, 'notifications.php') === false && strpos($content, $search) !== false) {
        $content = str_replace($search, $notifBtn . "\n                " . $search, $content);
    }
    
    // Add JS logic to fetch notifs
    $jsLogic = "
    <!-- NOTIF LOGIC USER -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('api_notif.php')
            .then(r => r.json())
            .then(data => {
                let badges = document.querySelectorAll('.user-notif-badge');
                badges.forEach(b => {
                    if(data.count > 0) {
                        b.style.display = 'flex';
                        b.textContent = data.count;
                    }
                });
            }).catch(e=>console.log(e));
        });
    </script>
</body>";

    if(strpos($content, '<!-- NOTIF LOGIC USER -->') === false){
        $content = str_replace('</body>', $jsLogic, $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
?>
