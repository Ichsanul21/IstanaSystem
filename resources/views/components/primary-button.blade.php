<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-lo text-white text-sm font-medium rounded-lg hover:bg-lo-600 focus:outline-none focus:ring-2 focus:ring-lo focus:ring-offset-2 transition-all duration-200 cta-main']) }}>
    {{ $slot }}
</button>
