<!-- ================= 5. MODAL OUTPUT NOTA GANDA (STRUK THERMAL & NOTA WA) ================= -->
<div x-show="receiptModal.open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-espresso-deep/75 backdrop-blur-xs p-4 overflow-y-auto">

    <div @click.away="receiptModal.open = false"
         class="bg-white rounded-2xl border-2 border-gold shadow-2xl max-w-3xl w-full overflow-hidden my-8">

        <div class="bg-espresso text-cream px-5 py-4 flex items-center justify-between border-b border-gold/30">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white p-1 flex items-center justify-center border border-gold shadow-sm shrink-0">
                    <img src="{{ asset('images/logo-yoursuf.jpg') }}"
                         alt="Logo Yoursuf Coffee"
                         class="w-full h-full object-contain">
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-cream">
                        Output Nota Ganda — <span class="text-gold font-mono" x-text="receiptModal.order?.order_number"></span>
                    </h3>
                    <p class="text-xs text-cream/70">
                        Pilih Cetak Struk Thermal (58mm/80mm) atau Kirim Nota Digital WhatsApp
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="receiptModal.open = false"
                    class="w-11 h-11 flex items-center justify-center text-cream/70 hover:text-gold rounded-xl transition">
                ✕
            </button>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5 bg-cream-light max-h-[78vh] overflow-y-auto">

            <!-- OPSI 1: CETAK STRUK THERMAL (58mm / 80mm) -->
            <div class="flex flex-col justify-between bg-white rounded-2xl border border-cream-dark p-4 shadow-xs">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-coffee">
                            Opsi 1: Struk Thermal
                        </span>

                        <div class="flex items-center gap-1 bg-cream p-1 rounded-xl">
                            <button type="button"
                                    @click="receiptModal.paperSize = '58mm'"
                                    :class="receiptModal.paperSize === '58mm' ? 'bg-espresso text-gold font-bold' : 'text-espresso/70'"
                                    class="min-h-[36px] px-3 rounded-lg text-xs font-mono transition">
                                58mm
                            </button>
                            <button type="button"
                                    @click="receiptModal.paperSize = '80mm'"
                                    :class="receiptModal.paperSize === '80mm' ? 'bg-espresso text-gold font-bold' : 'text-espresso/70'"
                                    class="min-h-[36px] px-3 rounded-lg text-xs font-mono transition">
                                80mm
                            </button>
                        </div>
                    </div>

                    <!-- Preview Visual Kertas Thermal -->
                    <div class="bg-stone-50 border border-dashed border-stone-300 rounded-lg p-3.5 font-mono text-xs text-stone-900 space-y-2 mx-auto"
                         :class="receiptModal.paperSize === '58mm' ? 'max-w-[250px]' : 'max-w-[320px]'">
                        <div class="text-center border-b border-dashed border-stone-400 pb-2">
                            <img src="{{ asset('images/logo-yoursuf.jpg') }}"
                                 alt="Yoursuf Coffee"
                                 class="w-14 h-14 mx-auto mb-1 object-contain rounded-lg bg-white p-0.5">
                            <p class="font-extrabold text-sm tracking-tight">YOURSUF COFFEE</p>
                            <p class="text-[10px] text-stone-600">Home Coffee &amp; Campus Delivery</p>
                            <p class="text-[10px] text-stone-600">UMY • UAA • UNJAYA • Krapyak</p>
                        </div>

                        <div class="text-[11px] space-y-0.5 border-b border-dashed border-stone-400 pb-2">
                            <div class="flex justify-between">
                                <span>No:</span>
                                <span class="font-bold" x-text="receiptModal.order?.order_number"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Waktu:</span>
                                <span x-text="formatDateTime(receiptModal.order?.created_at)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tipe:</span>
                                <span class="uppercase font-bold"
                                      x-text="receiptModal.order?.order_type === 'delivery'
                                          ? 'Delivery (' + (receiptModal.order?.delivery_area || '-') + ')'
                                          : 'Takeaway (Kos)'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pelanggan:</span>
                                <span x-text="receiptModal.order?.customer_name || 'Umum'"></span>
                            </div>
                        </div>

                        <div class="space-y-1.5 border-b border-dashed border-stone-400 pb-2 text-[11px]">
                            <template x-for="item in (receiptModal.order?.items || [])" :key="item.id">
                                <div>
                                    <div class="font-bold" x-text="item.product_name"></div>
                                    <div class="flex justify-between text-stone-700">
                                        <span x-text="item.quantity + ' x ' + formatRupiah(item.price)"></span>
                                        <span class="font-semibold" x-text="formatRupiah(item.subtotal)"></span>
                                    </div>
                                    <div x-show="item.item_notes" class="text-[10px] italic text-stone-600" x-text="'* ' + item.item_notes"></div>
                                </div>
                            </template>
                        </div>

                        <div class="space-y-1 text-[11px] border-b border-dashed border-stone-400 pb-2">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span x-text="formatRupiah(orderItemsSubtotal(receiptModal.order))"></span>
                            </div>
                            <div x-show="receiptModal.order?.order_type === 'delivery'" class="flex justify-between">
                                <span>Ongkir:</span>
                                <span x-text="(receiptModal.order?.delivery_fee || 0) === 0 ? 'GRATIS' : formatRupiah(receiptModal.order?.delivery_fee)"></span>
                            </div>
                            <div x-show="(receiptModal.order?.discount_amount || 0) > 0" class="flex justify-between text-emerald-700 font-semibold">
                                <span>Diskon:</span>
                                <span x-text="'- ' + formatRupiah(receiptModal.order?.discount_amount)"></span>
                            </div>
                            <div class="flex justify-between font-extrabold text-xs pt-1">
                                <span>TOTAL:</span>
                                <span x-text="formatRupiah(receiptModal.order?.total_amount)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Bayar (<span class="uppercase" x-text="receiptModal.order?.payment_method"></span>):</span>
                                <span x-text="formatRupiah(receiptModal.order?.cash_received)"></span>
                            </div>
                            <div x-show="receiptModal.order?.payment_method === 'cash'" class="flex justify-between">
                                <span>Kembali:</span>
                                <span x-text="formatRupiah(receiptModal.order?.change_amount)"></span>
                            </div>
                        </div>

                        <div class="text-center text-[10px] text-stone-600 pt-1">
                            <p>Terima kasih sudah ngopi</p>
                            <p class="font-bold">di Yoursuf Coffee!</p>
                        </div>
                    </div>
                </div>

                <button type="button"
                        @click="printThermalReceipt()"
                        class="mt-4 w-full min-h-[48px] py-3 px-4 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 shadow active:scale-95 transition">
                    <span>Cetak Struk Thermal (<span x-text="receiptModal.paperSize"></span>)</span>
                </button>
            </div>

            <!-- OPSI 2: NOTA DIGITAL WHATSAPP -->
            <div class="flex flex-col justify-between bg-white rounded-2xl border border-cream-dark p-4 shadow-xs">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">
                            Opsi 2: Nota Digital WhatsApp
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Siap Kirim / Copy
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-espresso mb-1">
                            Nomor WhatsApp Tujuan:
                        </label>
                        <input type="tel"
                               x-model="receiptModal.waPhone"
                               placeholder="Contoh: 081234567890"
                               class="w-full min-h-[44px] px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-xs sm:text-sm font-mono text-espresso outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-espresso mb-1">
                            Format Teks Struk WhatsApp:
                        </label>
                        <textarea readonly
                                  rows="10"
                                  :value="generateWhatsAppText(receiptModal.order)"
                                  class="w-full p-3 rounded-xl bg-stone-50 border border-stone-300 font-mono text-[11px] text-stone-800 leading-relaxed outline-none resize-none select-text"></textarea>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <button type="button"
                            @click="copyWhatsAppReceipt()"
                            class="min-h-[48px] py-2.5 px-3 rounded-xl bg-cream hover:bg-cream-dark text-espresso font-extrabold text-xs sm:text-sm flex items-center justify-center gap-1.5 border border-cream-dark active:scale-95 transition">
                        <span x-text="receiptModal.copied ? 'Tersalin! ✓' : 'Copy Struk WA'"></span>
                    </button>

                    <button type="button"
                            @click="sendToWhatsApp()"
                            class="min-h-[48px] py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow active:scale-95 transition">
                        <span>Kirim ke WhatsApp</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI KOSONGKAN KERANJANG (CUSTOM WEB, TOUCH-FRIENDLY) ================= -->
