<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['btn_login'])) {
    
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password_input = $_POST['password']; 

   
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($query) === 1) {
        $data = mysqli_fetch_assoc($query);
        
        
        if (password_verify($password_input, $data['password'])) {
            
           
            $_SESSION['status'] = 'login';
            $_SESSION['id_user'] = $data['id'];
            $_SESSION['nama'] = $data['nama_lengkap'];
            $_SESSION['role'] = $data['role']; 

        
    if ($data['role'] == 'admin') {
    header("Location: admin/admin.php");
} else if ($data['role'] == 'kasir') {
    header("Location: kasir/index.php");
} else if ($data['role'] == 'owner') {
    header("Location: owner/indexowner.php");
}
            
        } else {
            $error_msg = "Password salah!";
        }
    } else {
        $error_msg = "Username tidak ditemukan!";
    }
}

?>

<html lang="en" class="bg-white dark:bg-gray-950 scheme-light dark:scheme-dark js-focus-visible" data-js-focus-visible=""><head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

 <link rel="stylesheet" href="assets/style.css">

  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

 
<title inertia="">Login - Tailwind Plus</title>
<style>
    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: #06B6D4;

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px #06B6D4, 0 0 5px #06B6D4;
      opacity: 1.0;

      transform: rotate(3deg) translate(0px, -4px);
    }

    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: #06B6D4;
      border-left-color: #06B6D4;
      border-radius: 50%;

      animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
</style>
</head>

<body class="font-sans text-gray-950 dark:text-white antialiased [overflow-anchor:none]" >
  <div >
    <div class="grid min-h-dvh grid-cols-[1fr_2.5rem_minmax(0,var(--container-lg))_2.5rem_1fr]  grid-rows-[1fr_auto_1fr] overflow-clip ">
    <div class="col-start-2 row-span-full row-start-1 max-sm:hidden border-x border-x-(--grid-line-color) bg-size-[10px_10px] bg-fixed bg-black bg-[repeating-linear-gradient(315deg,var(--grid-line-color)_0,var(--grid-line-color)_1px,transparent_0,transparent_50%)]">

    </div>
<div class="col-start-4 row-span-full row-start-1 max-sm:hidden border-x border-x-black bg-size-[10px_10px] bg-fixed bg-[repeating-linear-gradient(315deg,black_0,black_1px,transparent_0,transparent_50%)]">

    </div>
    <main class="grid grid-cols-1 max-sm:col-span-full max-sm:col-start-1 max-sm:row-span-full max-sm:bg-gray-950/5 max-sm:p-2 dark:max-sm:bg-white/5 sm:line-y sm:col-start-3 sm:row-start-2 sm:-mx-px sm:p-[calc(0.5rem+1px)]">
        <div class="grid grid-cols-1 items-center rounded-xl bg-white max-sm:p-6 sm:p-10 dark:bg-gray-950">
            <div class="grid grid-cols-1 gap-10">
                <div class="flex items-start">
               <h1 class="font-high font-bold text-3xl">Login</h1>
            </div>
            <div>

                <form action="" method="POST">
                    <div class="flex flex-col gap-2">
                        <label for="username" class="block text-sm/6 font-medium">Username</label>
                        <input type="text" name="username" id="username" class="block h-10 w-full appearance-none rounded-lg bg-white px-3 sm:text-sm dark:bg-white/5 outline -outline-offset-1 outline-gray-950/15 dark:outline-white/25 focus:outline-gray-950 dark:focus:outline-white data-error:outline-rose-500 dark:data-error:outline-rose-400" required="" tabindex="1" value="">
                    </div>
                    <div class="relative mt-6">
                        <div class="flex flex-col gap-2">
                            <label for="password" class="block text-sm/6 font-medium">Password</label>
                            <input type="password" name="password" id="password" class="block h-10 w-full appearance-none rounded-lg bg-white px-3 sm:text-sm dark:bg-white/5 outline -outline-offset-1 outline-gray-950/15 dark:outline-white/25 focus:outline-gray-950 dark:focus:outline-white data-error:outline-rose-500 dark:data-error:outline-rose-400" required="" tabindex="1" value="">
                        </div>
                      
                    </div>
                    <button type="submit" name="btn_login" class="mt-10 w-full inline-flex justify-center rounded-full text-sm/6 font-semibold focus-visible:outline-2 focus-visible:outline-offset-2 bg-gray-950 text-white hover:bg-gray-800 focus-visible:outline-gray-950 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white dark:hover:bg-gray-200 dark:focus-visible:outline-white dark:focus-visible:outline-white px-4 py-2" tabindex="3">Login</button>
                    <p class="mt-6 text-sm/6">
                       </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</main>
</div>
</div>


</body>
</html>