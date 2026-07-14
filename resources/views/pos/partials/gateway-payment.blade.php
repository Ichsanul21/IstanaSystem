<div x-data="gatewayPayment()" class="mt-4">
    <input type="hidden" name="snap_token" x-model="snapToken">

    <template x-if="!snapToken">
        <x-ui.button type="button" variant="primary" x-on:click="requestSnap" :loading="loading">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
            Bayar Online
        </x-ui.button>
    </template>

    <template x-if="snapToken">
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                Snap token siap
            </div>
            <x-ui.button type="button" variant="primary" x-on:click="openSnap">
                Buka Pembayaran
            </x-ui.button>
        </div>
    </template>

    <script>
        function gatewayPayment() {
            return {
                snapToken: null,
                loading: false,
                requestSnap() {
                    this.loading = true;
                    fetch('/api/v1/payments/midtrans/snap', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ order_id: '{{ $order->id ?? '' }}' })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.snapToken = data.snap_token;
                        this.loading = false;
                    })
                    .catch(() => { this.loading = false; });
                },
                openSnap() {
                    if (this.snapToken && window.snap) {
                        window.snap.pay(this.snapToken, {
                            onSuccess: () => { window.location.reload(); }
                        });
                    }
                }
            }
        }
    </script>
</div>
