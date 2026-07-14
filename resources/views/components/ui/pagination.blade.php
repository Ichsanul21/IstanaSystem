@props(['paginator'])

@if ($paginator && $paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" {{ $attributes->merge(['class' => 'flex items-center justify-between']) }}>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-black/40 dark:text-white/40">
                    Menampilkan
                    <span class="font-medium text-dark dark:text-white">{{ $paginator->firstItem() }}</span>
                    hingga
                    <span class="font-medium text-dark dark:text-white">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-medium text-dark dark:text-white">{{ $paginator->total() }}</span>
                </p>
            </div>
            <div class="flex items-center gap-1">
                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-lo-gray dark:border-dark-700 text-black/20 dark:text-white/20 cursor-not-allowed">
                        <x-icon name="chevron-left" class="text-sm" />
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-lo-gray dark:border-dark-700 text-black hover:bg-gray-50 dark:hover:bg-dark-800 transition-colors">
                        <x-icon name="chevron-left" class="text-sm" />
                    </a>
                @endif

                {{-- Pages --}}
                @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium transition-colors {{ $page === $paginator->currentPage() ? 'bg-lo text-white' : 'border border-lo-gray dark:border-dark-700 text-black dark:text-white hover:bg-gray-50 dark:hover:bg-dark-800' }}">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-lo-gray dark:border-dark-700 text-black hover:bg-gray-50 dark:hover:bg-dark-800 transition-colors">
                        <x-icon name="chevron-right" class="text-sm" />
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-lo-gray dark:border-dark-700 text-black/20 dark:text-white/20 cursor-not-allowed">
                        <x-icon name="chevron-right" class="text-sm" />
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
