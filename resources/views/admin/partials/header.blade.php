<!-- ================= HEADER ATAS AREA KONTEN ADMIN ================= -->
<header class="bg-white border-b border-cream-dark sticky top-0 z-20 shrink-0 shadow-2xs">
    <div class="px-4 sm:px-6 lg:px-8 h-[68px] flex items-center justify-between gap-4">

        <!-- Kiri: Tombol Buka Sidebar (Mobile) & Judul Menu Aktif -->
        <div class="flex items-center gap-3 min-w-0">
            <button type="button"
                    @click="sidebarOpen = true"
                    class="md:hidden w-11 h-11 rounded-xl bg-espresso text-gold flex items-center justify-center shadow-xs shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('images/logo-yoursuf.jpg') }}"
                     alt="Logo Yoursuf Coffee"
                     class="md:hidden w-10 h-10 rounded-xl bg-white p-0.5 border border-cream-dark object-contain shrink-0">
                <div class="min-w-0">
                    <h2 class="text-base sm:text-lg font-extrabold text-espresso truncate" x-text="activeTabTitle"></h2>
                    <p class="text-xs text-espresso/60 hidden sm:block truncate" x-text="activeTabSubtitle"></p>
                </div>
            </div>
        </div>

        <!-- Kanan: Tombol Cepat Kembali ke Kasir POS -->
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('pos.index') }}"
               class="min-h-[42px] inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-xs sm:text-sm shadow-xs transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Kasir POS</span>
            </a>
        </div>

    </div>
</header>

<!-- TOAST NOTIFIKASI -->
<div x-show="toast.visible"
     x-transition
     x-cloak
     class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl border"
     :class="toast.type === 'error' ? 'bg-red-900 text-white border-red-400' : 'bg-espresso text-cream border-gold'">
    <span class="w-2.5 h-2.5 rounded-full" :class="toast.type === 'error' ? 'bg-red-400' : 'bg-gold'"></span>
    <span class="text-sm font-semibold" x-text="toast.message"></span>
</div>

<!-- FLASH MESSAGE DARI SERVER -->
<div class="px-4 sm:px-6 lg:px-8 pt-4">
    @if(session('success'))
        <div class="mb-2 p-3.5 rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-2 p-3.5 rounded-2xl bg-red-100 border border-red-300 text-red-900 text-xs sm:text-sm font-bold">
            <p class="font-extrabold mb-1">Periksa kembali input Anda:</p>
            <ul class="list-disc list-inside space-y-0.5 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
