<x-layouts.admin title="POS - Pesanan Baru">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">POS - Pesanan Baru</h1>
        </div>
    </x-slot:header>

    <div x-data="posCart()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cari Pelanggan</h2>
                </x-slot:header>
                <div class="space-y-3">
                    <x-ui.input type="text" placeholder="Cari nama atau nomor telepon..." x-model="customerSearch" x-on:input.debounce="searchCustomer" x-bind:disabled="selectedCustomer !== null" />
                    <template x-if="!selectedCustomer && customerSearch.length >= 2 && customerResults.length === 0">
                        <div class="flex flex-col items-center gap-2 py-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pelanggan tidak ditemukan</p>
                            <x-ui.button type="button" variant="primary" size="sm" x-on:click="openNewCustomerForm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Tambah Pelanggan Baru
                            </x-ui.button>
                        </div>
                    </template>
                    <template x-if="customerResults.length > 0">
                        <ul class="border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-200 dark:divide-gray-700 max-h-48 overflow-y-auto">
                            <template x-for="c in customerResults" :key="c.id">
                                <li x-on:click="selectCustomer(c)" class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                                    <span x-text="c.name"></span> - <span class="text-gray-500" x-text="c.phone"></span>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <div x-show="selectedCustomer" class="flex items-center gap-3 p-3 bg-primary/5 rounded-lg border border-primary/20">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white font-bold" x-text="selectedCustomer?.name?.charAt(0)"></div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="selectedCustomer?.name"></p>
                            <p class="text-sm text-gray-500" x-text="selectedCustomer?.phone"></p>
                        </div>
                        <button x-on:click="selectedCustomer = null; customerSearch = ''" class="ml-auto text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <x-ui.modal name="new-customer-modal" title="Tambah Pelanggan Baru">
                        <div class="space-y-4">
                            <x-ui.input name="new_customer_name" label="Nama" required x-model="newCustomerName" />
                            <x-ui.input name="new_customer_phone" label="Nomor Telepon" x-model="newCustomerPhone" />
                        </div>
                        <x-slot:footer>
                            <x-ui.button type="button" variant="ghost" x-on:click="closeNewCustomerForm">Batal</x-ui.button>
                            <x-ui.button type="button" variant="primary" x-on:click="createCustomer" x-bind:disabled="!newCustomerName.trim() || creatingCustomer" x-text="creatingCustomer ? 'Menyimpan...' : 'Simpan'"></x-ui.button>
                        </x-slot:footer>
                    </x-ui.modal>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pilih Layanan</h2>
                </x-slot:header>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($pricings ?? [] as $pricing)
                        <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary/50 transition-colors">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $pricing->service->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Rp {{ number_format($pricing->price, 0, ',', '.') }} / {{ $pricing->service->unit ?? 'item' }}
                                    @if($pricing->estimated_days)
                                        &middot; {{ $pricing->estimated_days }} hari
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-ui.input type="number" value="1" min="1" class="w-16 text-center" x-ref="qty_{{ $pricing->id }}" />
                                <x-ui.button size="sm" variant="primary" x-on:click="addItem({{ $pricing->id }}, '{{ addslashes($pricing->service->name) }}', {{ $pricing->price }}, $refs.qty_{{ $pricing->id }}.value, '{{ $pricing->service->unit ?? 'item' }}')">
                                    Tambah
                                </x-ui.button>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-2 text-center text-sm text-gray-500 dark:text-gray-400 py-8">Tidak ada layanan tersedia.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        Keranjang
                    </h2>
                </x-slot:header>
                <template x-if="cart.length === 0">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Belum ada item.</p>
                </template>
                <template x-if="cart.length > 0">
                    <div class="space-y-3">
                        <template x-for="(item, index) in cart" :key="index">
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="item.name"></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="number" x-model="item.qty" min="1" class="w-16 text-center text-xs rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-1" x-on:input.debounce="updateItem(index)">
                                        <span class="text-xs text-gray-500" x-text="'x Rp ' + numberFormat(item.price) + ' / ' + item.unit"></span>
                                    </div>
                                    <p class="text-sm font-semibold text-primary mt-1">Rp <span x-text="numberFormat(item.qty * item.price)"></span></p>
                                </div>
                                <button x-on:click="removeItem(index)" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </template>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-2">
                            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span class="font-medium text-gray-900 dark:text-white">Rp <span x-text="numberFormat(subtotal)"></span></span>
                            </div>
                            <template x-if="discount > 0">
                                <div class="flex justify-between text-sm">
                                    <span class="text-green-600 dark:text-green-400">Diskon</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">- Rp <span x-text="numberFormat(discount)"></span></span>
                                </div>
                            </template>
                            <template x-if="tax > 0">
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <span>Pajak (PPN <span x-text="taxRate"></span>%)</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Rp <span x-text="numberFormat(tax)"></span></span>
                                </div>
                            </template>
                            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                                <span>Total</span>
                                <span class="text-primary">Rp <span x-text="numberFormat(total)"></span></span>
                            </div>
                        </div>
                    </div>
                </template>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pembayaran</h2>
                </x-slot:header>
                <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="customer_id" x-bind:value="selectedCustomer?.id">
                    <template x-for="(item, index) in cart" :key="index">
                        <input type="hidden" :name="'items['+index+'][service_id]'" x-model="item.id">
                        <input type="hidden" :name="'items['+index+'][quantity]'" x-model="item.qty">
                        <input type="hidden" :name="'items['+index+'][price_per_unit]'" x-model="item.price">
                    </template>
                    <div>
                        <x-ui.label>Kode Promo</x-ui.label>
                        <div class="flex gap-2 mt-1">
                    <input type="hidden" name="promotion_code" x-model="promoCode">
                    <x-ui.input type="text" placeholder="Masukkan kode promo" class="flex-1" x-model="promoCode" x-bind:disabled="promoApplied" />
                    <x-ui.button type="button" variant="outline" size="sm" x-on:click="checkPromo" x-text="promoApplied ? 'Ganti' : 'Pakai'"></x-ui.button>
                        </div>
                        <template x-if="promoMessage">
                            <p class="mt-1 text-xs" x-bind:class="promoError ? 'text-red-600' : 'text-green-600'" x-text="promoMessage"></p>
                        </template>
                    </div>
                    <x-ui.select name="method" label="Metode Pembayaran" :options="['cash' => 'Tunai', 'transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'gateway' => 'Payment Gateway']" required />
                    <div>
                        <x-ui.input type="number" name="paid_amount" label="Jumlah Dibayar" required x-model="paidAmount" />
                        <template x-if="paidAmount > 0 && paidAmount >= total">
                            <p class="mt-1 text-xs text-green-600">Kembalian: Rp <span x-text="numberFormat(paidAmount - total)"></span></p>
                        </template>
                    </div>
                    <x-ui.textarea name="notes" label="Catatan" />
                    <x-ui.button type="submit" variant="primary" class="w-full" x-bind:disabled="cart.length === 0">Bayar</x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>


</x-layouts.admin>
