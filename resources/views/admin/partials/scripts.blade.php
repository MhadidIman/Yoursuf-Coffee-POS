<!-- ================= ALPINE.JS STATE & LOGIC BACKOFFICE ADMIN ================= -->
<script>
    function yoursufAdminApp() {
        return {
            activeTab: @json($activeTab),
            activePeriod: @json($activePeriod),
            customStartDate: @json($customStartDate),
            customEndDate: @json($customEndDate),
            isLoadingCustomStats: false,
            sidebarOpen: false,

            financeStats: @json($financeStats),
            categories: @json($categories),
            products: @json($products),
            expenses: @json($expenses),
            ingredients: @json($ingredients),
            orders: @json($orders),

            expenseCategoryFilter: 'Semua',
            productCategoryFilter: 'all',
            productSearch: '',
            orderStatusFilter: 'all',
            orderSearch: '',

            productModal: {
                open: false,
                isEdit: false,
                form: {
                    id: null,
                    category_id: '',
                    name: '',
                    description: '',
                    price: 22000,
                    cost_price: 11000,
                    is_available: true,
                }
            },

            expenseModal: {
                open: false,
                form: {
                    id: null,
                    title: '',
                    category: 'Bahan Baku',
                    amount: 0,
                    expense_date: '',
                    notes: '',
                }
            },

            ingredientModal: {
                open: false,
                form: {
                    id: null,
                    name: '',
                    unit: 'ml',
                    current_stock: 0,
                    min_stock_alert: 0,
                }
            },

            confirmDeleteModal: {
                open: false,
                title: '',
                itemName: '',
                message: '',
                actionUrl: '',
                buttonText: 'Ya, Hapus Sekarang',
            },

            toast: {
                visible: false,
                message: '',
                type: 'success',
            },

            switchTab(tab) {
                this.activeTab = tab;
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            },

            showToast(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.visible = true;
                setTimeout(() => {
                    this.toast.visible = false;
                }, 3000);
            },

            get currentStats() {
                return this.financeStats[this.activePeriod] || this.financeStats.today;
            },

            get exportCsvUrl() {
                const params = new URLSearchParams({
                    period: this.activePeriod,
                });
                if (this.activePeriod === 'custom') {
                    params.set('start_date', this.customStartDate);
                    params.set('end_date', this.customEndDate);
                }
                return `{{ route('admin.finance.export-csv') }}?` + params.toString();
            },

            async fetchCustomFinanceStats() {
                if (!this.customStartDate || !this.customEndDate) {
                    this.showToast('Pilih tanggal mulai dan tanggal akhir terlebih dahulu.', 'error');
                    return;
                }

                this.isLoadingCustomStats = true;
                try {
                    const params = new URLSearchParams({
                        start_date: this.customStartDate,
                        end_date: this.customEndDate,
                    });

                    const response = await fetch(`{{ route('admin.finance.custom-stats') }}?` + params.toString(), {
                        headers: {
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal memuat laporan rentang tanggal.');
                    }

                    this.customStartDate = data.start_date;
                    this.customEndDate = data.end_date;
                    this.financeStats.custom = data.stats;
                    this.activePeriod = 'custom';
                    this.showToast('Data laporan keuangan berhasil diperbarui!');
                } catch (err) {
                    this.showToast(err.message || 'Gagal memuat data rentang tanggal.', 'error');
                } finally {
                    this.isLoadingCustomStats = false;
                }
            },

            printFinanceReport() {
                window.print();
            },

            get activeTabTitle() {
                const titles = {
                    finance: 'Dashboard Keuangan & Laba Bersih',
                    expenses: 'Pencatatan Pengeluaran (Expense Tracker)',
                    products: 'Manajemen Menu & HPP Yoursuf Coffee',
                    ingredients: 'Pengingat Stok Bahan Utama di Kos',
                    orders: 'Riwayat & Pembatalan Transaksi (Void Order)',
                };
                return titles[this.activeTab] || 'Admin Backoffice';
            },

            get activeTabSubtitle() {
                const subtitles = {
                    finance: 'Ringkasan omzet penjualan, pengeluaran belanja, laba kotor, dan laba bersih kas',
                    expenses: 'Pisahkan catatan belanja bahan baku & kemasan usaha dari uang pribadi',
                    products: 'Kelola daftar harga jual, estimasi modal HPP per cup, dan status Tersedia / Habis',
                    ingredients: 'Pantau sisa stok bahan utama beserta peringatan merah jika stok menipis',
                    orders: 'Lihat riwayat nota penjualan atau batalkan (void) transaksi yang salah input',
                };
                return subtitles[this.activeTab] || '';
            },

            get cashPercentage() {
                const total = Number(this.currentStats.total_omzet) || 0;
                if (total === 0) return 0;
                return Math.round(((Number(this.currentStats.total_cash) || 0) / total) * 100);
            },

            get qrisPercentage() {
                const total = Number(this.currentStats.total_omzet) || 0;
                if (total === 0) return 0;
                return Math.round(((Number(this.currentStats.total_qris) || 0) / total) * 100);
            },

            get filteredExpenses() {
                if (this.expenseCategoryFilter === 'Semua') {
                    return this.expenses;
                }
                return this.expenses.filter(e => e.category === this.expenseCategoryFilter);
            },

            get filteredExpensesTotal() {
                return this.filteredExpenses.reduce((sum, e) => sum + Number(e.amount), 0);
            },

            get filteredAdminProducts() {
                return this.products.filter(p => {
                    const matchCat = this.productCategoryFilter === 'all' || Number(p.category_id) === Number(this.productCategoryFilter);
                    const q = this.productSearch.trim().toLowerCase();
                    const matchSearch = !q ||
                        p.name.toLowerCase().includes(q) ||
                        (p.description && p.description.toLowerCase().includes(q));
                    return matchCat && matchSearch;
                });
            },

            get filteredAdminOrders() {
                return this.orders.filter(o => {
                    const matchStatus = this.orderStatusFilter === 'all' || o.status === this.orderStatusFilter;
                    const q = this.orderSearch.trim().toLowerCase();
                    const matchSearch = !q ||
                        (o.order_number && o.order_number.toLowerCase().includes(q)) ||
                        (o.customer_name && o.customer_name.toLowerCase().includes(q));
                    return matchStatus && matchSearch;
                });
            },

            get lowStockCount() {
                return this.ingredients.filter(i => Number(i.current_stock) <= Number(i.min_stock_alert)).length;
            },

            async toggleAvailability(product) {
                try {
                    const response = await fetch(`/admin/products/${product.id}/toggle`, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal mengubah status menu.');
                    }

                    product.is_available = data.product.is_available;
                    this.showToast(data.message);
                } catch (err) {
                    this.showToast(err.message || 'Terjadi kesalahan.', 'error');
                }
            },

            openAddProductModal() {
                this.productModal.isEdit = false;
                this.productModal.form = {
                    id: null,
                    category_id: this.categories.length > 0 ? this.categories[0].id : '',
                    name: '',
                    description: '',
                    price: 22000,
                    cost_price: 11000,
                    is_available: true,
                };
                this.productModal.open = true;
            },

            openEditProductModal(prod) {
                this.productModal.isEdit = true;
                this.productModal.form = {
                    id: prod.id,
                    category_id: prod.category_id,
                    name: prod.name,
                    description: prod.description || '',
                    price: Number(prod.price),
                    cost_price: Number(prod.cost_price),
                    is_available: Boolean(prod.is_available),
                };
                this.productModal.open = true;
            },

            openEditExpenseModal(exp) {
                this.expenseModal.form = {
                    id: exp.id,
                    title: exp.title,
                    category: exp.category,
                    amount: Number(exp.amount),
                    expense_date: (exp.expense_date || '').slice(0, 10),
                    notes: exp.notes || '',
                };
                this.expenseModal.open = true;
            },

            openEditIngredientModal(ing) {
                this.ingredientModal.form = {
                    id: ing.id,
                    name: ing.name,
                    unit: ing.unit,
                    current_stock: Number(ing.current_stock),
                    min_stock_alert: Number(ing.min_stock_alert),
                };
                this.ingredientModal.open = true;
            },

            openDeleteModal(config) {
                this.confirmDeleteModal.title = config.title || 'Konfirmasi Hapus Data';
                this.confirmDeleteModal.itemName = config.itemName || '-';
                this.confirmDeleteModal.message = config.message || 'Apakah Anda yakin ingin menghapus data ini?';
                this.confirmDeleteModal.actionUrl = config.actionUrl || '';
                this.confirmDeleteModal.buttonText = config.buttonText || 'Ya, Hapus Sekarang';
                this.confirmDeleteModal.open = true;
            },

            formatRupiah(value) {
                const number = Number(value) || 0;
                const prefix = number < 0 ? '-Rp ' : 'Rp ';
                return prefix + Math.abs(number).toLocaleString('id-ID');
            },

            formatShortDate(dateStr) {
                if (!dateStr) return '-';
                const d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            },

            formatDateTime(dateStr) {
                if (!dateStr) return '-';
                const d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) + ' ' + d.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        };
    }
</script>