<div x-show="clearCartModal"
     x-transition
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-espresso-deep/80 backdrop-blur-xs p-4">
    <div @click.away="clearCartModal = false"
         class="bg-white rounded-2xl border-2 border-red-400 shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="bg-espresso px-5 py-4 flex items-center justify-between border-b border-gold/30">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center font-extrabold text-sm">!</span>
                <h3 class="text-sm sm:text-base font-extrabold text-cream">Kosongkan Keranjang?</h3>
            </div>
            <button type="button" @click="clearCartModal = false" class="w-10 h-10 flex items-center justify-center text-cream/70 hover:text-gold text-sm">✕</button>
        </div>
        <div class="p-5 bg-cream-light space-y-4">
            <p class="text-xs sm:text-sm text-espresso/80 leading-relaxed">
                Semua item menu (<span class="font-bold text-coffee" x-text="totalCartQty + ' cup'"></span>) yang sudah dipilih di keranjang saat ini akan dihapus.
            </p>
            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-cream-dark">
                <button type="button"
                        @click="clearCartModal = false"
                        class="min-h-[44px] px-4 py-2 rounded-xl bg-cream hover:bg-cream-dark text-espresso font-bold text-xs sm:text-sm active:scale-95 transition">
                    Batal
                </button>
                <button type="button"
                        @click="clearCart(); clearCartModal = false"
                        class="min-h-[44px] px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs sm:text-sm shadow active:scale-95 transition">
                    Ya, Kosongkan
                </button>
            </div>
        </div>
    </div>
</div>

