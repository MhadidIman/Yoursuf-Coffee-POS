<!-- =================================================================== -->
<!-- TAB 2: PENCATATAN PENGELUARAN (EXPENSE TRACKER)                     -->
<!-- =================================================================== -->
<section x-show="activeTab === 'expenses'" x-cloak class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Form Cepat Input Belanja Bahan -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-cream-dark p-5 shadow-xs">
            <div class="pb-3 mb-4 border-b border-cream-dark">
                <h3 class="text-base font-extrabold text-espresso">Catat Pengeluaran Usaha</h3>
                <p class="text-xs text-espresso/65 mt-0.5">
                    Pisahkan belanja bahan kos agar tidak tercampur dengan uang pribadi.
                </p>
            </div>

            <form action="{{ route('admin.expenses.store') }}" method="POST" class="space-y-3.5">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">
                        Nama Pengeluaran / Belanja
                    </label>
                    <input type="text"
                           name="title"
                           required
                           placeholder="Misal: Beli Susu Magic Milk & Sirup"
                           class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-sm text-espresso outline-none">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">
                        Kategori Pengeluaran
                    </label>
                    <select name="category"
                            required
                            class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-sm text-espresso outline-none">
                        <option value="Bahan Baku">Bahan Baku (Kopi, Susu, Sirup, Es)</option>
                        <option value="Kemasan">Kemasan (Cup 16oz, Lid, Sedotan, Plastik)</option>
                        <option value="Operasional">Operasional (Listrik Kos, Gas, Kuota)</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">
                            Nominal (Rp)
                        </label>
                        <input type="number"
                               name="amount"
                               min="1"
                               required
                               placeholder="Contoh: 19500"
                               class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-sm font-mono font-bold text-espresso outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">
                            Tanggal Belanja
                        </label>
                        <input type="date"
                               name="expense_date"
                               value="{{ now()->toDateString() }}"
                               required
                               class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-sm text-espresso outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea name="notes"
                              rows="2"
                              placeholder="Toko tempat beli / rincian nota..."
                              class="w-full px-3.5 py-2 rounded-xl bg-cream-light border border-cream-dark focus:border-coffee text-xs text-espresso outline-none"></textarea>
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-espresso hover:bg-coffee text-gold font-extrabold text-sm shadow transition flex items-center justify-center gap-2">
                    <span>+ Simpan Catatan Pengeluaran</span>
                </button>
            </form>
        </div>

        <!-- Tabel Riwayat Pengeluaran -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-cream-dark p-5 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-cream-dark">
                <div>
                    <h3 class="text-base font-extrabold text-espresso">Riwayat Pengeluaran Belanja</h3>
                    <p class="text-xs text-espresso/65">
                        Total tercatat: <span class="font-mono font-extrabold text-red-700" x-text="formatRupiah(filteredExpensesTotal)"></span>
                    </p>
                </div>

                <div class="flex items-center gap-1.5 flex-wrap">
                    <template x-for="catOpt in ['Semua', 'Bahan Baku', 'Kemasan', 'Operasional', 'Lainnya']" :key="catOpt">
                        <button type="button"
                                @click="expenseCategoryFilter = catOpt"
                                :class="expenseCategoryFilter === catOpt
                                    ? 'bg-coffee text-cream font-bold'
                                    : 'bg-cream-light text-espresso hover:bg-cream font-medium'"
                                class="px-3 py-1 rounded-lg text-xs border border-cream-dark transition"
                                x-text="catOpt"></button>
                    </template>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-cream-dark text-[11px] font-extrabold uppercase tracking-wider text-espresso/60 bg-cream-light">
                            <th class="py-3 px-3 rounded-tl-xl">Tanggal</th>
                            <th class="py-3 px-3">Nama Pengeluaran</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3 text-right">Nominal</th>
                            <th class="py-3 px-3 text-center rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-xs sm:text-sm">
                        <template x-for="exp in filteredExpenses" :key="exp.id">
                            <tr class="hover:bg-cream-light/60 transition">
                                <td class="py-3 px-3 font-mono text-xs text-espresso/75 whitespace-nowrap"
                                    x-text="formatShortDate(exp.expense_date)"></td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-espresso" x-text="exp.title"></div>
                                    <div x-show="exp.notes" class="text-xs text-espresso/60 italic" x-text="exp.notes"></div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cream text-coffee"
                                          x-text="exp.category"></span>
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-extrabold text-red-700 whitespace-nowrap"
                                    x-text="formatRupiah(exp.amount)"></td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                @click="openEditExpenseModal(exp)"
                                                class="px-2.5 py-1 rounded-lg bg-cream hover:bg-gold text-espresso font-bold text-xs transition">
                                            Edit
                                        </button>
                                        <button type="button"
                                                @click="openDeleteModal({
                                                    title: 'Hapus Catatan Pengeluaran?',
                                                    itemName: exp.title + ' (' + formatRupiah(exp.amount) + ')',
                                                    message: 'Catatan pengeluaran belanja ini akan dihapus secara permanen dari laporan keuangan.',
                                                    actionUrl: '/admin/expenses/' + exp.id,
                                                    buttonText: 'Ya, Hapus Pengeluaran'
                                                })"
                                                class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-600 text-red-700 hover:text-white font-bold text-xs transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div x-show="filteredExpenses.length === 0" class="py-10 text-center text-xs text-espresso/50 italic">
                    Belum ada catatan pengeluaran pada kategori ini.
                </div>
            </div>
        </div>

    </div>

</section>

