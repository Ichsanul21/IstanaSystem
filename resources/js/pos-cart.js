export default function posCart() {
    return {
        cart: [],
        customerSearch: '',
        customerResults: [],
        selectedCustomer: null,
        promoCode: '',
        promoApplied: false,
        promoData: null,
        promoMessage: '',
        promoError: false,
        taxRate: 11,
        paidAmount: 0,
        newCustomerName: '',
        newCustomerPhone: '',
        showNewCustomerForm: false,
        creatingCustomer: false,
        addItem(id, name, price, qtyEl, unit) {
            const qty = parseFloat(qtyEl) || 1;
            const existing = this.cart.find(i => i.id === id);
            if (existing) {
                existing.qty += qty;
            } else {
                this.cart.push({ id, name, price, qty, unit });
            }
            this.resetPromo();
        },
        removeItem(index) {
            this.cart.splice(index, 1);
            this.resetPromo();
        },
        updateItem(index) {
            this.resetPromo();
        },
        get subtotal() {
            return this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
        },
        get discount() {
            if (!this.promoData || !this.promoApplied) return 0;
            const sub = this.subtotal;
            if (this.promoData.min_order > 0 && sub < this.promoData.min_order) return 0;
            let disc = this.promoData.type === 'percentage'
                ? sub * (this.promoData.value / 100)
                : this.promoData.value;
            if (this.promoData.max_discount > 0 && disc > this.promoData.max_discount) {
                disc = this.promoData.max_discount;
            }
            return Math.min(disc, sub);
        },
        get tax() {
            return Math.round((this.subtotal - this.discount) * (this.taxRate / 100));
        },
        get total() {
            return this.subtotal - this.discount + this.tax;
        },
        numberFormat(n) {
            return new Intl.NumberFormat('id-ID').format(n);
        },
        searchCustomer() {
            if (this.customerSearch.length < 2) { this.customerResults = []; return; }
            fetch(`/admin/customers/search/json?q=${encodeURIComponent(this.customerSearch)}`)
                .then(r => r.json())
                .then(data => { this.customerResults = data; })
                .catch(() => { this.customerResults = []; });
        },
        selectCustomer(c) {
            this.selectedCustomer = c;
            this.customerSearch = '';
            this.customerResults = [];
            this.showNewCustomerForm = false;
        },
        openNewCustomerForm() {
            this.newCustomerName = '';
            this.newCustomerPhone = '';
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'new-customer-modal' }));
        },
        closeNewCustomerForm() {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'new-customer-modal' }));
            this.newCustomerName = '';
            this.newCustomerPhone = '';
        },
        createCustomer() {
            const name = this.newCustomerName.trim();
            if (!name) return;

            this.creatingCustomer = true;

            const formData = new FormData();
            formData.append('name', name);
            formData.append('phone', this.newCustomerPhone.trim());

            fetch('/admin/customers/quick-create', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: formData
            })
                .then(r => r.json())
                .then(customer => {
                    this.selectedCustomer = customer;
                    this.customerSearch = '';
                    this.customerResults = [];
                    this.closeNewCustomerForm();
                })
                .catch(() => {
                    alert('Gagal menambahkan pelanggan. Silakan coba lagi.');
                })
                .finally(() => {
                    this.creatingCustomer = false;
                });
        },
        checkPromo() {
            if (this.promoApplied) {
                this.promoApplied = false;
                this.promoData = null;
                this.promoMessage = '';
                this.promoCode = '';
                return;
            }
            const code = this.promoCode.trim();
            if (!code) {
                this.promoMessage = 'Masukkan kode promo.';
                this.promoError = true;
                return;
            }
            fetch(`/admin/promotions/check/${encodeURIComponent(code)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.valid) {
                        this.promoApplied = true;
                        this.promoData = data.promotion;
                        this.promoMessage = 'Promo ' + data.promotion.name + ' diterapkan!';
                        this.promoError = false;
                    } else {
                        this.promoApplied = false;
                        this.promoData = null;
                        this.promoMessage = data.message || 'Kode promo tidak valid.';
                        this.promoError = true;
                    }
                })
                .catch(() => {
                    this.promoApplied = false;
                    this.promoData = null;
                    this.promoMessage = 'Gagal memeriksa promo.';
                    this.promoError = true;
                });
        },
        resetPromo() {
            if (this.promoApplied) {
                this.promoApplied = false;
                this.promoData = null;
                this.promoMessage = '';
                this.promoError = false;
                this.promoCode = '';
            }
        }
    }
}
