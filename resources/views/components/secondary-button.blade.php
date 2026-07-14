<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-dark-900 border border-lo-gray dark:border-dark-700 text-sm font-medium text-black dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-lo focus:ring-offset-2 transition-all duration-200']) }}>
    {{ $slot }}
</button>
