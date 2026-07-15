@php
    $regime = old('tax_regime', $settingValues['tax_regime'] ?? 'pp23');
@endphp

<x-settings.group-layout title="Pajak" description="Pengaturan perpajakan" group="tax">
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Regime Pajak</h3>
        </x-slot:header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-ui.select
                name="tax_regime"
                label="Rezim Pajak"
                :options="[
                    'none' => 'Tidak Ada Pajak',
                    'pp23' => 'PP 23 (Pajak Penghasilan Pasal 23)',
                    'pkp' => 'PKP (Pengusaha Kena Pajak)',
                ]"
                value="{{ $regime }}"
            />
        </div>
    </x-ui.card>

    <div x-data="{ regime: '{{ $regime }}' }" x-on:tax_regime.window="regime = $event.target.value" class="space-y-6">
        <x-ui.card x-show="regime === 'pp23'" x-transition>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">PP 23</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tarif Pajak Penghasilan Pasal 23</p>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input
                    name="pp23_rate"
                    label="Tarif PP 23 (%)"
                    type="number"
                    step="0.01"
                    value="{{ old('pp23_rate', $settingValues['pp23_rate'] ?? '0.5') }}"
                    help="Contoh: 0.5 untuk 0.5%"
                />
            </div>
        </x-ui.card>

        <x-ui.card x-show="regime === 'pkp'" x-transition>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">PPN</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pajak Pertambahan Nilai</p>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input
                    name="ppn_rate"
                    label="Tarif PPN (%)"
                    type="number"
                    step="0.01"
                    value="{{ old('ppn_rate', $settingValues['ppn_rate'] ?? '11') }}"
                    help="Contoh: 11 untuk 11%"
                />
            </div>
        </x-ui.card>
    </div>
</x-settings.group-layout>
