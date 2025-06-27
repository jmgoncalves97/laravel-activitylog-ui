<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add Alpine.js for better interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Add custom scrollbar styling -->
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
    <!-- Add Highlight.js for JSON formatting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/json.min.js"></script>

    <!-- Custom styles for JSON display -->
    <style>
        pre code.hljs {
            padding: 0.5rem;
            border-radius: 0.375rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.875rem;
            line-height: 1.25rem;
            background-color: white !important;
            border: 1px solid #e5e7eb;
        }

        .hljs-string { color: #22863a !important; }
        .hljs-number { color: #005cc5 !important; }
        .hljs-literal { color: #005cc5 !important; }
        .hljs-punctuation { color: #24292e !important; }
    </style>

    <!-- Initialize syntax highlighting -->
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen" x-data="activityLog()">
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-indigo-600">🔍 Activity Logger</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">{{ now()->format('F j, Y') }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Filter Section -->
            <div x-data="{ isFilterOpen: true }" class="bg-white rounded-xl shadow-sm mb-6 border border-gray-100">
                <div class="p-4 flex justify-between items-center border-b border-gray-100">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-lg font-semibold text-gray-900">Filters</h2>
                        <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                            {{ count(array_filter(request()->all())) }} active
                        </span>
                    </div>
                    <button @click="isFilterOpen = !isFilterOpen"
                        class="flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 focus:outline-none">
                        <span x-show="!isFilterOpen">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                        <span x-show="isFilterOpen">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        </span>
                    </button>
                </div>

                <div x-show="isFilterOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="p-6">
                    <form method="GET" action="{{ route('activitylog-ui.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Search Filter -->
                            <div class="space-y-1">
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" id="search"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Search description..."
                                        value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Model Filter -->
                            <div class="space-y-1">
                                <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                                <select name="model" id="model"
                                    class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Models</option>
                                    @foreach ($models as $model)
                                        <option value="{{ $model }}" {{ request('model') === $model ? 'selected' : '' }}>
                                            {{ $model }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Event Filter -->
                            <div class="space-y-1">
                                <label for="event" class="block text-sm font-medium text-gray-700">Event Type</label>
                                <select name="event" id="event"
                                    class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Events</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event }}" {{ request('event') === $event ? 'selected' : '' }}>
                                            {{ ucfirst($event) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Subject ID Filter -->
                            <div class="space-y-1">
                                <label for="subject_id" class="block text-sm font-medium text-gray-700">ID</label>
                                <input type="text" name="subject_id" id="subject_id"
                                    class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Enter ID..."
                                    value="{{ request('subject_id') }}">
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-end space-x-2">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                    Filter
                                </button>
                                @if(count(array_filter(request()->all())))
                                    <a href="{{ route('activitylog-ui.index') }}"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Table with enhanced styling -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- Enhanced table header -->
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="group px-6 py-3 text-left">
                                    <div class="flex items-center space-x-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <span>#</span>
                                        <svg class="w-4 h-4 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Causer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Properties</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logged At</th>
                            </tr>
                        </thead>

                        <!-- Enhanced table body -->
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <!-- Enhanced cell styling -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $loop->iteration + $logs->firstItem() - 1 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $log->description }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $log->causer ? $log->causer->name : 'System' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ class_basename($log->subject_type) ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $log->subject_id ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $log->event === 'created' ? 'bg-green-100 text-green-800' :
                                               ($log->event === 'updated' ? 'bg-yellow-100 text-yellow-800' :
                                               ($log->event === 'deleted' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                            {{ $log->event ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center">
                                        @if($log->properties->isNotEmpty())
                                            <button
                                                {{-- Store data in data-* attributes to avoid quote conflicts --}}
                                                data-title="Properties for Log #{{ $log->id }}"
                                                data-properties="@json($log->properties)"

                                                {{-- The @click reads the data from the element itself ($el) and parses the JSON --}}
                                                @click="openModal($el.dataset.title, {{ json_encode($log->properties) }})"

                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">

                                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                View
                                            </button>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="mt-2 text-gray-500 text-sm">No activities found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Improved Pagination Section -->
                @if($logs->hasPages())
                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <!-- Mobile pagination -->
                            <div class="flex justify-between flex-1 sm:hidden">
                                @if($logs->onFirstPage())
                                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                        Previous
                                    </span>
                                @else
                                    <a href="{{ $logs->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Previous
                                    </a>
                                @endif

                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md">
                                    Page {{ $logs->currentPage() }}
                                </span>

                                @if($logs->hasMorePages())
                                    <a href="{{ $logs->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Next
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                        Next
                                    </span>
                                @endif
                            </div>

                            <!-- Desktop pagination -->
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">{{ $logs->firstItem() }}</span>
                                        to
                                        <span class="font-medium">{{ $logs->lastItem() }}</span>
                                        of
                                        <span class="font-medium">{{ $logs->total() }}</span>
                                        results
                                    </p>
                                </div>

                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        {{-- Previous Page Link --}}
                                        @if ($logs->onFirstPage())
                                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                                <span class="sr-only">Previous</span>
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <a href="{{ $logs->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Previous</span>
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($logs->getUrlRange(max($logs->currentPage() - 2, 1), min($logs->currentPage() + 2, $logs->lastPage())) as $page => $url)
                                            @if ($page == $logs->currentPage())
                                                <span class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600">
                                                    {{ $page }}
                                                </span>
                                            @else
                                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                    {{ $page }}
                                                </a>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if ($logs->hasMorePages())
                                            <a href="{{ $logs->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Next</span>
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                                <span class="sr-only">Next</span>
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div x-show="isModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true"
        style="display: none;">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div @click="isModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="w-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="modalTitle"></h3>
                            <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2 max-h-[60vh] overflow-y-auto">
                            <pre><code id="modal-code" class="language-json rounded-lg border" x-text="modalContent"></code></pre>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="isModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function activityLog() {
            return {
                isModalOpen: false,
                modalContent: '',
                modalTitle: '',
                openModal(title, properties) {
                    this.isModalOpen = true;
                    this.modalTitle = title;
                    this.modalContent = JSON.stringify(properties, null, 4);

                    this.$nextTick(() => {
                        const codeBlock = document.getElementById('modal-code');
                        if (codeBlock) {
                            hljs.highlightElement(codeBlock);
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
