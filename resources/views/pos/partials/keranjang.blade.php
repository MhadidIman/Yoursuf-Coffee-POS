<!-- 3. AREA KANAN: KERANJANG & TOMBOL BAYAR (LOCKED / STICKY DI KANAN LAYAR) -->
<aside class="w-full md:w-[360px] lg:w-[420px] xl:w-[460px] bg-white border-t-2 md:border-t-0 md:border-l-2 border-cream-dark flex flex-col h-full shrink-0 shadow-2xl z-20 overflow-hidden">

    <!-- Header Keranjang (Sticky di Atas Panel Kanan) -->
    <div class="px-4 py-3 bg-cream/70 border-b border-cream-dark flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-coffee text-gold flex items-center justify-center font-mono font-extrabold text-base shadow-xs"
                 x-text="totalCartQty"></div>
            <div>
                <h2 class="text-sm font-extrabold text-espresso leading-tight">Keranjang &amp; Checkout</h2>
                <p class="text-[11px] text-espresso/70">
                    Sisa Cup: <span class="font-mono font-bold text-coffee" x-text="cupStockCount"></span> |
                    Sisa Lid: <span class="font-mono font-bold text-coffee" x-text="lidStockCount"></span>
                </p>
            </div>
        </div>

        <button type="button"
                x-show="cart.length > 0"
                @click="clearCartModal = true"
                class="min-h-[44px] px-3.5 rounded-xl text-xs font-extrabold text-red-700 hover:text-red-900 bg-red-50 hover:bg-red-100 border border-red-200 active:scale-95 transition flex items-center">
            Kosongkan
        </button>
    </div>

    <!-- Scrollable Area di Dalam Keranjang (Item + Form Pelanggan & Pembayaran) -->
    <div class="flex-1 overflow-y-auto overscroll-contain p-3.5 sm:p-4 space-y-4">

        <!-- Empty Cart State -->
        <div x-show="cart.length === 0"
             class="py-8 px-4 text-center border-2 border-dashed border-cream-dark rounded-2xl bg-cream-light/50">
            <p class="text-sm font-bold text-espresso">Keranjang Masih Kosong</p>
            <p class="text-xs text-espresso/60 mt-1">Sentuh kartu menu di sebelah kiri untuk menambahkan pesanan.</p>
        </div>

        <!-- Daftar Item di Keranjang (Tombol +/- Berukuran 44px x 44px!) -->
        <div x-show="cart.length > 0" class="space-y-3">
            <template x-for="(item, index) in cart" :key="item.cart_key">
                <div class="p-3.5 rounded-2xl bg-cream-light border-2 border-cream-dark space-y-2.5 shadow-2xs">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h4 class="text-sm font-extrabold text-espresso leading-snug" x-text="item.name"></h4>
                            <p class="text-xs font-mono text-coffee font-bold mt-0.5">
                                <span x-text="formatRupiah(item.price)"></span>
                                <span class="text-espresso/50 font-normal">× <span x-text="item.quantity"></span> = </span>
                                <span class="text-espresso font-extrabold" x-text="formatRupiah(item.price * item.quantity)"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Kontrol Qty Touch-Friendly (Tinggi 44px, Lebar 44px per tombol) -->
                    <div class="flex items-center justify-between gap-2 pt-0.5">
                        <div class="inline-flex items-center bg-white border-2 border-cream-dark rounded-xl overflow-hidden shadow-2xs">
                            <button type="button"
                                    @click="decrementQty(index)"
                                    class="w-11 h-11 flex items-center justify-center bg-cream/60 hover:bg-cream text-espresso active:bg-coffee active:text-cream font-extrabold text-lg transition">
                                −
                            </button>
                            <span class="w-11 text-center text-sm font-mono font-extrabold text-espresso" x-text="item.quantity"></span>
                            <button type="button"
                                    @click="incrementQty(index)"
                                    class="w-11 h-11 flex items-center justify-center bg-cream/60 hover:bg-cream text-espresso active:bg-coffee active:text-cream font-extrabold text-lg transition">
                                +
                            </button>
                        </div>

                        <span class="text-sm font-mono font-extrabold text-coffee"
                              x-text="formatRupiah(item.price * item.quantity)"></span>
                    </div>

                    <!-- Input Catatan Per Item + Tombol Chip Cepat Touch-Friendly -->
                    <div class="pt-1 flex flex-col gap-2">
                        <input type="text"
                               x-model="item.item_notes"
                               placeholder="Catatan cup (misal: less sugar, less ice)..."
                               class="w-full min-h-[44px] px-3 py-2 rounded-xl bg-white border border-cream-dark focus:border-coffee text-xs text-espresso placeholder-espresso/45 outline-none">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">
                            <template x-for="preset in ['Less Sugar', 'Less Ice', 'Normal Sugar', 'Extra Ice']" :key="preset">
                                <button type="button"
                                        @click="toggleItemNotePreset(item, preset)"
                                        class="min-h-[38px] px-2 py-1.5 rounded-xl text-[11px] font-bold border transition active:scale-95 text-center"
                                        :class="(item.item_notes || '').toLowerCase().includes(preset.toLowerCase())
                                            ? 'bg-coffee text-cream border-coffee shadow-2xs'
                                            : 'bg-white text-espresso/75 border-cream-dark hover:border-coffee'">
                                    <span x-text="preset"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- FORM PENGATURAN ORDER, DISKON, & PEMBAYARAN (SEMUA TOMBOL >= 46PX) -->
        <div class="pt-3 border-t-2 border-cream-dark space-y-4">

            <!-- Pilihan Tipe Order: Takeaway vs Delivery (Min-H 48px) -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso/75 mb-2">
                    Tipe Pesanan
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                            @click="setOrderType('takeaway')"
                            :class="orderForm.order_type === 'takeaway'
                                ? 'bg-coffee text-cream border-coffee font-extrabold shadow-sm'
                                : 'bg-cream-light text-espresso border-cream-dark hover:border-coffee/50 font-bold'"
                            class="min-h-[48px] py-2.5 px-3 rounded-xl border-2 text-xs sm:text-sm flex items-center justify-center gap-2 active:scale-[0.98] transition">
                        <span>Takeaway (Kos)</span>
                    </button>

                    <button type="button"
                            @click="setOrderType('delivery')"
                            :class="orderForm.order_type === 'delivery'
                                ? 'bg-coffee text-cream border-coffee font-extrabold shadow-sm'
                                : 'bg-cream-light text-espresso border-cream-dark hover:border-coffee/50 font-bold'"
                            class="min-h-[48px] py-2.5 px-3 rounded-xl border-2 text-xs sm:text-sm flex items-center justify-center gap-2 active:scale-[0.98] transition">
                        <span>Delivery (Antar)</span>
                    </button>
                </div>
            </div>

            <!-- Form Data Pelanggan & Pengantaran (OPSIONAL, Input >= 44px) -->
            <div class="p-3.5 rounded-2xl bg-cream/40 border border-cream-dark space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-coffee">
                        Data Pelanggan (Opsional)
                    </span>
                    <span class="text-[10px] font-bold text-espresso/60 bg-white px-2 py-0.5 rounded-full border border-cream-dark">
                        Opsional
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <input type="text"
                           x-model="orderForm.customer_name"
                           placeholder="Nama Pelanggan"
                           class="w-full min-h-[44px] px-3 py-2 rounded-xl bg-white border border-cream-dark focus:border-coffee text-xs sm:text-sm text-espresso outline-none">
                    <input type="tel"
                           x-model="orderForm.customer_wa"
                           placeholder="No. WA (08xx)"
                           class="w-full min-h-[44px] px-3 py-2 rounded-xl bg-white border border-cream-dark focus:border-coffee text-xs sm:text-sm font-mono text-espresso outline-none">
                </div>

                <!-- Pilihan Area Kampus jika Delivery dipilih (Tombol >= 44px) -->
                <div x-show="orderForm.order_type === 'delivery'" x-cloak class="pt-1 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-espresso">Area Pengantaran:</label>
                        <span x-show="isFreeDeliveryArea"
                              class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                            FREE ONGKIR (Rp 0)
                        </span>
                    </div>

                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-1.5">
                        <template x-for="area in ['UMY', 'UAA', 'UNJAYA', 'Krapyak', 'Lainnya']" :key="area">
                            <button type="button"
                                    @click="selectDeliveryArea(area)"
                                    :class="orderForm.delivery_area === area
                                        ? 'bg-espresso text-gold border-espresso font-extrabold shadow-xs'
                                        : 'bg-white text-espresso border-cream-dark hover:border-coffee font-bold'"
                                    class="min-h-[44px] py-2 px-2 rounded-xl border text-xs text-center active:scale-95 transition">
                                <span x-text="area === 'Lainnya' ? 'Luar Area' : area"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Input Manual Ongkir jika pilih Luar Area -->
                    <div x-show="orderForm.delivery_area === 'Lainnya'" class="pt-1 space-y-2">
                        <label class="block text-xs font-bold text-espresso">Nominal Ongkir Luar Area (Rp):</label>
                        <input type="number"
                               min="0"
                               x-model.number="orderForm.delivery_fee"
                               @input="syncDefaultCashReceived()"
                               class="w-full min-h-[44px] px-3 py-2 rounded-xl bg-white border border-cream-dark focus:border-coffee text-sm font-mono font-bold text-espresso outline-none">
                        <div class="grid grid-cols-4 gap-1.5">
                            <template x-for="feePreset in [3000, 5000, 8000, 10000]" :key="feePreset">
                                <button type="button"
                                        @click="orderForm.delivery_fee = feePreset; syncDefaultCashReceived()"
                                        class="min-h-[44px] rounded-xl bg-white hover:bg-gold border border-cream-dark text-xs font-mono font-extrabold text-espresso active:scale-95 transition">
                                    <span x-text="(feePreset/1000) + 'K'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <input type="text"
                           x-model="orderForm.notes"
                           placeholder="Catatan lokasi / patokan kos (opsional)..."
                           class="w-full min-h-[44px] px-3 py-2 rounded-xl bg-white border border-cream-dark focus:border-coffee text-xs sm:text-sm text-espresso outline-none">
                </div>
            </div>

            <!-- Diskon / Paket Bundling Mahasiswa (Tombol >= 44px) -->
            <div class="p-3.5 rounded-2xl bg-cream/40 border border-cream-dark space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-extrabold uppercase tracking-wider text-coffee">
                        Diskon / Promo Bundling
                    </label>
                    <span x-show="effectiveDiscount > 0"
                          class="text-xs font-mono font-extrabold text-emerald-700"
                          x-text="'-' + formatRupiah(effectiveDiscount)"></span>
                </div>

                <div class="grid grid-cols-4 gap-1.5">
                    <button type="button"
                            @click="setDiscount(0)"
                            :class="Number(orderForm.discount_amount) === 0 ? 'bg-espresso text-gold font-extrabold border-espresso' : 'bg-white text-espresso font-bold border-cream-dark'"
                            class="min-h-[44px] rounded-xl border text-xs active:scale-95 transition">
                        Normal
                    </button>
                    <button type="button"
                            @click="setDiscount(2000)"
                            :class="Number(orderForm.discount_amount) === 2000 ? 'bg-espresso text-gold font-extrabold border-espresso' : 'bg-white text-espresso font-bold border-cream-dark'"
                            class="min-h-[44px] rounded-xl border text-xs font-mono active:scale-95 transition">
                        -2K
                    </button>
                    <button type="button"
                            @click="setDiscount(4000)"
                            :class="Number(orderForm.discount_amount) === 4000 ? 'bg-espresso text-gold font-extrabold border-espresso' : 'bg-white text-espresso font-bold border-cream-dark'"
                            class="min-h-[44px] rounded-xl border text-xs font-mono active:scale-95 transition">
                        -4K
                    </button>
                    <button type="button"
                            @click="setDiscount(5000)"
                            :class="Number(orderForm.discount_amount) === 5000 ? 'bg-espresso text-gold font-extrabold border-espresso' : 'bg-white text-espresso font-bold border-cream-dark'"
                            class="min-h-[44px] rounded-xl border text-xs font-mono active:scale-95 transition">
                        -5K
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-espresso/75 shrink-0">Diskon Manual:</span>
                    <input type="number"
                           min="0"
                           x-model.number="orderForm.discount_amount"
                           @input="syncDefaultCashReceived()"
                           placeholder="0"
                           class="w-full min-h-[44px] px-3 py-2 rounded-xl bg-white border border-cream-dark focus:border-coffee text-xs sm:text-sm font-mono font-bold text-espresso outline-none">
                </div>
            </div>

            <!-- Metode Pembayaran: Cash vs QRIS (Tombol >= 48px) -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso/75 mb-2">
                    Metode Pembayaran
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                            @click="setPaymentMethod('cash')"
                            :class="orderForm.payment_method === 'cash'
                                ? 'bg-espresso text-gold border-espresso font-extrabold shadow-sm'
                                : 'bg-cream-light text-espresso border-cream-dark hover:border-coffee/50 font-bold'"
                            class="min-h-[48px] py-2.5 px-3 rounded-xl border-2 text-xs sm:text-sm flex items-center justify-center gap-2 active:scale-[0.98] transition">
                        <span>Cash (Tunai)</span>
                    </button>

                    <button type="button"
                            @click="setPaymentMethod('qris')"
                            :class="orderForm.payment_method === 'qris'
                                ? 'bg-espresso text-gold border-espresso font-extrabold shadow-sm'
                                : 'bg-cream-light text-espresso border-cream-dark hover:border-coffee/50 font-bold'"
                            class="min-h-[48px] py-2.5 px-3 rounded-xl border-2 text-xs sm:text-sm flex items-center justify-center gap-2 active:scale-[0.98] transition">
                        <span>QRIS</span>
                    </button>
                </div>

                <!-- Kalkulator Uang Tunai & Kembalian Otomatis (Tombol Uang Cepat >= 46px) -->
                <div x-show="orderForm.payment_method === 'cash'" class="mt-3 p-3.5 rounded-2xl bg-cream-light border border-cream-dark space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-espresso">Uang Diterima (Rp):</label>
                        <span class="text-xs font-mono font-extrabold"
                              :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-600'"
                              x-text="changeAmount >= 0 ? 'Kembali: ' + formatRupiah(changeAmount) : 'Kurang: ' + formatRupiah(Math.abs(changeAmount))"></span>
                    </div>

                    <input type="number"
                           min="0"
                           x-model.number="orderForm.cash_received"
                           placeholder="Masukkan nominal uang diterima..."
                           class="w-full min-h-[46px] px-3.5 py-2 rounded-xl bg-white border-2 border-cream-dark focus:border-coffee text-base font-mono font-extrabold text-espresso outline-none">

                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button"
                                @click="orderForm.cash_received = grandTotal"
                                class="min-h-[46px] px-2 rounded-xl bg-gold/30 hover:bg-gold text-espresso font-extrabold text-xs border border-gold active:scale-95 transition">
                            Uang Pas
                        </button>
                        <button type="button"
                                @click="orderForm.cash_received = 20000"
                                class="min-h-[46px] px-2 rounded-xl bg-white hover:bg-cream border border-cream-dark text-espresso font-mono font-extrabold text-xs active:scale-95 transition">
                            20K
                        </button>
                        <button type="button"
                                @click="orderForm.cash_received = 50000"
                                class="min-h-[46px] px-2 rounded-xl bg-white hover:bg-cream border border-cream-dark text-espresso font-mono font-extrabold text-xs active:scale-95 transition">
                            50K
                        </button>
                        <button type="button"
                                @click="orderForm.cash_received = 100000"
                                class="min-h-[46px] px-2 rounded-xl bg-white hover:bg-cream border border-cream-dark text-espresso font-mono font-extrabold text-xs active:scale-95 transition">
                            100K
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Total & Tombol Bayar (STICKY / TETAP DIAM DI BAWAH KANAN LAYAR, TINGGI TOMBOL 52PX) -->
    <div class="p-4 bg-espresso text-cream border-t-2 border-gold/40 space-y-3 shrink-0 shadow-2xl">
        <div class="space-y-1 text-xs">
            <div class="flex justify-between text-cream/80">
                <span>Subtotal Menu (<span x-text="totalCartQty"></span> cup)</span>
                <span class="font-mono font-semibold" x-text="formatRupiah(cartSubtotal)"></span>
            </div>
            <div x-show="orderForm.order_type === 'delivery'" class="flex justify-between text-cream/80">
                <span>Ongkir (<span x-text="orderForm.delivery_area || 'Kampus'"></span>)</span>
                <span class="font-mono font-semibold"
                      :class="effectiveDeliveryFee === 0 ? 'text-gold' : 'text-cream'"
                      x-text="effectiveDeliveryFee === 0 ? 'GRATIS (Rp 0)' : formatRupiah(effectiveDeliveryFee)"></span>
            </div>
            <div x-show="effectiveDiscount > 0" class="flex justify-between text-emerald-300 font-semibold">
                <span>Potongan Diskon / Promo</span>
                <span class="font-mono" x-text="'- ' + formatRupiah(effectiveDiscount)"></span>
            </div>
            <div class="flex justify-between items-baseline pt-1.5 border-t border-coffee text-sm sm:text-base font-extrabold">
                <span class="text-gold">TOTAL BAYAR</span>
                <span class="font-mono text-xl text-gold" x-text="formatRupiah(grandTotal)"></span>
            </div>
        </div>

        <!-- Tombol Utama Pembayaran Touch-Friendly (Tinggi 52px / min-h-[52px]) -->
        <button type="button"
                @click="submitCheckout()"
                :disabled="cart.length === 0 || isSubmitting"
                :class="cart.length === 0 || isSubmitting
                    ? 'bg-coffee/50 text-cream/40 cursor-not-allowed'
                    : 'bg-gradient-to-r from-gold to-gold-light hover:from-gold-light hover:to-gold text-espresso shadow-lg active:scale-[0.99]'"
                class="w-full min-h-[52px] py-3.5 px-4 rounded-2xl font-extrabold text-sm sm:text-base flex items-center justify-center gap-2 transition-all">
            <template x-if="!isSubmitting">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Proses Transaksi &amp; Cetak Nota</span>
                </span>
            </template>
            <template x-if="isSubmitting">
                <span>Menyimpan Pesanan...</span>
            </template>
        </button>
    </div>
</aside>

