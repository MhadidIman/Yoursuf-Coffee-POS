<!-- ================= OVERLAY MOBILE SIDEBAR ================= -->
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     x-cloak
     class="fixed inset-0 z-40 bg-espresso-deep/75 backdrop-blur-xs md:hidden"></div>

<!-- ================= SIDEBAR NAVIGASI ADMIN BACKOFFICE ================= -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-espresso text-cream border-r-2 border-gold/30 flex flex-col justify-between shrink-0 shadow-2xl transition-transform duration-200 ease-in-out md:static md:h-[100dvh]">

    <!-- Bagian Atas Sidebar: Brand & Daftar Menu -->
    <div class="flex flex-col flex-1 overflow-y-auto">

        <!-- Logo & Nama Usaha -->
        <div class="h-[76px] px-5 flex items-center justify-between border-b border-coffee/80 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center shadow-md border-2 border-gold/70 shrink-0 overflow-hidden">
                    <img src="{{ asset('images/logo-yoursuf.jpg') }}"
                         alt="Logo Yoursuf Coffee"
                         class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-cream leading-none">
                        YOURSUF <span class="text-gold">COFFEE</span>
                    </h1>
                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-coffee text-gold border border-gold/30">
                        Admin Backoffice
                    </span>
                </div>
            </div>

            <!-- Tombol Tutup Sidebar di Layar Mobile -->
            <button type="button"
                    @click="sidebarOpen = false"
                    class="md:hidden w-9 h-9 rounded-xl bg-coffee/60 text-cream flex items-center justify-center">
                ✕
            </button>
        </div>

        <!-- Daftar 5 Menu Utama di Sidebar -->
        <div class="p-4 space-y-1.5">
            <p class="px-3 pb-1.5 text-[10px] font-extrabold uppercase tracking-widest text-cream/45">
                Menu Manajemen
            </p>

            <!-- Menu 1: Keuangan & Laba Bersih -->
            <button type="button"
                    @click="switchTab('finance'); sidebarOpen = false"
                    :class="activeTab === 'finance'
                        ? 'bg-gradient-to-r from-gold to-gold-light text-espresso font-extrabold shadow-md'
                        : 'text-cream/80 hover:text-cream hover:bg-coffee/60 font-semibold'"
                    class="w-full min-h-[48px] px-3.5 py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-between gap-3 transition-all text-left">
                <div class="flex items-center gap-3 min-w-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="truncate">Keuangan &amp; Laba</span>
                </div>
            </button>

            <!-- Menu 2: Pencatatan Pengeluaran -->
            <button type="button"
                    @click="switchTab('expenses'); sidebarOpen = false"
                    :class="activeTab === 'expenses'
                        ? 'bg-gradient-to-r from-gold to-gold-light text-espresso font-extrabold shadow-md'
                        : 'text-cream/80 hover:text-cream hover:bg-coffee/60 font-semibold'"
                    class="w-full min-h-[48px] px-3.5 py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-between gap-3 transition-all text-left">
                <div class="flex items-center gap-3 min-w-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="truncate">Catat Pengeluaran</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-mono shrink-0"
                      :class="activeTab === 'expenses' ? 'bg-espresso text-gold font-extrabold' : 'bg-coffee text-cream'"
                      x-text="expenses.length"></span>
            </button>

            <!-- Menu 3: Manajemen Menu & HPP -->
            <button type="button"
                    @click="switchTab('products'); sidebarOpen = false"
                    :class="activeTab === 'products'
                        ? 'bg-gradient-to-r from-gold to-gold-light text-espresso font-extrabold shadow-md'
                        : 'text-cream/80 hover:text-cream hover:bg-coffee/60 font-semibold'"
                    class="w-full min-h-[48px] px-3.5 py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-between gap-3 transition-all text-left">
                <div class="flex items-center gap-3 min-w-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="truncate">Menu &amp; HPP</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-mono shrink-0"
                      :class="activeTab === 'products' ? 'bg-espresso text-gold font-extrabold' : 'bg-coffee text-cream'"
                      x-text="products.length"></span>
            </button>

            <!-- Menu 4: Pengingat Stok Bahan Utama -->
            <button type="button"
                    @click="switchTab('ingredients'); sidebarOpen = false"
                    :class="activeTab === 'ingredients'
                        ? 'bg-gradient-to-r from-gold to-gold-light text-espresso font-extrabold shadow-md'
                        : 'text-cream/80 hover:text-cream hover:bg-coffee/60 font-semibold'"
                    class="w-full min-h-[48px] px-3.5 py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-between gap-3 transition-all text-left">
                <div class="flex items-center gap-3 min-w-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="truncate">Stok Bahan Utama</span>
                </div>
                <span x-show="lowStockCount > 0"
                      class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-600 text-white animate-pulse shrink-0"
                      x-text="lowStockCount + ' Kritis'"></span>
                <span x-show="lowStockCount === 0"
                      class="px-2 py-0.5 rounded-full text-[11px] font-mono shrink-0"
                      :class="activeTab === 'ingredients' ? 'bg-espresso text-gold font-extrabold' : 'bg-coffee text-cream'"
                      x-text="ingredients.length"></span>
            </button>

            <!-- Menu 5: Riwayat & Void Transaksi -->
            <button type="button"
                    @click="switchTab('orders'); sidebarOpen = false"
                    :class="activeTab === 'orders'
                        ? 'bg-gradient-to-r from-gold to-gold-light text-espresso font-extrabold shadow-md'
                        : 'text-cream/80 hover:text-cream hover:bg-coffee/60 font-semibold'"
                    class="w-full min-h-[48px] px-3.5 py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-between gap-3 transition-all text-left">
                <div class="flex items-center gap-3 min-w-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="truncate">Riwayat &amp; Void Nota</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-mono shrink-0"
                      :class="activeTab === 'orders' ? 'bg-espresso text-gold font-extrabold' : 'bg-coffee text-cream'"
                      x-text="orders.length"></span>
            </button>
        </div>

        <!-- Ringkasan Cepat Laba Hari Ini di Sidebar -->
        <div class="mx-4 mt-2 p-3.5 rounded-2xl bg-espresso-deep border border-coffee/80 space-y-2">
            <div class="flex items-center justify-between text-[11px]">
                <span class="text-cream/65 font-semibold">Omzet Hari Ini</span>
                <span class="font-mono font-bold text-gold" x-text="formatRupiah(financeStats.today.total_omzet)"></span>
            </div>
            <div class="flex items-center justify-between text-[11px]">
                <span class="text-cream/65 font-semibold">Cup Terjual</span>
                <span class="font-mono font-bold text-cream" x-text="financeStats.today.total_cups_sold + ' Cup'"></span>
            </div>
        </div>
    </div>

    <!-- Bagian Bawah Sidebar: Tombol Cepat ke Kasir POS & Logout -->
    <div class="p-4 border-t border-coffee/80 bg-espresso-deep space-y-2.5 shrink-0">
        <a href="{{ route('pos.index') }}"
           class="w-full min-h-[46px] px-4 py-2.5 rounded-xl bg-gradient-to-r from-gold to-gold-light hover:from-gold-light hover:to-gold text-espresso font-extrabold text-xs sm:text-sm shadow-md flex items-center justify-center gap-2 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Buka Halaman Kasir POS</span>
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full min-h-[42px] px-4 py-2 rounded-xl bg-coffee/70 hover:bg-red-700 text-cream hover:text-white font-bold text-xs flex items-center justify-center gap-2 border border-gold/15 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Keluar / Logout</span>
            </button>
        </form>
    </div>

</aside>

