<?php

if (!isset($pageTitle)) {
    $pageTitle = "Aplikasi Kasir";
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$nama_tampil = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guest';


$role_tampil = isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Petugas';


$huruf_avatar = strtoupper(substr($nama_tampil, 0, 1));
?>

<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0 z-10">
    
    <div class="flex items-center flex-1">
        <h2 class="text-xl font-bold text-gray-700 mr-8 tracking-tight">
            <?= $pageTitle ?>
        </h2>
    </div>

    <div class="flex items-center space-x-4">
        
        <div id="realtime-clock" class="text-sm font-bold text-gray-500 hidden sm:block bg-gray-50 px-3 py-1 rounded-md border border-gray-200">
            00:00:00
        </div>

    

        <div class="h-6 w-px bg-gray-300 mx-2"></div>

        <div class="flex items-center cursor-pointer">
            <div class="text-right mr-3 hidden sm:block">
                <div class="text-sm font-bold text-gray-800"><?= $nama_tampil ?></div>
                <div class="text-xs text-gray-500"><?= $role_tampil ?></div>
            </div>
            
            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow-sm border-2 border-white ring-2 ring-gray-100">
                <?= $huruf_avatar ?>
            </div>
        </div>
    </div>
</header>

<script>
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        document.getElementById('realtime-clock').innerText = now.toLocaleTimeString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock(); 
</script>