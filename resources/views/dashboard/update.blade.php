<x-header></x-header>
<body class="bg-gray-100">
<script src="@vite('resources/js/add-quiz.js')"></script>
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <x-sidebar></x-sidebar>
    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Navigation -->
        <x-topNavigation></x-topNavigation>
        <!-- Content -->
        <main class="p-6">
            <div class="min-h-screen bg-gray-100">
                <div class="container">
                    <!-- Header -->
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">Edit Quiz</h2>
                        <p class="mt-2 text-gray-600">Modify the quiz details below.</p>
                    </div>

                    <!-- Main Form -->
                    <form id="quizForm" method="POST" action="{{ route('quiz-update', ['quiz' => $quiz->id]) }}">
                        @csrf
                        <!-- Quiz Details Section -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Quiz Details</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Quiz Title</label>
                                    <input type="text" id="title" name="title" value="{{ $quiz->title }}" required
                                           class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Quiz Description</label>
                                    <textarea id="description" name="description" rows="3" required
                                              class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $quiz->description }}</textarea>
                                </div>
                                <div>
                                    <label for="timeLimit" class="block text-sm font-medium text-gray-700">Time Limit (minutes)</label>
                                    <input type="number" id="timeLimit" name="timeLimit" value="{{ $quiz->time_limit }}" min="1" required
                                           class="w-48 px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach($quiz->images as $image)
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Quiz Image" width="100">
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Questions Section -->
                        <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-semibold text-gray-800">Questions</h2>
                                <button type="button" id="addQuestionBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Add Question
                                </button>
                            </div>

                            <div id="questionsContainer" class="space-y-6">
                                @foreach($quiz->questions as $qIndex => $question)
                                    <div class="p-4 border border-gray-200 rounded-lg" data-question-id="{{ $qIndex }}">
                                        <label class="block text-sm font-medium text-gray-700">Question {{ $qIndex + 1 }}</label>
                                        <input type="text" name="questions[{{ $qIndex }}][name]" value="{{ $question->name }}" required
                                               class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                        <!-- Options -->
                                        <div class="mt-4 space-y-3" data-options-container>
                                            <p class="text-sm font-medium text-gray-700">Answer Options</p>
                                            @foreach($question->options as $oIndex => $option)
                                                <div class="flex items-center gap-4">
                                                    <input type="radio" name="questions[{{ $qIndex }}][correct]" value="{{ $oIndex }}" {{ $option->option_true ? 'checked' : '' }}>
                                                    <input type="text" name="questions[{{ $qIndex }}][options][]" value="{{ $option->name }}" required
                                                           class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    <button type="button" class="removeOptionBtn text-red-600 hover:text-red-800">×</button>
                                                </div>
                                            @endforeach
                                            <button type="button" class="addOptionBtn px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                                Add Option
                                            </button>
                                        </div>

                                        <button type="button" class="removeQuestionBtn text-red-600 hover:text-red-800 mt-4">
                                            Remove Question
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Update Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
