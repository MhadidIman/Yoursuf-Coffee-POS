<!-- =================================================================== -->
<!-- MODAL TAMBAH / EDIT MENU & HPP                                      -->
<!-- =================================================================== -->
<div x-show="productModal.open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-espresso-deep/75 backdrop-blur-xs p-4 overflow-y-auto">
    <div @click.away="productModal.open = false"
         class="bg-white rounded-2xl border-2 border-gold shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="bg-espresso text-cream px-5 py-4 flex items-center justify-between border-b border-gold/30">
            <h3 class="text-base font-extrabold" x-text="productModal.isEdit ? 'Edit Menu & HPP — ' + productModal.form.name : 'Tambah Menu Baru Yoursuf Coffee'"></h3>
            <button type="button" @click="productModal.open = false" class="text-cream/70 hover:text-gold">✕</button>
        </div>

        <form :action="productModal.isEdit ? '/admin/products/' + productModal.form.id : '{{ route('admin.products.store') }}'"
              method="POST"
              class="p-5 space-y-4">
            @csrf
            <template x-if="productModal.isEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Kategori Menu</label>
                <select name="category_id"
                        x-model="productModal.form.category_id"
                        required
                        class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm text-espresso outline-none">
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Nama Menu</label>
                <input type="text"
                       name="name"
                       x-model="productModal.form.name"
                       required
                       placeholder="Contoh: Butterscotch Latte"
                       class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm text-espresso outline-none">
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Komposisi / Deskripsi</label>
                <textarea name="description"
                          x-model="productModal.form.description"
                          rows="2"
                          placeholder="Contoh: espresso, magic milk, and butterscotch syrup"
                          class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-xs text-espresso outline-none"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Harga Jual (Rp)</label>
                    <input type="number"
                           name="price"
                           min="0"
                           x-model.number="productModal.form.price"
                           required
                           class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm font-mono font-bold text-espresso outline-none">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-coffee">Modal HPP / Cup (Rp)</label>
                        <button type="button"
                                @click="productModal.form.cost_price = Math.round((Number(productModal.form.price) || 0) * 0.5)"
                                class="text-[10px] font-bold text-coffee underline">
                            Set 50% Otomatis
                        </button>
                    </div>
                    <input type="number"
                           name="cost_price"
                           min="0"
                           x-model.number="productModal.form.cost_price"
                           required
                           class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm font-mono font-bold text-coffee outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between p-3 rounded-xl bg-cream/50 border border-cream-dark">
                <span class="text-xs font-bold text-espresso">Status Ketersediaan Menu</span>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_available" value="0">
                    <input type="checkbox"
                           name="is_available"
                           value="1"
                           x-model="productModal.form.is_available"
                           class="rounded border-cream-dark text-coffee focus:ring-coffee">
                    <span class="text-xs font-extrabold"
                          :class="productModal.form.is_available ? 'text-emerald-700' : 'text-red-600'"
                          x-text="productModal.form.is_available ? 'Tersedia (Bisa Dipesan)' : 'Habis'"></span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button"
                        @click="productModal.open = false"
                        class="px-4 py-2 rounded-xl bg-cream hover:bg-cream-dark text-espresso font-bold text-xs">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-xs shadow">
                    Simpan Menu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =================================================================== -->
<!-- MODAL EDIT PENGELUARAN (EXPENSE)                                    -->
<!-- =================================================================== -->
<div x-show="expenseModal.open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-espresso-deep/75 backdrop-blur-xs p-4 overflow-y-auto">
    <div @click.away="expenseModal.open = false"
         class="bg-white rounded-2xl border-2 border-gold shadow-2xl max-w-md w-full overflow-hidden">
        <div class="bg-espresso text-cream px-5 py-4 flex items-center justify-between border-b border-gold/30">
            <h3 class="text-base font-extrabold">Edit Catatan Pengeluaran</h3>
            <button type="button" @click="expenseModal.open = false" class="text-cream/70 hover:text-gold">✕</button>
        </div>

        <form :action="'/admin/expenses/' + expenseModal.form.id" method="POST" class="p-5 space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Nama Pengeluaran</label>
                <input type="text"
                       name="title"
                       x-model="expenseModal.form.title"
                       required
                       class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm text-espresso outline-none">
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Kategori</label>
                <select name="category"
                        x-model="expenseModal.form.category"
                        required
                        class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm text-espresso outline-none">
                    <option value="Bahan Baku">Bahan Baku</option>
                    <option value="Kemasan">Kemasan</option>
                    <option value="Operasional">Operasional</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Nominal (Rp)</label>
                    <input type="number"
                           name="amount"
                           min="1"
                           x-model.number="expenseModal.form.amount"
                           required
                           class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm font-mono font-bold text-espresso outline-none">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Tanggal</label>
                    <input type="date"
                           name="expense_date"
                           x-model="expenseModal.form.expense_date"
                           required
                           class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm text-espresso outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Catatan</label>
                <textarea name="notes"
                          x-model="expenseModal.form.notes"
                          rows="2"
                          class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-xs text-espresso outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button"
                        @click="expenseModal.open = false"
                        class="px-4 py-2 rounded-xl bg-cream hover:bg-cream-dark text-espresso font-bold text-xs">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-xs shadow">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =================================================================== -->
