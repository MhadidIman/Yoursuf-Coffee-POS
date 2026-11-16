<!-- 2. AREA KIRI: KATALOG MENU (SCROLL INDEPENDEN) -->
<section class="flex-1 flex flex-col h-full overflow-hidden p-3 sm:p-4 lg:p-5">

    <!-- Quick Active Queue Strip (Touch-Friendly Buttons >= 44px) -->
    <div x-show="activeQueueCount > 0" x-cloak
         class="mb-3 bg-gradient-to-r from-coffee to-espresso text-cream rounded-2xl p-2.5 sm:p-3 border border-gold/30 shadow-sm flex flex-wrap items-center justify-between gap-2 shrink-0">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-gold animate-ping shrink-0"></span>
            <span class="text-xs sm:text-sm font-bold">
                <span class="text-gold underline" x-text="activeQueueCount"></span> Pesanan Aktif
            </span>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto py-0.5">
            <template x-for="activeOrd in activeOrdersPreview" :key="activeOrd.id">
                <div class="flex items-center gap-2 bg-espresso-deep/90 border border-gold/30 rounded-xl pl-3 pr-1.5 py-1 text-xs shrink-0">
                    <span class="font-mono font-bold text-gold" x-text="'#' + activeOrd.order_number.slice(-3)"></span>
                    <span class="truncate max-w-[85px] font-semibold" x-text="activeOrd.customer_name || (activeOrd.order_type === 'delivery' ? 'Delivery' : 'Takeaway')"></span>
                    <button type="button"
                            @click="advanceOrderStatus(activeOrd)"
                            class="min-h-[36px] px-3 rounded-lg bg-gold text-espresso font-extrabold hover:bg-gold-light active:scale-95 transition text-xs"
                            x-text="activeOrd.status === 'masuk' ? 'Buat →' : 'Selesai ✓'"></button>
                </div>
            </template>
            <button type="button"
                    @click="activeMainTab = 'queue'"
                    class="min-h-[36px] px-2.5 text-xs font-extrabold text-gold hover:underline shrink-0">
                Semua →
            </button>
        </div>
    </div>

    <!-- Bar Filter Kategori & Pencarian Cepat (Pinned di Atas Katalog, Tombol >= 46px) -->
    <div class="flex flex-col gap-2.5 mb-3.5 shrink-0">

        <!-- Baris 1: Kolom Pencarian Cepat Touch-Friendly (Tinggi 46px) -->
        <div class="relative w-full">
            <input type="text"
                   x-model="searchQuery"
                   placeholder="Cari nama kopi, rasa, atau komposisi menu..."
                   class="w-full min-h-[46px] pl-11 pr-11 py-2.5 rounded-2xl bg-white border-2 border-cream-dark focus:border-coffee text-sm font-medium text-espresso placeholder-espresso/45 outline-none shadow-2xs transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-coffee absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button type="button"
                    x-show="searchQuery.length > 0"
                    @click="searchQuery = ''"
                    class="w-11 h-11 flex items-center justify-center absolute right-1 top-1/2 -translate-y-1/2 text-espresso/60 hover:text-espresso rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Baris 2: Filter Tab 4 Kategori (Tinggi Minimal 46px, Mudah Ditekan Jari di Tablet) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <button type="button"
                    @click="selectedCategoryId = 'all'"
                    :class="selectedCategoryId === 'all'
                        ? 'bg-coffee text-cream border-coffee shadow-md font-extrabold'
                        : 'bg-white text-espresso border-cream-dark hover:border-coffee/50 font-bold'"
                    class="min-h-[46px] px-4 py-2 rounded-2xl text-xs sm:text-sm border-2 transition-all shrink-0 flex items-center gap-2 active:scale-[0.98]">
                <span>Semua Menu</span>
                <span class="px-2 py-0.5 rounded-full text-xs"
                      :class="selectedCategoryId === 'all' ? 'bg-gold text-espresso font-extrabold' : 'bg-cream text-coffee'"
                      x-text="products.length"></span>
            </button>

            <template x-for="cat in categories" :key="cat.id">
                <button type="button"
                        @click="selectedCategoryId = cat.id"
                        :class="selectedCategoryId === cat.id
                            ? 'bg-coffee text-cream border-coffee shadow-md font-extrabold'
                            : 'bg-white text-espresso border-cream-dark hover:border-coffee/50 font-bold'"
                        class="min-h-[46px] px-4 py-2 rounded-2xl text-xs sm:text-sm border-2 transition-all shrink-0 flex items-center gap-2 active:scale-[0.98]">
                    <span x-text="cat.name"></span>
                    <span class="px-2 py-0.5 rounded-full text-xs"
                          :class="selectedCategoryId === cat.id ? 'bg-gold text-espresso font-extrabold' : 'bg-cream text-coffee'"
                          x-text="countProductsInCategory(cat.id)"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Grid Katalog Produk (Scroll Independen, Card Besar & Tombol + 48x48px) -->
    <div class="flex-1 overflow-y-auto overscroll-contain pr-1 pb-6">
        <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-3.5">
            <template x-for="product in filteredProducts" :key="product.id">
                <div @click="product.is_available ? addToCart(product) : null"
                     :class="[
                         product.is_available
                             ? 'cursor-pointer active:scale-[0.98] hover:border-coffee bg-white'
                             : 'cursor-not-allowed opacity-60 bg-stone-100 border-stone-300',
                         getProductCartQty(product.id) > 0 ? 'ring-2 ring-gold border-coffee bg-cream-light/40' : 'border-cream-dark'
                     ]"
                     class="group relative rounded-2xl border-2 p-3.5 sm:p-4 min-h-[164px] flex flex-col justify-between transition-all duration-150 shadow-xs">

                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cream text-coffee truncate">
                                <span x-text="product.category ? product.category.name : 'Menu'"></span>
                            </span>

                            <template x-if="!product.is_available">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-red-600 text-white uppercase tracking-wider shrink-0">
                                    Habis
                                </span>
                            </template>

                            <template x-if="product.is_available && getProductCartQty(product.id) > 0">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-gold text-espresso shadow shrink-0">
                                    <span x-text="getProductCartQty(product.id) + 'x'"></span>
                                </span>
                            </template>
                        </div>

                        <h3 class="text-sm sm:text-base font-extrabold text-espresso group-hover:text-coffee transition-colors leading-snug"
                            x-text="product.name"></h3>

                        <p class="text-xs text-espresso/65 mt-1 line-clamp-2 leading-relaxed italic"
                           x-text="product.description || 'Racikan spesial Yoursuf Coffee'"></p>
                    </div>

                    <!-- Bagian Bawah Card: Harga & Tombol Sentuh Besar 48x48px -->
                    <div class="mt-3.5 pt-2.5 border-t border-cream flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] uppercase tracking-wider text-espresso/50 block font-bold">Harga Cup</span>
                            <span class="text-base sm:text-lg font-extrabold font-mono text-coffee" x-text="formatRupiah(product.price)"></span>
                        </div>

                        <!-- Tombol Tambah Ukuran 48px x 48px (w-12 h-12) -->
                        <button type="button"
                                :disabled="!product.is_available"
                                :class="product.is_available
                                    ? 'bg-coffee text-cream group-hover:bg-gold group-hover:text-espresso active:scale-95'
                                    : 'bg-stone-200 text-stone-400 cursor-not-allowed'"
                                class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all shadow-sm shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="filteredProducts.length === 0" x-cloak
             class="bg-white rounded-2xl border-2 border-cream-dark p-10 text-center my-4">
            <h4 class="font-bold text-espresso text-base">Menu tidak ditemukan</h4>
            <p class="text-xs text-espresso/60 mt-1">Coba kata kunci lain atau pilih kategori "Semua Menu".</p>
        </div>
    </div>
</section>

