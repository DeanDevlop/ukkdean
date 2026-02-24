<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
       
        /* MASIH EROR SEMUA CODE DI FILE INI*/
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-white font-sans h-screen flex overflow-hidden">

    <main class="w-2/3 flex flex-col h-full border-r border-gray-100">
        
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-white z-10">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Belanja</h1>
                <p class="text-gray-400 text-sm mt-1">Silakan cek pesanan Anda</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tanggal</p>
                <p class="text-lg font-bold text-gray-700"><?= date('d F Y') ?></p>
            </div>
        </div>

        <div id="cart-container" class="flex-1 overflow-y-auto p-8 no-scrollbar">
            <div class="flex items-center justify-center h-full text-gray-400">
                Memuat data...
            </div>
        </div>

    </main>

    <aside class="w-1/3 bg-gradient-to-br from-blue-700 to-blue-900 text-white flex flex-col justify-center items-center shadow-2xl relative overflow-hidden">
        
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>

        <div class="text-center z-10 px-8 w-full">
            <p class="text-blue-200 text-sm font-bold mb-2 uppercase tracking-[0.2em]">Total Tagihan</p>
            
            <h1 id="display-total" class="text-6xl font-black tracking-tight drop-shadow-lg mb-8">
                Rp 0
            </h1>
            
            <div class="border-t border-white/20 w-1/2 mx-auto mb-8"></div>

            <div class="opacity-80 text-sm font-light">
                Terima Kasih telah berbelanja di<br>
                <span class="font-bold text-white text-lg mt-1 block">TOKO DEAN</span>
            </div>
        </div>
    </aside>

<script>
        function updateCart() {
            
            let uniqueUrl = 'logic/fetch_keranjang_public.php?t=' + new Date().getTime();

            fetch(uniqueUrl)
                .then(response => response.text())
                .then(data => {
                    const container = document.getElementById('cart-container');
                    
                    
                    if (container.innerHTML.trim() !== data.trim()) {
                        container.innerHTML = data;
                    }

                    
                    let serverTotal = document.getElementById('server_total');
                    if (serverTotal) {
                        document.getElementById('display-total').innerText = serverTotal.value;
                    } else {
                        document.getElementById('display-total').innerText = "Rp 0";
                    }
                })
                .catch(error => console.error('Koneksi terputus:', error));
        }

        
        setInterval(updateCart, 1000);
        updateCart(); 
    </script>

</body>
</html>