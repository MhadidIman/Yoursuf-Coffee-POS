<!-- ================= ALPINE.JS STATE & LOGIC KASIR POS ================= -->
<script>
    function yoursufPosApp() {
        return {
            activeMainTab: 'catalog',
            selectedCategoryId: 'all',
            searchQuery: '',
            currentTime: '',

            categories: @json($categories),
            products: @json($products),
            orders: @json($orders),

            cupStockCount: {{ $cupStock ? (int) $cupStock->current_stock : 0 }},
            lidStockCount: {{ $lidStock ? (int) $lidStock->current_stock : 0 }},

            cart: [],
            clearCartModal: false,
            isSubmitting: false,

            orderForm: {
                order_type: 'takeaway',
                customer_name: '',
                customer_wa: '',
                delivery_area: 'UMY',
                delivery_fee: 0,
                discount_amount: 0,
                payment_method: 'cash',
                cash_received: 0,
                notes: '',
            },

            receiptModal: {
                open: false,
                order: null,
                paperSize: '58mm',
                waPhone: '',
                copied: false,
            },

            toast: {
                visible: false,
                message: '',
                type: 'success',
            },

            initPos() {
                this.updateClock();
                setInterval(() => this.updateClock(), 1000);
            },

            updateClock() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                }) + ' WIB';
            },

            showToast(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.visible = true;
                setTimeout(() => {
                    this.toast.visible = false;
                }, 3400);
            },

            get filteredProducts() {
                return this.products.filter(product => {
                    const matchCategory = this.selectedCategoryId === 'all' || Number(product.category_id) === Number(this.selectedCategoryId);
                    const q = this.searchQuery.trim().toLowerCase();
                    const matchSearch = !q ||
                        product.name.toLowerCase().includes(q) ||
                        (product.description && product.description.toLowerCase().includes(q));
                    return matchCategory && matchSearch;
                });
            },

            countProductsInCategory(categoryId) {
                return this.products.filter(p => Number(p.category_id) === Number(categoryId)).length;
            },

            getProductCartQty(productId) {
                return this.cart
                    .filter(item => Number(item.product_id) === Number(productId))
                    .reduce((sum, item) => sum + Number(item.quantity), 0);
            },

            addToCart(product) {
                if (!product.is_available) {
                    this.showToast('Maaf, produk ini sedang habis.', 'error');
                    return;
                }

                const existingIndex = this.cart.findIndex(
                    item => Number(item.product_id) === Number(product.id) && (!item.item_notes || item.item_notes.trim() === '')
                );

                if (existingIndex !== -1) {
                    this.cart[existingIndex].quantity += 1;
                } else {
                    this.cart.push({
                        cart_key: product.id + '-' + Date.now() + '-' + Math.random().toString(36).slice(2, 6),
                        product_id: product.id,
                        name: product.name,
                        price: Number(product.price),
                        quantity: 1,
                        item_notes: '',
                    });
                }

                this.syncDefaultCashReceived();
            },

            incrementQty(index) {
                this.cart[index].quantity += 1;
                this.syncDefaultCashReceived();
            },

            decrementQty(index) {
                if (this.cart[index].quantity > 1) {
                    this.cart[index].quantity -= 1;
                } else {
                    this.cart.splice(index, 1);
                }
                this.syncDefaultCashReceived();
            },

            clearCart() {
                this.cart = [];
                this.orderForm.discount_amount = 0;
                this.orderForm.cash_received = 0;
            },

            toggleItemNotePreset(item, preset) {
                const current = (item.item_notes || '').trim();
                if (!current) {
                    item.item_notes = preset;
                    return;
                }

                const parts = current.split(',').map(s => s.trim()).filter(Boolean);
                const idx = parts.findIndex(p => p.toLowerCase() === preset.toLowerCase());

                if (idx !== -1) {
                    parts.splice(idx, 1);
                } else {
                    parts.push(preset);
                }

                item.item_notes = parts.join(', ');
            },

            setOrderType(type) {
                this.orderForm.order_type = type;
                if (type === 'takeaway') {
                    this.orderForm.delivery_fee = 0;
                } else {
                    if (!this.orderForm.delivery_area) {
                        this.orderForm.delivery_area = 'UMY';
                    }
                    this.selectDeliveryArea(this.orderForm.delivery_area);
                }
                this.syncDefaultCashReceived();
            },

            selectDeliveryArea(area) {
                this.orderForm.delivery_area = area;
                const freeCampusAreas = ['UMY', 'UAA', 'UNJAYA', 'Krapyak'];
                if (freeCampusAreas.includes(area)) {
                    this.orderForm.delivery_fee = 0;
                } else if (Number(this.orderForm.delivery_fee) === 0) {
                    this.orderForm.delivery_fee = 5000;
                }
                this.syncDefaultCashReceived();
            },

            setDiscount(amount) {
                this.orderForm.discount_amount = Number(amount) || 0;
                this.syncDefaultCashReceived();
            },

            setPaymentMethod(method) {
                this.orderForm.payment_method = method;
                this.syncDefaultCashReceived();
            },

            syncDefaultCashReceived() {
                if (this.orderForm.payment_method === 'cash') {
                    this.orderForm.cash_received = this.grandTotal;
                }
            },

            get isFreeDeliveryArea() {
                return ['UMY', 'UAA', 'UNJAYA', 'Krapyak'].includes(this.orderForm.delivery_area);
            },

            get totalCartQty() {
                return this.cart.reduce((sum, item) => sum + Number(item.quantity), 0);
            },

            get cartSubtotal() {
                return this.cart.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0);
            },

            get effectiveDeliveryFee() {
                if (this.orderForm.order_type !== 'delivery') {
                    return 0;
                }
                if (this.isFreeDeliveryArea) {
                    return 0;
                }
                return Math.max(0, Number(this.orderForm.delivery_fee) || 0);
            },

            get effectiveDiscount() {
                const raw = Math.max(0, Number(this.orderForm.discount_amount) || 0);
                return Math.min(raw, this.cartSubtotal + this.effectiveDeliveryFee);
            },

            get grandTotal() {
                return Math.max(0, (this.cartSubtotal + this.effectiveDeliveryFee) - this.effectiveDiscount);
            },

            get changeAmount() {
                if (this.orderForm.payment_method !== 'cash') {
                    return 0;
                }
                const received = Number(this.orderForm.cash_received) || 0;
                return received - this.grandTotal;
            },

            get activeQueueCount() {
                return this.orders.filter(o => o.status === 'masuk' || o.status === 'dibuat').length;
            },

            get activeOrdersPreview() {
                return this.orders.filter(o => o.status === 'masuk' || o.status === 'dibuat').slice(0, 3);
            },

            ordersByStatus(status) {
                return this.orders.filter(o => o.status === status);
            },

            async submitCheckout() {
                if (this.cart.length === 0) {
                    this.showToast('Pilih minimal 1 menu terlebih dahulu.', 'error');
                    return;
                }

                if (this.orderForm.payment_method === 'cash' && Number(this.orderForm.cash_received) < this.grandTotal) {
                    this.showToast('Uang tunai yang diterima kurang dari total pembayaran!', 'error');
                    return;
                }

                this.isSubmitting = true;

                try {
                    const response = await fetch('{{ route('pos.orders.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            order_type: this.orderForm.order_type,
                            customer_name: this.orderForm.customer_name,
                            customer_wa: this.orderForm.customer_wa,
                            delivery_area: this.orderForm.order_type === 'delivery' ? this.orderForm.delivery_area : null,
                            delivery_fee: this.effectiveDeliveryFee,
                            discount_amount: this.effectiveDiscount,
                            payment_method: this.orderForm.payment_method,
                            cash_received: this.orderForm.payment_method === 'cash' ? Number(this.orderForm.cash_received) : this.grandTotal,
                            notes: this.orderForm.notes,
                            items: this.cart.map(item => ({
                                product_id: item.product_id,
                                quantity: item.quantity,
                                item_notes: item.item_notes,
                            })),
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal menyimpan pesanan.');
                    }

                    this.orders.unshift(data.order);

                    if (data.cup_stock !== null && data.cup_stock !== undefined) {
                        this.cupStockCount = Number(data.cup_stock);
                    }
                    if (data.lid_stock !== null && data.lid_stock !== undefined) {
                        this.lidStockCount = Number(data.lid_stock);
                    }

                    this.openReceiptModal(data.order);

                    this.cart = [];
                    this.orderForm.customer_name = '';
                    this.orderForm.customer_wa = '';
                    this.orderForm.notes = '';
                    this.orderForm.discount_amount = 0;
                    this.orderForm.cash_received = 0;

                    this.showToast(data.message);
                } catch (err) {
                    this.showToast(err.message || 'Terjadi kesalahan saat menyimpan transaksi.', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            },

            async updateOrderStatus(order, nextStatus) {
                try {
                    const response = await fetch(`/pos/orders/${order.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ status: nextStatus }),
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal mengubah status pesanan.');
                    }

                    const idx = this.orders.findIndex(o => Number(o.id) === Number(order.id));
                    if (idx !== -1) {
                        this.orders[idx] = data.order;
                    }

                    this.showToast(data.message);
                } catch (err) {
                    this.showToast(err.message || 'Gagal memperbarui status.', 'error');
                }
            },

            advanceOrderStatus(order) {
                const nextStatus = order.status === 'masuk' ? 'dibuat' : 'selesai';
                this.updateOrderStatus(order, nextStatus);
            },

            async refreshOrders() {
                try {
                    const response = await fetch('{{ route('pos.orders.index') }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.orders = data.orders;
                        this.showToast('Data antrean berhasil diperbarui.');
                    }
                } catch (e) {
                    this.showToast('Gagal memuat ulang antrean.', 'error');
                }
            },

            openReceiptModal(order) {
                this.receiptModal.order = order;
                this.receiptModal.waPhone = order.customer_wa || '';
                this.receiptModal.copied = false;
                this.receiptModal.open = true;
            },

            printThermalReceipt() {
                window.print();
            },

            orderItemsSubtotal(order) {
                if (!order || !order.items) return 0;
                return order.items.reduce((sum, item) => sum + Number(item.subtotal), 0);
            },

            generateWhatsAppText(order) {
                if (!order) return '';

                const lines = [];
                lines.push('*☕ YOURSUF COFFEE — NOTA PESANAN*');
                lines.push('--------------------------------');
                lines.push(`No. Order : *${order.order_number}*`);
                lines.push(`Waktu     : ${this.formatDateTime(order.created_at)}`);
                lines.push(`Pelanggan : ${order.customer_name || 'Pelanggan Umum'}`);
                if (order.order_type === 'delivery') {
                    lines.push(`Tipe      : *Delivery (${order.delivery_area || 'Kampus'})*`);
                } else {
                    lines.push('Tipe      : *Takeaway (Ambil di Kos)*');
                }
                lines.push('--------------------------------');
                lines.push('*Rincian Menu:*');

                (order.items || []).forEach(item => {
                    lines.push(`• ${item.quantity}x *${item.product_name}* — ${this.formatRupiah(item.subtotal)}`);
                    if (item.item_notes) {
                        lines.push(`  _(Catatan: ${item.item_notes})_`);
                    }
                });

                lines.push('--------------------------------');
                lines.push(`Subtotal  : ${this.formatRupiah(this.orderItemsSubtotal(order))}`);
                if (order.order_type === 'delivery') {
                    const feeText = Number(order.delivery_fee) === 0 ? 'GRATIS (Rp 0)' : this.formatRupiah(order.delivery_fee);
                    lines.push(`Ongkir    : ${feeText}`);
                }
                if (Number(order.discount_amount) > 0) {
                    lines.push(`Diskon    : - ${this.formatRupiah(order.discount_amount)}`);
                }
                lines.push(`*TOTAL    : ${this.formatRupiah(order.total_amount)}*`);
                lines.push(`Metode    : ${(order.payment_method || 'cash').toUpperCase()}`);

                if (order.payment_method === 'cash') {
                    lines.push(`Tunai     : ${this.formatRupiah(order.cash_received)}`);
                    lines.push(`Kembalian : ${this.formatRupiah(order.change_amount)}`);
                }

                if (order.notes) {
                    lines.push('--------------------------------');
                    lines.push(`Catatan   : _${order.notes}_`);
                }

                lines.push('--------------------------------');
                lines.push('Terima kasih sudah ngopi di *Yoursuf Coffee*! 🙏✨');

                return lines.join('\n');
            },

            async copyWhatsAppReceipt() {
                const text = this.generateWhatsAppText(this.receiptModal.order);
                try {
                    await navigator.clipboard.writeText(text);
                    this.receiptModal.copied = true;
                    this.showToast('Struk WhatsApp berhasil disalin ke clipboard!');
                    setTimeout(() => {
                        this.receiptModal.copied = false;
                    }, 2500);
                } catch (e) {
                    this.showToast('Gagal menyalin teks secara otomatis.', 'error');
                }
            },

            sendToWhatsApp() {
                const text = encodeURIComponent(this.generateWhatsAppText(this.receiptModal.order));
                let rawPhone = (this.receiptModal.waPhone || '').replace(/[^0-9]/g, '');

                if (rawPhone.startsWith('0')) {
                    rawPhone = '62' + rawPhone.slice(1);
                }

                const url = rawPhone
                    ? `https://wa.me/${rawPhone}?text=${text}`
                    : `https://wa.me/?text=${text}`;

                window.open(url, '_blank');
            },

            formatRupiah(value) {
                const number = Number(value) || 0;
                return 'Rp ' + number.toLocaleString('id-ID');
            },

            formatTime(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            },

            formatDateTime(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }
        };
    }
</script>

