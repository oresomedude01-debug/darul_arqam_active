@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="hidden md:flex flex-1 items-center justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    @if ($paginator->onFirstPage())
                        {{ $paginator->count() }}
                    @else
                        <span>{{ $paginator->firstItem() }}</span>–<span>{{ $paginator->lastItem() }}</span>
                        of <span>{{ $paginator->total() }}</span>
                    @endif
                </p>
            </div>

            <div class="flex gap-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed" aria-hidden="true">
                            <i class="fas fa-chevron-left mr-2"></i>
                            @lang('pagination.previous')
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200 transition-colors">
                        <i class="fas fa-chevron-left mr-2"></i>
                        @lang('pagination.previous')
                    </a>
                @endif

                {{-- Pagination Elements --}}
                <div class="hidden md:flex gap-1">
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 rounded-lg bg-gray-50">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" aria-label="@lang('pagination.page', ['page' => $page])">
                                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg border border-blue-600">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" aria-label="@lang('pagination.go_to_page', ['page' => $page])" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200 transition-colors">
                        @lang('pagination.next')
                        <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed" aria-hidden="true">
                            @lang('pagination.next')
                            <i class="fas fa-chevron-right ml-2"></i>
                        </span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Mobile Pagination --}}
        <div class="md:hidden w-full">
            <div class="flex items-center justify-between gap-2">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" class="flex-1">
                        <span class="block w-full text-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-left mr-1"></i>
                            Prev
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex-1 block text-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 border border-blue-200 transition-colors">
                        <i class="fas fa-chevron-left mr-1"></i>
                        Prev
                    </a>
                @endif

                <div class="text-center px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 whitespace-nowrap text-xs font-medium text-gray-700">
                    {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                </div>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex-1 block text-center px-3 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 border border-green-200 transition-colors">
                        Next
                        <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                @else
                    <span aria-disabled="true" class="flex-1">
                        <span class="block w-full text-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            Next
                            <i class="fas fa-chevron-right ml-1"></i>
                        </span>
                    </span>
                @endif
            </div>

            <div class="mt-3 text-center text-xs text-gray-600">
                <p>
                    Showing <span class="font-semibold">{{ $paginator->firstItem() }}</span> to
                    <span class="font-semibold">{{ $paginator->lastItem() }}</span> of
                    <span class="font-semibold">{{ $paginator->total() }}</span> results
                </p>
            </div>
        </div>
    </nav>
@endif
