<div x-data="{ pin: '', error: '', loading: false }">
    <div class="text-center space-y-4">
        <div class="w-16 h-16 mx-auto rounded-full bg-primary/10 flex items-center justify-center">
            <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Masukkan PIN</h2>
        <p class="text-sm text-gray-500">Masukkan 2 digit terakhir nomor telepon Anda</p>
        <form x-on:submit.prevent="
            loading = true; error = '';
            fetch('/track/{{ $token }}/verify', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ pin })
            }).then(r => {
                if (r.ok) window.location.reload();
                else return r.json().then(d => { error = d.error || 'PIN salah'; });
            }).catch(() => { error = 'Terjadi kesalahan.'; })
            .finally(() => { loading = false; });
        " class="space-y-4">
            <x-ui.input type="text" maxlength="2" class="w-20 mx-auto text-center text-2xl" x-model="pin" required />
            <template x-if="error">
                <p class="text-sm text-red-600" x-text="error"></p>
            </template>
            <x-ui.button type="submit" variant="primary" x-bind:disabled="loading || pin.length !== 2" x-text="loading ? 'Memverifikasi...' : 'Verifikasi'"></x-ui.button>
        </form>
    </div>
</div>
