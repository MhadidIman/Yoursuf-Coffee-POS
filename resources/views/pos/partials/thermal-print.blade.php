<!-- ================= AREA KHUSUS CETAK THERMAL (@media print) ================= -->
<div class="hidden print-only-container">
    <div :class="receiptModal.paperSize === '58mm' ? 'thermal-paper-58mm' : 'thermal-paper-80mm'">
        <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 6px; margin-bottom: 6px;">
            <img src="{{ asset('images/logo-yoursuf.jpg') }}"
                 alt="Yoursuf Coffee"
                 style="width: 52px; height: 52px; margin: 0 auto 4px auto; display: block; object-fit: contain;">
            <div style="font-weight: 800; font-size: 14px;">YOURSUF COFFEE</div>
            <div style="font-size: 10px;">Home Coffee &amp; Campus Delivery</div>
            <div style="font-size: 10px;">UMY - UAA - UNJAYA - Krapyak</div>
        </div>

        <div style="border-bottom: 1px dashed #000; padding-bottom: 6px; margin-bottom: 6px;">
            <div style="display: flex; justify-content: space-between;">
                <span>No:</span>
                <strong x-text="receiptModal.order?.order_number"></strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Waktu:</span>
                <span x-text="formatDateTime(receiptModal.order?.created_at)"></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Tipe:</span>
                <strong x-text="receiptModal.order?.order_type === 'delivery' ? 'DELIVERY (' + (receiptModal.order?.delivery_area || '-') + ')' : 'TAKEAWAY'"></strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Pelanggan:</span>
                <span x-text="receiptModal.order?.customer_name || 'Umum'"></span>
            </div>
        </div>

        <div style="border-bottom: 1px dashed #000; padding-bottom: 6px; margin-bottom: 6px;">
            <template x-for="item in (receiptModal.order?.items || [])" :key="item.id">
                <div style="margin-bottom: 4px;">
                    <div style="font-weight: 700;" x-text="item.product_name"></div>
                    <div style="display: flex; justify-content: space-between;">
                        <span x-text="item.quantity + ' x ' + formatRupiah(item.price)"></span>
                        <span x-text="formatRupiah(item.subtotal)"></span>
                    </div>
                    <template x-if="item.item_notes">
                        <div style="font-size: 10px; font-style: italic;" x-text="'Catatan: ' + item.item_notes"></div>
                    </template>
                </div>
            </template>
        </div>

        <div style="border-bottom: 1px dashed #000; padding-bottom: 6px; margin-bottom: 6px;">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal:</span>
                <span x-text="formatRupiah(orderItemsSubtotal(receiptModal.order))"></span>
            </div>
            <template x-if="receiptModal.order?.order_type === 'delivery'">
                <div style="display: flex; justify-content: space-between;">
                    <span>Ongkir:</span>
                    <span x-text="(receiptModal.order?.delivery_fee || 0) === 0 ? 'GRATIS' : formatRupiah(receiptModal.order?.delivery_fee)"></span>
                </div>
            </template>
            <template x-if="(receiptModal.order?.discount_amount || 0) > 0">
                <div style="display: flex; justify-content: space-between;">
                    <span>Diskon:</span>
                    <span x-text="'- ' + formatRupiah(receiptModal.order?.discount_amount)"></span>
                </div>
            </template>
            <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 13px; margin-top: 3px;">
                <span>TOTAL:</span>
                <span x-text="formatRupiah(receiptModal.order?.total_amount)"></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 2px;">
                <span x-text="'Bayar (' + (receiptModal.order?.payment_method || 'cash').toUpperCase() + '):'"></span>
                <span x-text="formatRupiah(receiptModal.order?.cash_received)"></span>
            </div>
            <template x-if="receiptModal.order?.payment_method === 'cash'">
                <div style="display: flex; justify-content: space-between;">
                    <span>Kembali:</span>
                    <span x-text="formatRupiah(receiptModal.order?.change_amount)"></span>
                </div>
            </template>
        </div>

        <div style="text-align: center; padding-top: 4px; font-size: 10px;">
            <div>Terima kasih telah memesan</div>
            <div style="font-weight: 700;">YOURSUF COFFEE</div>
        </div>
    </div>
</div>

