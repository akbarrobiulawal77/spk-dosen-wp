<?php
include 'koneksi_cloud.php';

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SPK Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-[#FDF8F5] text-[#1A202C] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-8 md:p-10 shadow-[8px_8px_0px_0px_#1A202C] w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🔐</div>
            <h1 class="text-3xl font-black text-[#1A202C] tracking-tight">Masuk Akun</h1>
            <p class="text-gray-500 font-bold mt-2">Masuk untuk mengelola data dosen</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-[#E53E3E] text-white font-bold p-3 rounded-xl mb-6 border-2 border-[#1A202C] shadow-[2px_2px_0px_0px_#1A202C] text-center">
                Username atau password salah!
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block font-black text-sm mb-2">Username</label>
                <input type="text" name="username" required autocomplete="off" class="w-full border-2 border-[#1A202C] rounded-xl p-3 font-bold focus:outline-none focus:ring-4 focus:ring-[#8BD3DD] transition">
            </div>

            <div>
                <label class="block font-black text-sm mb-2">Password</label>
                <input type="password" name="password" required class="w-full border-2 border-[#1A202C] rounded-xl p-3 font-bold focus:outline-none focus:ring-4 focus:ring-[#8BD3DD] transition">
            </div>

            <button type="submit" name="login" class="w-full bg-[#FAAE2B] text-black border-2 border-black font-black p-4 rounded-xl shadow-[4px_4px_0px_0px_#000] hover:translate-y-0.5 hover:shadow-[2px_2px_0px_0px_#000] transition-all text-lg cursor-pointer mt-4">
                Login Sekarang
            </button>
        </form>
    </div>

</body>
</html>