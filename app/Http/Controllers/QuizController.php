<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Quiz;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.quizzes',[
            'quizzes' => Quiz::withCount('questions')
                ->where('user_id', auth()->id())
                ->orderBy('id','desc')
                ->paginate(9)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'timeLimit' => 'required|integer',
            'questions' => 'required|array',
        ]);
        $quiz = Quiz::create([
            'user_id' => auth()->id(),
            'title' => $validator['title'],
            'description' => $validator['description'],
            'time_limit' => $validator['timeLimit'],
            'slug' => Str::slug(strtotime(now()->format('Y-m-d H:i:s')) .  $validator['title']),
        ]);
        foreach ($validator['questions'] as $question) {
            $questionItem = $quiz->questions()->create([
                'name' => $question['quiz'],
            ]);
            foreach ($question['options'] as $key => $option) {
                $questionItem->options()->create([
                    'name' => $option,
                    'option_true' => $question['correct'] = $key ? 1 : 0,
                ]);
            }
        }
        return to_route('quizzes');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        return view('dashboard.update',[
            'quiz' => $quiz,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'timeLimit' => 'required|integer',
            'questions' => 'required|array',
        ]);
        $quiz->title = request('title');
        $quiz->description = request('description');
        $quiz->time_limit = request('timeLimit');
        $quiz->slug = Str::slug(Str::slug(strtotime(now()->format('Y-m-d H:i:s')) .  $validator['title']));
        $quiz->save();

        $quiz->questions()->delete();
        foreach ($validator['questions'] as $question) {
            $questionItem = $quiz->questions()->create([
                'name' => $question['quiz'],
            ]);
            foreach ($question['options'] as $key => $option) {
                $questionItem->options()->create([
                    'name' => $option,
                    'option_true' => $question['correct'] = $key ? 1 : 0,
                ]);
            }
        }
        return to_route('quizzes')->with('success', 'Quiz updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        Quiz::destroy($quiz->id);
        return to_route('quizzes');
    }
    public function startQuiz(string $slug){
        $quiz = Quiz::query()->where('slug', $slug)->with('questions.options')->first();
        return view('quiz.take-quiz',[
            'quiz' => $quiz,
        ]);
    }
    public function takeQuiz(string $slug, Request $request){
        $validator = $request->validate([
            'answer' => 'required|string',
        ]);
        $user_id = auth()->id();
        $quiz = Quiz::where('slug', $slug)->first();

        $result = Result::where('quiz_id', $quiz->id)
            ->where('user_id', $user_id)->first();

        if (!$result) {
            $result = Result::create([
                'user_id' => $user_id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
            ]);

            Answer::create([
                'result_id' => $result->id,
                'option_id' => $validator['answer'],
            ]);

            $answeredOptionIds = Answer::where('result_id', $result->id);
        }
    }
}