<!-- MODAL UPDATE STOK BAHAN UTAMA (INGREDIENT)                          -->
<!-- =================================================================== -->
<div x-show="ingredientModal.open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-espresso-deep/75 backdrop-blur-xs p-4 overflow-y-auto">
    <div @click.away="ingredientModal.open = false"
         class="bg-white rounded-2xl border-2 border-gold shadow-2xl max-w-md w-full overflow-hidden">
        <div class="bg-espresso text-cream px-5 py-4 flex items-center justify-between border-b border-gold/30">
            <h3 class="text-base font-extrabold" x-text="'Update Stok — ' + ingredientModal.form.name"></h3>
            <button type="button" @click="ingredientModal.open = false" class="text-cream/70 hover:text-gold">✕</button>
        </div>

        <form :action="'/admin/ingredients/' + ingredientModal.form.id" method="POST" class="p-5 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Nama Bahan</label>
                <input type="text"
                       name="name"
                       x-model="ingredientModal.form.name"
                       required
                       class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm text-espresso outline-none">
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Satuan (Unit)</label>
                <select name="unit"
                        x-model="ingredientModal.form.unit"
                        required
                        class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm text-espresso outline-none">
                    <option value="ml">Milliliter (ml)</option>
                    <option value="gr">Gram (gr)</option>
                    <option value="pcs">Pieces / Buah (pcs)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">Sisa Stok Saat Ini</label>
                    <input type="number"
                           name="current_stock"
                           min="0"
                           x-model.number="ingredientModal.form.current_stock"
                           required
                           class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm font-mono font-extrabold text-espresso outline-none">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-red-700 mb-1">Batas Minimum Alert</label>
                    <input type="number"
                           name="min_stock_alert"
                           min="0"
                           x-model.number="ingredientModal.form.min_stock_alert"
                           required
                           class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark text-sm font-mono font-bold text-red-700 outline-none">
                </div>
            </div>

            <div class="p-3 rounded-xl bg-cream/50 border border-cream-dark space-y-1.5">
                <span class="text-[11px] font-bold text-espresso block">Penyesuaian Cepat Stok:</span>
                <div class="grid grid-cols-4 gap-1.5">
                    <button type="button"
                            @click="ingredientModal.form.current_stock = Math.max(0, Number(ingredientModal.form.current_stock) - 50)"
                            class="py-1 rounded bg-white border border-cream-dark text-xs font-mono font-bold text-red-700 hover:bg-red-50">
                        -50
                    </button>
                    <button type="button"
                            @click="ingredientModal.form.current_stock = Math.max(0, Number(ingredientModal.form.current_stock) - 10)"
                            class="py-1 rounded bg-white border border-cream-dark text-xs font-mono font-bold text-red-700 hover:bg-red-50">
                        -10
                    </button>
                    <button type="button"
                            @click="ingredientModal.form.current_stock = Number(ingredientModal.form.current_stock) + 50"
                            class="py-1 rounded bg-white border border-cream-dark text-xs font-mono font-bold text-emerald-700 hover:bg-emerald-50">
                        +50
                    </button>
                    <button type="button"
                            @click="ingredientModal.form.current_stock = Number(ingredientModal.form.current_stock) + 100"
                            class="py-1 rounded bg-white border border-cream-dark text-xs font-mono font-bold text-emerald-700 hover:bg-emerald-50">
                        +100
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button"
                        @click="ingredientModal.open = false"
                        class="px-4 py-2 rounded-xl bg-cream hover:bg-cream-dark text-espresso font-bold text-xs">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-xs shadow">
                    Simpan Stok
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =================================================================== -->
<!-- MODAL VERIFIKASI HAPUS / VOID CUSTOM LANGSUNG DARI WEB              -->
<!-- =================================================================== -->
<div x-show="confirmDeleteModal.open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-espresso-deep/80 backdrop-blur-xs p-4">

    <div @click.away="confirmDeleteModal.open = false"
         class="bg-white rounded-2xl border-2 border-red-400 shadow-2xl max-w-md w-full overflow-hidden">

        <!-- Header Modal Konfirmasi -->
        <div class="bg-espresso px-5 py-4 flex items-center justify-between border-b border-gold/30">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center font-extrabold text-sm shrink-0">
                    !
                </div>
                <h3 class="text-sm sm:text-base font-extrabold text-cream" x-text="confirmDeleteModal.title"></h3>
            </div>
            <button type="button"
                    @click="confirmDeleteModal.open = false"
                    class="text-cream/70 hover:text-gold text-sm">
                ✕
            </button>
        </div>

        <!-- Isi Pesan Verifikasi -->
        <div class="p-5 space-y-3 bg-cream-light">
            <div class="p-3.5 rounded-xl bg-white border border-cream-dark">
                <span class="text-[10px] font-bold uppercase tracking-wider text-espresso/50 block">Data yang dipilih:</span>
                <p class="text-sm font-extrabold text-espresso mt-0.5" x-text="confirmDeleteModal.itemName"></p>
            </div>

            <p class="text-xs text-espresso/80 leading-relaxed" x-text="confirmDeleteModal.message"></p>

            <!-- Tombol Aksi Batal / Konfirmasi Hapus -->
            <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-cream-dark">
                <button type="button"
                        @click="confirmDeleteModal.open = false"
                        class="px-4 py-2 rounded-xl bg-cream hover:bg-cream-dark text-espresso font-bold text-xs transition">
                    Batal
                </button>

                <form :action="confirmDeleteModal.actionUrl" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span x-text="confirmDeleteModal.buttonText"></span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

