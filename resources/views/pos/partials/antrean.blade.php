<!-- ================= VIEW 2: MANAJEMEN ANTREAN PESANAN (3 TAHAP, TOUCH-FRIENDLY >= 44PX) ================= -->
<section x-show="activeMainTab === 'queue'" x-cloak class="flex-1 overflow-y-auto overscroll-contain p-4 lg:p-6 max-w-[1600px] w-full mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-lg sm:text-xl font-extrabold text-espresso">
                Manajemen Antrean Pesanan (3 Tahap)
            </h2>
            <p class="text-xs text-espresso/70">
                Sentuh tombol aksi untuk menggeser status: <span class="font-bold text-coffee">Pesanan Masuk → Sedang Dibuat → Selesai</span>
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button"
                    @click="refreshOrders()"
                    class="min-h-[44px] inline-flex items-center gap-2 px-4 rounded-xl bg-white border-2 border-cream-dark hover:border-coffee text-xs sm:text-sm font-bold text-espresso shadow-xs active:scale-95 transition">
                <span>Segarkan Antrean</span>
            </button>

            <button type="button"
                    @click="activeMainTab = 'catalog'"
                    class="min-h-[44px] inline-flex items-center gap-2 px-4 rounded-xl bg-coffee hover:bg-espresso text-cream text-xs sm:text-sm font-extrabold shadow-xs active:scale-95 transition">
                <span>+ Buat Pesanan Baru</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-5">

        <!-- TAHAP 1: PESANAN MASUK ('masuk') -->
        <div class="bg-cream/50 rounded-2xl border-2 border-amber-500/40 p-4 flex flex-col">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-cream-dark">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <h3 class="font-extrabold text-sm text-espresso uppercase tracking-wider">1. Pesanan Masuk</h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-500 text-espresso"
                      x-text="ordersByStatus('masuk').length"></span>
            </div>

            <div class="space-y-3 flex-1">
                <template x-for="order in ordersByStatus('masuk')" :key="order.id">
                    <div class="bg-white rounded-2xl border border-cream-dark p-4 shadow-xs space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono text-xs font-extrabold text-coffee bg-cream px-2.5 py-0.5 rounded-lg"
                                      x-text="order.order_number"></span>
                                <h4 class="text-sm font-extrabold text-espresso mt-1.5"
                                    x-text="order.customer_name || 'Pelanggan Umum'"></h4>
                                <p x-show="order.customer_wa" class="text-xs font-mono text-espresso/65" x-text="'WA: ' + order.customer_wa"></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase"
                                      :class="order.order_type === 'delivery' ? 'bg-blue-100 text-blue-800' : 'bg-stone-200 text-stone-800'"
                                      x-text="order.order_type === 'delivery' ? 'Delivery • ' + (order.delivery_area || '') : 'Takeaway'"></span>
                                <p class="text-[11px] text-espresso/50 mt-1" x-text="formatTime(order.created_at)"></p>
                            </div>
                        </div>

                        <div class="bg-cream-light rounded-xl p-3 space-y-1.5 border border-cream-dark">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="text-xs">
                                    <div class="flex justify-between font-bold text-espresso">
                                        <span><span class="font-mono text-coffee" x-text="item.quantity + 'x'"></span> <span x-text="item.product_name"></span></span>
                                        <span class="font-mono" x-text="formatRupiah(item.subtotal)"></span>
                                    </div>
                                    <p x-show="item.item_notes" class="text-[11px] italic text-amber-800 pl-4" x-text="'Catatan: ' + item.item_notes"></p>
                                </div>
                            </template>
                            <p x-show="order.notes" class="text-[11px] text-espresso/75 pt-1 border-t border-cream-dark italic" x-text="'Info: ' + order.notes"></p>
                        </div>

                        <div class="flex items-center justify-between pt-1 gap-2">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-espresso/60" x-text="order.payment_method.toUpperCase()"></span>
                                <p class="font-mono text-sm font-extrabold text-espresso" x-text="formatRupiah(order.total_amount)"></p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="openReceiptModal(order)"
                                        class="min-h-[44px] px-3.5 rounded-xl bg-cream hover:bg-cream-dark text-espresso text-xs font-extrabold active:scale-95 transition">
                                    Nota / WA
                                </button>

                                <button type="button"
                                        @click="updateOrderStatus(order, 'dibuat')"
                                        class="min-h-[44px] px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-espresso font-extrabold text-xs shadow active:scale-95 transition flex items-center gap-1">
                                    <span>Mulai Buat</span>
                                    <span>→</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="ordersByStatus('masuk').length === 0"
                     class="text-center py-8 text-xs text-espresso/50 italic">
                    Tidak ada pesanan baru yang menunggu.
                </div>
            </div>
        </div>

        <!-- TAHAP 2: SEDANG DIBUAT ('dibuat') -->
        <div class="bg-cream/50 rounded-2xl border-2 border-coffee/40 p-4 flex flex-col">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-cream-dark">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-coffee animate-pulse"></span>
                    <h3 class="font-extrabold text-sm text-espresso uppercase tracking-wider">2. Sedang Dibuat</h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-coffee text-cream"
                      x-text="ordersByStatus('dibuat').length"></span>
            </div>

            <div class="space-y-3 flex-1">
                <template x-for="order in ordersByStatus('dibuat')" :key="order.id">
                    <div class="bg-white rounded-2xl border-2 border-coffee/30 p-4 shadow-xs space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono text-xs font-extrabold text-cream bg-coffee px-2.5 py-0.5 rounded-lg"
                                      x-text="order.order_number"></span>
                                <h4 class="text-sm font-extrabold text-espresso mt-1.5"
                                    x-text="order.customer_name || 'Pelanggan Umum'"></h4>
                                <p x-show="order.customer_wa" class="text-xs font-mono text-espresso/65" x-text="'WA: ' + order.customer_wa"></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase"
                                      :class="order.order_type === 'delivery' ? 'bg-blue-100 text-blue-800' : 'bg-stone-200 text-stone-800'"
                                      x-text="order.order_type === 'delivery' ? 'Delivery • ' + (order.delivery_area || '') : 'Takeaway'"></span>
                                <p class="text-[11px] text-espresso/50 mt-1" x-text="formatTime(order.created_at)"></p>
                            </div>
                        </div>

                        <div class="bg-cream-light rounded-xl p-3 space-y-1.5 border border-cream-dark">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="text-xs">
                                    <div class="flex justify-between font-bold text-espresso">
                                        <span><span class="font-mono text-coffee" x-text="item.quantity + 'x'"></span> <span x-text="item.product_name"></span></span>
                                        <span class="font-mono" x-text="formatRupiah(item.subtotal)"></span>
                                    </div>
                                    <p x-show="item.item_notes" class="text-[11px] italic text-amber-800 pl-4" x-text="'Catatan: ' + item.item_notes"></p>
                                </div>
                            </template>
                            <p x-show="order.notes" class="text-[11px] text-espresso/75 pt-1 border-t border-cream-dark italic" x-text="'Info: ' + order.notes"></p>
                        </div>

                        <div class="flex items-center justify-between pt-1 gap-2">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-espresso/60" x-text="order.payment_method.toUpperCase()"></span>
                                <p class="font-mono text-sm font-extrabold text-espresso" x-text="formatRupiah(order.total_amount)"></p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="openReceiptModal(order)"
                                        class="min-h-[44px] px-3.5 rounded-xl bg-cream hover:bg-cream-dark text-espresso text-xs font-extrabold active:scale-95 transition">
                                    Nota / WA
                                </button>

                                <button type="button"
                                        @click="updateOrderStatus(order, 'selesai')"
                                        class="min-h-[44px] px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow active:scale-95 transition flex items-center gap-1">
                                    <span>Selesai</span>
                                    <span>✓</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="ordersByStatus('dibuat').length === 0"
                     class="text-center py-8 text-xs text-espresso/50 italic">
                    Belum ada pesanan yang sedang diracik.
                </div>
            </div>
        </div>

        <!-- TAHAP 3: SELESAI ('selesai') -->
        <div class="bg-cream/50 rounded-2xl border-2 border-emerald-600/30 p-4 flex flex-col">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-cream-dark">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                    <h3 class="font-extrabold text-sm text-espresso uppercase tracking-wider">3. Selesai (Hari Ini)</h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-600 text-white"
                      x-text="ordersByStatus('selesai').length"></span>
            </div>

            <div class="space-y-3 flex-1">
                <template x-for="order in ordersByStatus('selesai')" :key="order.id">
                    <div class="bg-white/95 rounded-2xl border border-cream-dark p-4 shadow-xs space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-lg"
                                      x-text="order.order_number"></span>
                                <h4 class="text-sm font-bold text-espresso mt-1.5"
                                    x-text="order.customer_name || 'Pelanggan Umum'"></h4>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Selesai ✓
                                </span>
                                <p class="text-[11px] text-espresso/50 mt-1" x-text="formatTime(order.created_at)"></p>
                            </div>
                        </div>

                        <div class="text-xs text-espresso/80 space-y-0.5">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="flex justify-between">
                                    <span><span class="font-mono font-bold" x-text="item.quantity + 'x'"></span> <span x-text="item.product_name"></span></span>
                                    <span class="font-mono" x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-cream">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-espresso/50"
                                      x-text="order.order_type.toUpperCase() + ' • ' + order.payment_method.toUpperCase()"></span>
                                <p class="font-mono text-sm font-extrabold text-coffee" x-text="formatRupiah(order.total_amount)"></p>
                            </div>

                            <button type="button"
                                    @click="openReceiptModal(order)"
                                    class="min-h-[44px] px-4 rounded-xl bg-espresso hover:bg-coffee text-gold text-xs font-extrabold active:scale-95 transition flex items-center gap-1.5">
                                <span>Struk &amp; WA</span>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="ordersByStatus('selesai').length === 0"
                     class="text-center py-8 text-xs text-espresso/50 italic">
                    Belum ada pesanan yang selesai hari ini.
                </div>
            </div>
        </div>

    </div>
</section>

