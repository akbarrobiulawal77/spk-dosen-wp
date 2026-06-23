<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-white border-b-4 border-[#F7D7B3] rounded-3xl p-4 mb-8 shadow-sm">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="text-2xl">🏆</span>
            <span class="font-extrabold text-xl text-[#1A202C] tracking-tight">SPK DOSEN BEST</span>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex flex-wrap items-center justify-center gap-2 bg-[#FDF8F5] p-1.5 rounded-2xl border border-[#F7D7B3]">
                <a href="index.php" class="px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 <?php echo ($current_page == 'index.php') ? 'bg-[#FE98A3] text-[#1A202C] shadow-sm' : 'text-[#1A202C] hover:bg-[#8BD3DD]'; ?>">
                    🏠 Home
                </a>
                <a href="tambah.php" class="px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 <?php echo ($current_page == 'tambah.php') ? 'bg-[#FE98A3] text-[#1A202C] shadow-sm' : 'text-[#1A202C] hover:bg-[#8BD3DD]'; ?>">
                    📝 Input
                </a>
                <a href="detail.php" class="px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 <?php echo ($current_page == 'detail.php') ? 'bg-[#FE98A3] text-[#1A202C] shadow-sm' : 'text-[#1A202C] hover:bg-[#8BD3DD]'; ?>">
                    🔍 Detail
                </a>
                <a href="peringkat.php" class="px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 <?php echo ($current_page == 'peringkat.php' || $current_page == 'edit.php') ? 'bg-[#FE98A3] text-[#1A202C] shadow-sm' : 'text-[#1A202C] hover:bg-[#8BD3DD]'; ?>">
                    📊 Peringkat
                </a>
            </div>

            <a href="#" onclick="bukaModalLogout(event)" class="bg-[#E53E3E] whitespace-nowrap text-white px-5 py-2.5 rounded-xl font-black text-sm transition-all duration-200 border-2 border-[#1A202C] shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C]">
                🚪 Logout
            </a>
        </div>
    </div>
</nav>

<?php if (isset($_SESSION['alert'])): ?>
    <div class="max-w-6xl mx-auto mb-6 transform transition-all duration-300">
        <div class="p-4 rounded-2xl border-2 flex items-center justify-between gap-3 <?php echo ($_SESSION['alert']['type'] == 'success') ? 'bg-[#8BD3DD] border-[#1A202C] text-[#1A202C]' : 'bg-[#FAAE2B] border-[#1A202C] text-[#1A202C]'; ?>">
            <div class="flex items-center gap-3 font-bold">
                <span class="text-xl"><?php echo ($_SESSION['alert']['type'] == 'success') ? '✨' : '⚠️'; ?></span>
                <p><?php echo $_SESSION['alert']['message']; ?></p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="font-black hover:text-white text-lg cursor-pointer">&times;</button>
        </div>
    </div>
    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<div id="modalLogout" class="fixed inset-0 bg-black/60 z-[999] hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
    <div class="bg-white border-4 border-[#1A202C] rounded-3xl p-6 md:p-8 shadow-[8px_8px_0px_0px_#1A202C] max-w-sm w-full mx-4 transform scale-95 transition-transform duration-300" id="modalContentLogout">
        <div class="text-center">
            <div class="text-5xl mb-4">🚪</div>
            <h3 class="text-xl font-black mb-2 text-[#1A202C]">Keluar dari Sistem?</h3>
            <p class="text-sm font-bold text-gray-600 mb-6">Sesi Anda akan diakhiri dan Anda harus login kembali.</p>
            <div class="flex gap-3">
                <button onclick="tutupModalLogout()" class="w-1/2 bg-white text-[#1A202C] border-2 border-[#1A202C] font-black py-3 rounded-xl shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all cursor-pointer">Batal</button>
                <a href="logout.php" class="w-1/2 bg-[#E53E3E] text-white border-2 border-[#1A202C] font-black py-3 rounded-xl shadow-[3px_3px_0px_0px_#1A202C] hover:translate-y-0.5 hover:shadow-[1px_1px_0px_0px_#1A202C] transition-all flex items-center justify-center">Ya, Keluar</a>
            </div>
        </div>
    </div>
</div>

<script>
    function bukaModalLogout(e) {
        if(e) e.preventDefault();
        const modal = document.getElementById('modalLogout');
        const content = document.getElementById('modalContentLogout');
        modal.classList.remove('hidden');
        setTimeout(() => { content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
    }
    function tutupModalLogout() {
        const modal = document.getElementById('modalLogout');
        const content = document.getElementById('modalContentLogout');
        content.classList.remove('scale-100'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 200);
    }
</script>