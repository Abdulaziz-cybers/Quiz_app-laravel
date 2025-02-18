<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Option;
use App\Models\Question;
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
    public function getTimeTaken(Result $result){
        $createdAt = strtotime($result->started_at);
        $finishedAt = strtotime($result->finished_at);

        $diffInSeconds = $finishedAt - $createdAt;

// Format the output dynamically
        if ($diffInSeconds >= 3600) {
            $formattedTime = date('H:i:s', $diffInSeconds - strtotime('TODAY')); // Hours, minutes, seconds
        } elseif ($diffInSeconds >= 60) {
            $formattedTime = date('i:s', $diffInSeconds - strtotime('TODAY')); // Minutes and seconds
        } else {
            $formattedTime = $diffInSeconds . 's'; // Only seconds
        }

        return $formattedTime;
    }
    public function show(string $slug)
    {
        $quiz = Quiz::where('slug', $slug)->first();
        $result = Result::query()
            ->where('quiz_id', $quiz->id)
                ->where('user_id', auth()->id())
                    ->first();
        if(!$result){
            return view('quiz.show-quiz',[
                'quiz' => $quiz,
            ]);
        }
        $correctAnswerCount = Answer::query()
            ->where('result_id', $result->id) // Filter by quiz result
            ->whereHas('option', function ($query) {
                $query->where('option_true', 1); // Only count correct answers
            })->count();

        $quiz->question_count = $quiz->questions()->count();
        $quiz->correct_answer_count = $correctAnswerCount;
        $quiz->time_taken = $this->getTimeTaken($result);
        return to_route('results', ['quiz' => $quiz]);
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
        $quiz->title = $validator['title'];
        $quiz->description = $validator['description'];
        $quiz->time_limit = $validator['timeLimit'];
        $quiz->slug = Str::slug(Str::slug(strtotime(now()->format('Y-m-d H:i:s')) .  $validator['title']));
        $quiz->save();

        $quiz->questions()->delete();
        foreach ($validator['questions'] as $question) {
            $questionItem = $quiz->questions()->create([
                'quiz_id' => $quiz->id,
                'name' => $question['quiz'],
            ]);
            foreach ($question['options'] as $key => $option) {
                $questionItem->options()->create([
                    'question_id' => $questionItem->id,
                    'name' => $option,
                    'option_true' => ($question['correct'] == $key) ? 1 : 0,
                ]);
            }
        }
        return to_route('quizzes');
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
        $result = Result::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'finished_at' => date('Y-m-d H:i:s',strtotime('+'.$quiz->time_limit.' minutes'))
        ]);
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

        if($result->finished_at <= now()){
            return 'Seni vaqting tugagan yaramas';
        }
        $exists = Answer::where('option_id', $validator['answer'])
            ->where('result_id', $result->id)
            ->exists();
        if (!$exists) {
            Answer::create([
                'result_id' => $result->id,
                'option_id' => $validator['answer'],
            ]);
        }
        $answers = Answer::query()
            ->where('result_id', $result->id)
                ->get();
        $correctAnswerCount = Answer::query()
            ->where('result_id', $result->id) // Filter by quiz result
            ->whereHas('option', function ($query) {
                $query->where('option_true', 1); // Only count correct answers
            })->count();
        $options = Option::query()
            ->select('question_id')
                ->whereIn('id', $answers->pluck('option_id'))
                    ->get();
        $questions = Question::query()
            ->where('quiz_id', $quiz->id)
                ->whereNotIn('id', $options->pluck('question_id'))
                    ->get();
        if(count($questions)){
            $questions->load('options');
            $quiz->questions = $questions;
            return view('quiz.take-quiz',[
                'quiz' => $quiz
            ]);
        }
        $result->finished_at = now();
        $result->save();
        $quiz->question_count = $quiz->questions()->count();
        $quiz->correct_answer_count = $correctAnswerCount;
        $quiz->time_taken = $this->getTimeTaken($result);
        return to_route('results',['quiz' => $quiz]);
    }
}
