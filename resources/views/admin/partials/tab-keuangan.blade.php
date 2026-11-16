<!-- =================================================================== -->
<!-- TAB 1: DASHBOARD KEUANGAN & LABA BERSIH                             -->
<!-- =================================================================== -->
<section x-show="activeTab === 'finance'" class="space-y-6">

    <!-- Header & Filter Periode (Hari Ini, Minggu Ini, Bulan Ini, Pilih Tanggal Custom) + Tombol Export -->
    <div class="bg-white p-5 rounded-2xl border border-cream-dark shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-espresso">
                    Dashboard Keuangan &amp; Laba Bersih
                </h2>
                <p class="text-xs text-espresso/65 mt-0.5">
                    Menampilkan data periode: <span class="font-bold text-coffee" x-text="currentStats.label"></span>
                </p>
            </div>

            <!-- Pilihan Tab Periode & Tombol Export Excel / Print -->
            <div class="flex flex-wrap items-center gap-2.5 no-print">

                <!-- 4 Tombol Periode -->
                <div class="inline-flex flex-wrap items-center bg-cream p-1 rounded-xl border border-cream-dark">
                    <button type="button"
                            @click="activePeriod = 'today'"
                            :class="activePeriod === 'today' ? 'bg-espresso text-gold font-extrabold shadow-xs' : 'text-espresso/75 hover:text-espresso font-semibold'"
                            class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm transition">
                        Hari Ini
                    </button>
                    <button type="button"
                            @click="activePeriod = 'week'"
                            :class="activePeriod === 'week' ? 'bg-espresso text-gold font-extrabold shadow-xs' : 'text-espresso/75 hover:text-espresso font-semibold'"
                            class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm transition">
                        Minggu Ini
                    </button>
                    <button type="button"
                            @click="activePeriod = 'month'"
                            :class="activePeriod === 'month' ? 'bg-espresso text-gold font-extrabold shadow-xs' : 'text-espresso/75 hover:text-espresso font-semibold'"
                            class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm transition">
                        Bulan Ini
                    </button>
                    <button type="button"
                            @click="activePeriod = 'custom'"
                            :class="activePeriod === 'custom' ? 'bg-espresso text-gold font-extrabold shadow-xs' : 'text-espresso/75 hover:text-espresso font-semibold'"
                            class="px-3.5 py-1.5 rounded-lg text-xs sm:text-sm transition flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Pilih Tanggal</span>
                    </button>
                </div>

                <!-- Tombol Download Laporan Excel (CSV) -->
                <a :href="exportCsvUrl"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs sm:text-sm shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download Excel (CSV)</span>
                </a>

                <!-- Tombol Cetak Rekap / Simpan PDF -->
                <button type="button"
                        @click="printFinanceReport()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-cream hover:bg-gold text-espresso font-extrabold text-xs sm:text-sm border border-cream-dark transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak / PDF</span>
                </button>
            </div>
        </div>

        <!-- Bar Pemilihan Rentang Tanggal Bebas (Muncul Saat Pilih "Pilih Tanggal") -->
        <div x-show="activePeriod === 'custom'"
             x-transition
             x-cloak
             class="no-print pt-3 border-t border-cream-dark flex flex-col sm:flex-row sm:items-end justify-between gap-3 bg-cream-light/70 p-3.5 rounded-xl border border-cream-dark">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-coffee mb-1">
                        Dari Tanggal (Mulai)
                    </label>
                    <input type="date"
                           x-model="customStartDate"
                           class="px-3.5 py-2 rounded-xl bg-white border border-cream-dark focus:border-coffee text-xs sm:text-sm font-mono font-bold text-espresso outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-coffee mb-1">
                        Sampai Tanggal (Akhir)
                    </label>
                    <input type="date"
                           x-model="customEndDate"
                           class="px-3.5 py-2 rounded-xl bg-white border border-cream-dark focus:border-coffee text-xs sm:text-sm font-mono font-bold text-espresso outline-none">
                </div>

                <button type="button"
                        @click="fetchCustomFinanceStats()"
                        :disabled="isLoadingCustomStats"
                        class="px-4 py-2 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-xs sm:text-sm shadow transition flex items-center gap-2">
                    <span x-show="!isLoadingCustomStats">Tampilkan Laporan Tanggal Ini</span>
                    <span x-show="isLoadingCustomStats">Memuat Data...</span>
                </button>
            </div>

            <div class="text-xs text-espresso/65 italic">
                Tip: Klik <strong>Download Excel (CSV)</strong> untuk mengunduh rincian transaksi &amp; pengeluaran pada rentang tanggal di atas.
            </div>
        </div>
    </div>

    <!-- 4 Card Ringkasan Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: Total Omzet Penjualan -->
        <div class="bg-white rounded-2xl border border-cream-dark p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-espresso/60">Total Omzet Penjualan</span>
                <span class="w-9 h-9 rounded-xl bg-cream text-coffee flex items-center justify-center font-bold">Rp</span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold font-mono text-espresso" x-text="formatRupiah(currentStats.total_omzet)"></p>
                <p class="text-xs text-espresso/60 mt-1">
                    Dari <span class="font-bold text-espresso" x-text="currentStats.total_orders"></span> transaksi (<span class="font-bold text-coffee" x-text="currentStats.total_cups_sold"></span> cup terjual)
                </p>
            </div>
        </div>

        <!-- Card 2: Total Pengeluaran Belanja -->
        <div class="bg-white rounded-2xl border border-cream-dark p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-red-700/80">Total Pengeluaran Belanja</span>
                <span class="w-9 h-9 rounded-xl bg-red-50 text-red-700 flex items-center justify-center font-bold">-</span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold font-mono text-red-700" x-text="formatRupiah(currentStats.total_expenses)"></p>
                <p class="text-xs text-espresso/60 mt-1">
                    Akumulasi belanja bahan &amp; operasional
                </p>
            </div>
        </div>

        <!-- Card 3: Estimasi Laba Kotor -->
        <div class="bg-white rounded-2xl border border-cream-dark p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-coffee">Estimasi Laba Kotor</span>
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-coffee flex items-center justify-center font-bold">+</span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold font-mono text-coffee" x-text="formatRupiah(currentStats.gross_profit)"></p>
                <p class="text-xs text-espresso/60 mt-1">
                    Omzet dikurangi HPP (<span class="font-mono" x-text="formatRupiah(currentStats.total_hpp)"></span>)
                </p>
            </div>
        </div>

        <!-- Card 4: Laba Bersih Kas -->
        <div class="bg-gradient-to-br from-espresso to-coffee text-cream rounded-2xl border-2 border-gold/50 p-5 shadow-md flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gold">Laba Bersih Kas</span>
                <span class="w-9 h-9 rounded-xl bg-gold text-espresso flex items-center justify-center font-extrabold">✓</span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold font-mono"
                   :class="currentStats.net_cash_profit >= 0 ? 'text-gold' : 'text-red-300'"
                   x-text="formatRupiah(currentStats.net_cash_profit)"></p>
                <p class="text-xs text-cream/80 mt-1">
                    Total Omzet - Total Pengeluaran Belanja
                </p>
            </div>
        </div>

    </div>

    <!-- Baris Rincian Uang Masuk (Cash vs QRIS, Cup Terjual) & 5 Menu Terlaris -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <div class="lg:col-span-5 bg-white rounded-2xl border border-cream-dark p-5 shadow-xs space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-espresso">Rincian Uang Masuk &amp; Volume</h3>
                    <p class="text-xs text-espresso/60">Perbandingan pembayaran Cash vs QRIS serta total cup terjual</p>
                </div>

                <div class="p-4 rounded-xl bg-cream/60 border border-cream-dark flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-coffee uppercase tracking-wider block">Total Volume Penjualan</span>
                        <span class="text-2xl font-extrabold font-mono text-espresso" x-text="currentStats.total_cups_sold + ' Cup'"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-espresso/65 block">Total Diskon Promo</span>
                        <span class="text-sm font-extrabold font-mono text-emerald-700" x-text="formatRupiah(currentStats.total_discount || 0)"></span>
                    </div>
                </div>

                <!-- Rincian Cash -->
                <div class="p-3.5 rounded-xl bg-cream-light border border-cream-dark space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                            <span class="text-xs font-extrabold text-espresso uppercase">Uang Masuk CASH (Tunai)</span>
                        </div>
                        <span class="text-xs font-semibold text-espresso/60" x-text="currentStats.cash_count + ' transaksi'"></span>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <span class="text-lg font-extrabold font-mono text-emerald-700" x-text="formatRupiah(currentStats.total_cash)"></span>
                        <span class="text-xs font-mono font-bold text-espresso/60" x-text="cashPercentage + '%'"></span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-cream overflow-hidden">
                        <div class="h-full bg-emerald-600 rounded-full transition-all duration-300" :style="'width: ' + cashPercentage + '%'"></div>
                    </div>
                </div>

                <!-- Rincian QRIS -->
                <div class="p-3.5 rounded-xl bg-cream-light border border-cream-dark space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                            <span class="text-xs font-extrabold text-espresso uppercase">Uang Masuk QRIS</span>
                        </div>
                        <span class="text-xs font-semibold text-espresso/60" x-text="currentStats.qris_count + ' transaksi'"></span>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <span class="text-lg font-extrabold font-mono text-blue-700" x-text="formatRupiah(currentStats.total_qris)"></span>
                        <span class="text-xs font-mono font-bold text-espresso/60" x-text="qrisPercentage + '%'"></span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-cream overflow-hidden">
                        <div class="h-full bg-blue-600 rounded-full transition-all duration-300" :style="'width: ' + qrisPercentage + '%'"></div>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-cream-dark flex items-center justify-between text-xs text-espresso/70 no-print">
                <span>Ada transaksi salah input?</span>
                <button type="button"
                        @click="switchTab('orders')"
                        class="font-extrabold text-coffee hover:underline">
                    Buka Riwayat &amp; Void Transaksi →
                </button>
            </div>
        </div>

        <!-- Daftar 5 Menu Terlaris -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-cream-dark p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-espresso">Daftar 5 Menu Terlaris</h3>
                        <p class="text-xs text-espresso/60">Menu dengan penjualan cup tertinggi pada periode terpilih</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-cream text-coffee">
                        Top 5 Menu
                    </span>
                </div>

                <div x-show="currentStats.top_products && currentStats.top_products.length > 0" class="space-y-2.5">
                    <template x-for="(top, idx) in currentStats.top_products" :key="top.product_name">
                        <div class="p-3.5 rounded-xl bg-cream-light border border-cream-dark flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-mono font-extrabold text-xs shrink-0"
                                     :class="idx === 0 ? 'bg-gold text-espresso shadow-xs' : (idx === 1 ? 'bg-coffee text-cream' : 'bg-cream text-espresso')"
                                     x-text="'#' + (idx + 1)"></div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-extrabold text-espresso truncate" x-text="top.product_name"></h4>
                                    <p class="text-[11px] text-espresso/60">
                                        Laba Kotor: <span class="font-mono font-bold text-emerald-700" x-text="formatRupiah(top.total_profit)"></span>
                                    </p>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-espresso text-gold font-mono"
                                      x-text="top.total_qty + ' Cup'"></span>
                                <p class="text-xs font-mono font-bold text-coffee mt-1" x-text="formatRupiah(top.total_revenue)"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="!currentStats.top_products || currentStats.top_products.length === 0"
                     class="py-12 text-center border-2 border-dashed border-cream-dark rounded-2xl bg-cream-light/50">
                    <p class="text-sm font-bold text-espresso">Belum ada data penjualan pada periode ini</p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-cream-dark flex items-center justify-between text-xs no-print">
                <span class="text-espresso/60">Perlu menyesuaikan HPP atau harga jual menu?</span>
                <button type="button"
                        @click="switchTab('products')"
                        class="font-extrabold text-coffee hover:underline">
                    Kelola Menu &amp; HPP →
                </button>
            </div>
        </div>

    </div>

</section>
