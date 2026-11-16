<!-- =================================================================== -->
<!-- TAB 4: PENGINGAT STOK BAHAN UTAMA (INGREDIENTS)                     -->
<!-- =================================================================== -->
<section x-show="activeTab === 'ingredients'" x-cloak class="space-y-6">

    <div x-show="lowStockCount > 0"
         class="p-4 rounded-2xl bg-red-900 text-white border-2 border-red-400 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center font-extrabold text-lg shrink-0">
                !
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-extrabold">
                    Peringatan: Ada <span class="underline text-amber-300" x-text="lowStockCount"></span> Bahan Baku di Bawah Batas Minimum!
                </h3>
                <p class="text-xs text-red-100">
                    Segera lakukan restock bahan yang ditandai warna merah agar operasional seduh di kos tidak terhambat.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Form Tambah Bahan Utama -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-cream-dark p-5 shadow-xs">
            <div class="pb-3 mb-4 border-b border-cream-dark">
                <h3 class="text-base font-extrabold text-espresso">Tambah Bahan Dipantau</h3>
                <p class="text-xs text-espresso/65 mt-0.5">
                    Stok <span class="font-bold text-coffee">Cup 16oz</span> dan <span class="font-bold text-coffee">Lid (Tutup Cup)</span> otomatis berkurang saat transaksi di Kasir POS.
                </p>
            </div>

            <form action="{{ route('admin.ingredients.store') }}" method="POST" class="space-y-3.5">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">
                        Nama Bahan Baku / Kemasan
                    </label>
                    <input type="text"
                           name="name"
                           required
                           placeholder="Misal: Magic Milk / Espresso Beans"
                           class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-sm text-espresso outline-none">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">
                        Satuan Takar (Unit)
                    </label>
                    <select name="unit"
                            required
                            class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-sm text-espresso outline-none">
                        <option value="ml">Milliliter (ml)</option>
                        <option value="gr">Gram (gr)</option>
                        <option value="pcs">Pieces / Buah (pcs)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">
                            Stok Saat Ini
                        </label>
                        <input type="number"
                               name="current_stock"
                               min="0"
                               required
                               placeholder="1500"
                               class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-sm font-mono font-bold text-espresso outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-red-700 mb-1">
                            Batas Alert Minimum
                        </label>
                        <input type="number"
                               name="min_stock_alert"
                               min="0"
                               required
                               placeholder="300"
                               class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-sm font-mono font-bold text-red-700 outline-none">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-sm shadow transition flex items-center justify-center gap-2">
                    <span>+ Tambah ke Daftar Stok</span>
                </button>
            </form>
        </div>

        <!-- Daftar Stok Bahan Utama -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-cream-dark p-5 shadow-xs space-y-4">
            <div class="pb-3 border-b border-cream-dark flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-espresso">Daftar Pantauan Stok Bahan di Kos</h3>
                    <p class="text-xs text-espresso/65">
                        Baris berwarna <span class="font-bold text-red-600">Merah</span> menandakan stok berada di bawah atau sama dengan batas minimum (`min_stock_alert`).
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-cream-dark text-[11px] font-extrabold uppercase tracking-wider text-espresso/60 bg-cream-light">
                            <th class="py-3 px-3.5 rounded-tl-xl">Nama Bahan</th>
                            <th class="py-3 px-3.5 text-right">Sisa Stok Saat Ini</th>
                            <th class="py-3 px-3.5 text-right">Batas Minimum</th>
                            <th class="py-3 px-3.5 text-center">Status Stok</th>
                            <th class="py-3 px-3.5 text-center rounded-tr-xl">Update / Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-xs sm:text-sm">
                        <template x-for="ing in ingredients" :key="ing.id">
                            <tr :class="Number(ing.current_stock) <= Number(ing.min_stock_alert)
                                    ? 'bg-red-50/90 hover:bg-red-100/80'
                                    : 'hover:bg-cream-light/60'"
                                class="transition">
                                <td class="py-3.5 px-3.5">
                                    <div class="font-extrabold flex items-center gap-2"
                                         :class="Number(ing.current_stock) <= Number(ing.min_stock_alert) ? 'text-red-900' : 'text-espresso'">
                                        <span x-text="ing.name"></span>
                                        <template x-if="ing.name.toLowerCase().includes('cup') || ing.name.toLowerCase().includes('lid')">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gold/30 text-espresso border border-gold">
                                                Auto-Deduct POS
                                            </span>
                                        </template>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-extrabold text-sm"
                                    :class="Number(ing.current_stock) <= Number(ing.min_stock_alert) ? 'text-red-700' : 'text-espresso'">
                                    <span x-text="ing.current_stock.toLocaleString('id-ID')"></span>
                                    <span class="text-xs font-normal uppercase" x-text="ing.unit"></span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-xs text-espresso/65">
                                    <span x-text="ing.min_stock_alert.toLocaleString('id-ID')"></span>
                                    <span class="uppercase" x-text="ing.unit"></span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <template x-if="Number(ing.current_stock) <= Number(ing.min_stock_alert)">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-red-600 text-white shadow-xs">
                                            <span>⚠ STOK MENIPIS</span>
                                        </span>
                                    </template>
                                    <template x-if="Number(ing.current_stock) > Number(ing.min_stock_alert)">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span>✓ Stok Aman</span>
                                        </span>
                                    </template>
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                @click="openEditIngredientModal(ing)"
                                                class="px-3 py-1.5 rounded-lg bg-espresso hover:bg-coffee text-gold font-bold text-xs transition">
                                            Update Stok
                                        </button>

                                        <button type="button"
                                                @click="openDeleteModal({
                                                    title: 'Hapus Bahan dari Pantauan Stok?',
                                                    itemName: ing.name + ' (Sisa: ' + ing.current_stock + ' ' + ing.unit + ')',
                                                    message: 'Bahan baku ini akan dihapus dari daftar pengingat stok di kos.',
                                                    actionUrl: '/admin/ingredients/' + ing.id,
                                                    buttonText: 'Ya, Hapus Bahan'
                                                })"
                                                class="px-2.5 py-1.5 rounded-lg bg-red-100 hover:bg-red-600 text-red-700 hover:text-white font-bold text-xs transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>

