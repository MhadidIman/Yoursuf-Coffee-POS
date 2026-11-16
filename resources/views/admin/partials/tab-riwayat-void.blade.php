<!-- =================================================================== -->
<!-- TAB 5: RIWAYAT & PEMBATALAN TRANSAKSI (VOID ORDER)                  -->
<!-- =================================================================== -->
<section x-show="activeTab === 'orders'" x-cloak class="space-y-5">

    <div class="bg-white rounded-2xl border border-cream-dark p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-extrabold text-espresso">Riwayat &amp; Pembatalan Transaksi (Void Order)</h2>
            <p class="text-xs text-espresso/65">
                Jika ada kesalahan input di kasir, klik <span class="font-bold text-red-700">Void / Batalkan Nota</span> — stok Cup &amp; Lid akan otomatis dikembalikan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <select x-model="orderStatusFilter"
                    class="px-3 py-2 rounded-xl bg-cream-light border border-cream-dark text-xs font-bold text-espresso outline-none">
                <option value="all">Semua Status</option>
                <option value="masuk">Pesanan Masuk</option>
                <option value="dibuat">Sedang Dibuat</option>
                <option value="selesai">Selesai</option>
            </select>

            <input type="text"
                   x-model="orderSearch"
                   placeholder="Cari No. Order / Nama Pelanggan..."
                   class="px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-xs text-espresso outline-none">
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-cream-dark p-5 shadow-xs overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-cream-dark text-[11px] font-extrabold uppercase tracking-wider text-espresso/60 bg-cream-light">
                    <th class="py-3.5 px-4 rounded-tl-xl">No. Order &amp; Waktu</th>
                    <th class="py-3.5 px-4">Pelanggan &amp; Tipe</th>
                    <th class="py-3.5 px-4">Rincian Item &amp; Kemasan</th>
                    <th class="py-3.5 px-4 text-right">Diskon</th>
                    <th class="py-3.5 px-4 text-right">Total Bayar</th>
                    <th class="py-3.5 px-4 text-center">Status</th>
                    <th class="py-3.5 px-4 text-center rounded-tr-xl">Aksi (Void)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cream-dark text-xs sm:text-sm">
                <template x-for="ord in filteredAdminOrders" :key="ord.id">
                    <tr class="hover:bg-cream-light/60 transition">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-mono font-extrabold text-coffee" x-text="ord.order_number"></div>
                            <div class="text-[11px] text-espresso/60" x-text="formatDateTime(ord.created_at)"></div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-espresso" x-text="ord.customer_name || 'Pelanggan Umum'"></div>
                            <div class="text-[11px] text-espresso/65 uppercase font-semibold"
                                 x-text="ord.order_type === 'delivery' ? 'Delivery (' + (ord.delivery_area || '-') + ')' : 'Takeaway Kos'"></div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="space-y-0.5">
                                <template x-for="item in (ord.items || [])" :key="item.id">
                                    <div class="text-xs">
                                        <span class="font-mono font-bold text-coffee" x-text="item.quantity + 'x'"></span>
                                        <span class="font-semibold text-espresso" x-text="item.product_name"></span>
                                        <span x-show="item.item_notes" class="text-[11px] italic text-amber-800" x-text="'(' + item.item_notes + ')'"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-cream text-espresso/80">
                                    Kemasan: Cup 16oz + Lid Otomatis
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono text-xs">
                            <span x-show="Number(ord.discount_amount) > 0" class="font-bold text-emerald-700" x-text="'-' + formatRupiah(ord.discount_amount)"></span>
                            <span x-show="!ord.discount_amount || Number(ord.discount_amount) === 0" class="text-espresso/40">-</span>
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <div class="font-mono font-extrabold text-espresso" x-text="formatRupiah(ord.total_amount)"></div>
                            <div class="text-[10px] font-bold uppercase text-espresso/60" x-text="ord.payment_method"></div>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase"
                                  :class="ord.status === 'selesai'
                                      ? 'bg-emerald-100 text-emerald-800'
                                      : (ord.status === 'dibuat' ? 'bg-coffee text-cream' : 'bg-amber-100 text-amber-800')"
                                  x-text="ord.status"></span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <button type="button"
                                    @click="openDeleteModal({
                                        title: 'Batalkan (Void) Nota Transaksi?',
                                        itemName: ord.order_number + ' — ' + (ord.customer_name || 'Pelanggan Umum') + ' (' + formatRupiah(ord.total_amount) + ')',
                                        message: 'Nota transaksi akan dihapus dan stok Cup 16oz + Lid (Tutup Cup) akan dikembalikan secara otomatis.',
                                        actionUrl: '/admin/orders/' + ord.id,
                                        buttonText: 'Ya, Void & Kembalikan Stok'
                                    })"
                                    class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-700 hover:text-white font-bold text-xs border border-red-200 transition">
                                Void / Hapus Nota
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div x-show="filteredAdminOrders.length === 0" class="py-10 text-center text-xs text-espresso/50 italic">
            Tidak ada riwayat transaksi yang ditemukan.
        </div>
    </div>

</section>

