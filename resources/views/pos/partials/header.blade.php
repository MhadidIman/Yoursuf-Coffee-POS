<!-- 1. HEADER NAVIGASI (STICKY AT TOP, TOUCH-FRIENDLY MIN-H 44PX-48PX BUTTONS) -->
<header class="bg-espresso text-cream border-b-2 border-gold/40 shadow-lg z-30 shrink-0">
    <div class="max-w-[1600px] mx-auto px-3 sm:px-5 h-[68px] flex items-center justify-between gap-2 sm:gap-4">

        <!-- Brand Logo & Identity -->
        <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
            <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center shadow-md border-2 border-gold/70 shrink-0 overflow-hidden">
                <img src="{{ asset('images/logo-yoursuf.jpg') }}"
                     alt="Logo Yoursuf Coffee"
                     class="w-full h-full object-contain">
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-sm sm:text-base lg:text-lg font-extrabold tracking-tight text-cream leading-none">
                        YOURSUF COFFEE <span class="text-gold">POS</span>
                    </h1>
                    <span class="hidden xl:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-coffee text-gold border border-gold/30">
                        Tablet Ready
                    </span>
                </div>
                <p class="text-[11px] text-cream/70 hidden lg:block mt-0.5">
                    Home Coffee &amp; Campus Free Delivery System
                </p>
            </div>
        </div>

        <!-- Tab / Toggle Cepat: Katalog POS vs Antrean Pesanan Aktif (Touch Target >= 44px) -->
        <div class="flex items-center bg-espresso-deep p-1 rounded-2xl border border-coffee">
            <button type="button"
                    @click="activeMainTab = 'catalog'"
                    :class="activeMainTab === 'catalog'
                        ? 'bg-gold text-espresso font-extrabold shadow-sm'
                        : 'text-cream/80 hover:text-cream font-semibold'"
                    class="min-h-[44px] flex items-center gap-2 px-3.5 sm:px-4 rounded-xl text-xs sm:text-sm transition-all duration-150 active:scale-[0.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Katalog POS</span>
            </button>

            <button type="button"
                    @click="activeMainTab = 'queue'"
                    :class="activeMainTab === 'queue'
                        ? 'bg-gold text-espresso font-extrabold shadow-sm'
                        : 'text-cream/80 hover:text-cream font-semibold'"
                    class="min-h-[44px] flex items-center gap-2 px-3.5 sm:px-4 rounded-xl text-xs sm:text-sm transition-all duration-150 active:scale-[0.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span>Antrean Aktif</span>
                <span class="inline-flex items-center justify-center min-w-[24px] h-6 px-1.5 rounded-full text-xs font-extrabold"
                      :class="activeMainTab === 'queue' ? 'bg-espresso text-gold' : (activeQueueCount > 0 ? 'bg-amber-500 text-espresso animate-pulse' : 'bg-coffee text-cream')"
                      x-text="activeQueueCount"></span>
            </button>
        </div>

        <!-- Kanan: Indikator Stok Cup & Lid, Ke Halaman Admin, & Logout -->
        <div class="flex items-center gap-2 shrink-0">

            <!-- Indikator Sisa Stok Cup 16oz & Lid -->
            <div class="hidden lg:flex items-center gap-2 px-3 min-h-[44px] rounded-xl bg-espresso-deep border border-coffee text-xs font-mono">
                <span class="text-cream/75">Cup:</span>
                <span class="font-extrabold"
                      :class="cupStockCount <= 25 ? 'text-red-400' : 'text-gold'"
                      x-text="cupStockCount"></span>
                <span class="text-coffee">|</span>
                <span class="text-cream/75">Lid:</span>
                <span class="font-extrabold"
                      :class="lidStockCount <= 25 ? 'text-red-400' : 'text-gold'"
                      x-text="lidStockCount"></span>
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="min-h-[44px] inline-flex items-center gap-2 px-3.5 sm:px-4 rounded-xl bg-gradient-to-r from-gold to-gold-light hover:from-gold-light hover:to-gold text-espresso font-extrabold text-xs sm:text-sm shadow-md active:scale-[0.98] transition-all border border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="hidden sm:inline">Admin / Backoffice</span>
                <span class="sm:hidden">Admin</span>
            </a>

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        title="Keluar / Logout"
                        class="min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl bg-coffee hover:bg-red-700 text-cream hover:text-white border border-gold/20 active:scale-[0.98] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- NOTIFIKASI TOAST SINGKAT -->
<div x-show="toast.visible"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     x-cloak
     class="fixed bottom-5 left-5 z-50 flex items-center gap-3 px-4 py-3.5 rounded-2xl shadow-xl border"
     :class="toast.type === 'error' ? 'bg-red-900 text-white border-red-400' : 'bg-espresso text-cream border-gold'">
    <span class="w-3 h-3 rounded-full shrink-0" :class="toast.type === 'error' ? 'bg-red-400' : 'bg-gold'"></span>
    <span class="text-sm font-bold" x-text="toast.message"></span>
</div>

