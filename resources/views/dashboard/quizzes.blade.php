<x-header></x-header>
<body class="bg-gray-100">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <x-sidebar></x-sidebar>
    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Navigation -->
        <x-topNavigation></x-topNavigation>
        <!-- Content -->
        <main class="p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">My Quizzes</h2>
                <div class="flex space-x-4">
                    <a href="{{ route('create-quiz') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Create New Quiz
                    </a>
                    <div class="flex border rounded-lg">
                        <button class="px-3 py-2 bg-white border-r">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 4h10v2H5V4zm0 5h10v2H5V9zm0 5h10v2H5v-2z"></path>
                            </svg>
                        </button>
                        <button class="px-3 py-2 bg-gray-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 5h2v2H5V5zm0 4h2v2H5V9zm0 4h2v2H5v-2zm4-8h6v2H9V5zm0 4h6v2H9V9zm0 4h6v2H9v-2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1">
                        <input type="text" placeholder="Search quizzes..." class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <select class="px-4 py-2 border rounded-lg">
                        <option>Sort by</option>
                        <option>Date Created</option>
                        <option>Completion Rate</option>
                        <option>Title</option>
                    </select>
                </div>
            </div>

            <!-- Quiz Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($quizzes as $quiz)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold">{{ $quiz->title }}</h3>
                                <p class="text-gray-500 text-sm">Mathematics</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">{{ $quiz->description }}</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-500">{{ $quiz->questions_count }} Questions</span>
                            <span class="text-sm text-gray-500">{{ $quiz->time_limit }} minutes</span>
                        </div>
                        <div class="flex justify-between">
                            <a href="{{ route('quiz-edit', ['quiz' => $quiz->id]) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                            <button class="text-green-600 hover:text-green-800">View Results</button>
                            <button class="text-green-600 hover:text-green-100 hover:bg-blue-500" onclick="share('{{ $quiz->slug }}')">Share</button>
                            <a href="{{ route('delete-quiz', ['quiz' => $quiz->id]) }}" class="text-red-600 hover:text-red-800">Delete</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination (Default Laravel Links) -->
            <div class="mt-6">
                {{ $quizzes->links() }}
            </div>

            <!-- Custom Pagination UI (Optional) -->
            <div class="flex justify-center mt-6">
                <ul class="inline-flex items-center -space-x-px">
                    @if ($quizzes->onFirstPage())
                        <li class="px-3 py-2 text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed rounded-l-lg">
                            Previous
                        </li>
                    @else
                        <li>
                            <a href="{{ $quizzes->previousPageUrl() }}" class="px-3 py-2 text-gray-600 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-200">
                                Previous
                            </a>
                        </li>
                    @endif

                    @foreach ($quizzes->getUrlRange(1, $quizzes->lastPage()) as $page => $url)
                        <li>
                            <a href="{{ $url }}" class="px-3 py-2 border border-gray-300 {{ $quizzes->currentPage() == $page ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-200' }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach

                    @if ($quizzes->hasMorePages())
                        <li>
                            <a href="{{ $quizzes->nextPageUrl() }}" class="px-3 py-2 text-gray-600 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-200">
                                Next
                            </a>
                        </li>
                    @else
                        <li class="px-3 py-2 text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed rounded-r-lg">
                            Next
                        </li>
                    @endif
                </ul>
            </div>

        </main>
    </div>
</div>

<script>
    async function share(slug) {
        try {
            slug = '{{ url('/show-quiz/') }}' + '/' + slug;
            await navigator.clipboard.writeText(slug);
            alert('Content copied to clipboard');
        } catch (err) {
            console.error('Failed to copy: ' + err);
        }
    }
</script>
</body>
</html>
