<!-- =================================================================== -->
<!-- TAB 3: MANAJEMEN MENU & HPP                                         -->
<!-- =================================================================== -->
<section x-show="activeTab === 'products'" x-cloak class="space-y-5">

    <div class="bg-white rounded-2xl border border-cream-dark p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-extrabold text-espresso">Manajemen Menu &amp; Harga Pokok Penjualan (HPP)</h2>
            <p class="text-xs text-espresso/65">
                Atur harga jual, modal HPP per cup, dan klik switch <span class="font-bold text-coffee">Tersedia / Habis</span> secara instan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <select x-model="productCategoryFilter"
                    class="px-3 py-2 rounded-xl bg-cream-light border border-cream-dark text-xs font-bold text-espresso outline-none">
                <option value="all">Semua Kategori</option>
                <template x-for="cat in categories" :key="cat.id">
                    <option :value="cat.id" x-text="cat.name"></option>
                </template>
            </select>

            <input type="text"
                   x-model="productSearch"
                   placeholder="Cari nama menu..."
                   class="px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-xs text-espresso outline-none">

            <button type="button"
                    @click="openAddProductModal()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-xs sm:text-sm shadow transition">
                <span>+ Tambah Menu Baru</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-cream-dark p-5 shadow-xs overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-cream-dark text-[11px] font-extrabold uppercase tracking-wider text-espresso/60 bg-cream-light">
                    <th class="py-3.5 px-4 rounded-tl-xl">Nama Menu &amp; Komposisi</th>
                    <th class="py-3.5 px-4">Kategori</th>
                    <th class="py-3.5 px-4 text-right">Harga Jual</th>
                    <th class="py-3.5 px-4 text-right">Modal / HPP</th>
                    <th class="py-3.5 px-4 text-right">Margin Laba / Cup</th>
                    <th class="py-3.5 px-4 text-center">Status (Tersedia / Habis)</th>
                    <th class="py-3.5 px-4 text-center rounded-tr-xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cream-dark text-xs sm:text-sm">
                <template x-for="prod in filteredAdminProducts" :key="prod.id">
                    <tr class="hover:bg-cream-light/60 transition">
                        <td class="py-3.5 px-4">
                            <div class="font-extrabold text-espresso" x-text="prod.name"></div>
                            <div class="text-xs text-espresso/60 italic" x-text="prod.description || '-'"></div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cream text-coffee"
                                  x-text="prod.category ? prod.category.name : '-'"></span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-extrabold text-espresso"
                            x-text="formatRupiah(prod.price)"></td>
                        <td class="py-3.5 px-4 text-right font-mono font-semibold text-coffee"
                            x-text="formatRupiah(prod.cost_price)"></td>
                        <td class="py-3.5 px-4 text-right font-mono">
                            <span class="font-extrabold text-emerald-700" x-text="formatRupiah(prod.price - prod.cost_price)"></span>
                            <span class="block text-[10px] text-espresso/55"
                                  x-text="prod.price > 0 ? Math.round(((prod.price - prod.cost_price) / prod.price) * 100) + '% margin' : '0%'"></span>
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            <button type="button"
                                    @click="toggleAvailability(prod)"
                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold transition border"
                                    :class="prod.is_available
                                        ? 'bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200'
                                        : 'bg-red-100 text-red-800 border-red-300 hover:bg-red-200'">
                                <span class="w-2.5 h-2.5 rounded-full"
                                      :class="prod.is_available ? 'bg-emerald-600' : 'bg-red-600'"></span>
                                <span x-text="prod.is_available ? 'Tersedia' : 'Habis'"></span>
                            </button>
                        </td>

                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button"
                                        @click="openEditProductModal(prod)"
                                        class="px-3 py-1.5 rounded-lg bg-cream hover:bg-gold text-espresso font-bold text-xs transition">
                                    Edit Harga / HPP
                                </button>

                                <button type="button"
                                        @click="openDeleteModal({
                                            title: 'Hapus Menu dari Katalog?',
                                            itemName: prod.name + ' (' + formatRupiah(prod.price) + ')',
                                            message: 'Menu ini akan dihapus dari daftar katalog Kasir POS Yoursuf Coffee.',
                                            actionUrl: '/admin/products/' + prod.id,
                                            buttonText: 'Ya, Hapus Menu'
                                        })"
                                        class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-700 hover:text-white font-bold text-xs transition">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

</section>

