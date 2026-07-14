@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-lg border border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 text-gray-900 dark:text-gray-100 transition-colors px-4 py-3 text-sm focus:border-lo focus:ring-lo disabled:bg-gray-50 dark:disabled:bg-dark-900 disabled:cursor-not-allowed']) }}>
