<x-settings.group-layout title="Notifikasi" description="Pengaturan notifikasi dan WhatsApp" group="notification">
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">WhatsApp</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Konfigurasi integrasi WhatsApp</p>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.select
                name="wa_enabled"
                label="Aktifkan WhatsApp"
                :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                value="{{ old('wa_enabled', $settingValues['wa_enabled'] ?? '1') }}"
            />
            <x-ui.input
                name="wa_number"
                label="Nomor WhatsApp"
                value="{{ old('wa_number', $settingValues['wa_number'] ?? '6281234567890') }}"
                help="Format internasional tanpa tanda +"
            />
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Template Pesan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Template notifikasi yang dikirim ke pelanggan</p>
        </x-slot:header>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <x-ui.label for="wa_template_order_created">Template Pesanan Baru</x-ui.label>
                <textarea name="wa_template_order_created" id="wa_template_order_created" rows="3"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-4 py-2.5 focus:border-primary focus:ring-primary">{{ old('wa_template_order_created', $settingValues['wa_template_order_created'] ?? 'Halo {customer_name}, pesanan Anda #{order_number} sedang kami proses. Terima kasih!') }}</textarea>
            </div>
            <div>
                <x-ui.label for="wa_template_ready_pickup">Template Siap Diambil</x-ui.label>
                <textarea name="wa_template_ready_pickup" id="wa_template_ready_pickup" rows="3"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-4 py-2.5 focus:border-primary focus:ring-primary">{{ old('wa_template_ready_pickup', $settingValues['wa_template_ready_pickup'] ?? 'Halo {customer_name}, pesanan Anda #{order_number} sudah siap diambil. Terima kasih!') }}</textarea>
            </div>
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Saluran Notifikasi</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.select
                name="notif_order_status"
                label="Notifikasi Status Order"
                :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                value="{{ old('notif_order_status', $settingValues['notif_order_status'] ?? '1') }}"
            />
            <x-ui.select
                name="notif_payment"
                label="Notifikasi Pembayaran"
                :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                value="{{ old('notif_payment', $settingValues['notif_payment'] ?? '1') }}"
            />
            <x-ui.select
                name="notif_email"
                label="Notifikasi Email"
                :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                value="{{ old('notif_email', $settingValues['notif_email'] ?? '1') }}"
            />
        </div>
    </x-ui.card>
</x-settings.group-layout>
