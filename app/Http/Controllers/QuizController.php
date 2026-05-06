<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Attempt;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::latest()->get();
        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz = Quiz::create($validated);

        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', 'Quiz created. Now add questions.');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz->update($validated);

        return redirect()->back()->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('home')->with('success', 'Quiz deleted successfully.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('quizzes.show', compact('quiz'));
    }

    public function attempt(Request $request, Quiz $quiz)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
        ]);

        $attempt = Attempt::create([
            'quiz_id' => $quiz->id,
            'user_name' => $request->user_name,
            'submitted_at' => now(),
            'score' => 0
        ]);

        $totalScore = 0;
        $quiz->load('questions.options');

        foreach ($quiz->questions as $question) {
            $inputAnswer = $request->input("answers.{$question->id}");
            $isCorrect = false;
            $marksAwarded = 0;

            if ($question->type === 'binary' || $question->type === 'single_choice') {
                $option = $question->options()->find($inputAnswer);
                if ($option && $option->is_correct) {
                    $isCorrect = true;
                }
            } elseif ($question->type === 'multiple_choice') {
                // $inputAnswer is array of option IDs
                if (is_array($inputAnswer)) {
                    $correctOptionIds = $question->options()->where('is_correct', true)->pluck('id')->toArray();
                    // Sort both arrays to compare
                    $submittedIds = array_map('intval', $inputAnswer);
                    sort($correctOptionIds);
                    sort($submittedIds);
                    if ($correctOptionIds == $submittedIds) {
                        $isCorrect = true;
                    }
                }
            } elseif ($question->type === 'number_input') {
                $correctOption = $question->options()->where('is_correct', true)->first();
                // Loose comparison to allow 10 vs "10"
                if ($correctOption && floatval($correctOption->text) == floatval($inputAnswer)) {
                    $isCorrect = true;
                }
            } elseif ($question->type === 'text_input') {
                $correctOption = $question->options()->where('is_correct', true)->first();
                if ($correctOption && strtolower(trim($correctOption->text)) === strtolower(trim($inputAnswer))) {
                    $isCorrect = true;
                }
            }

            if ($isCorrect) {
                $marksAwarded = $question->marks;
                $totalScore += $marksAwarded;
            }

            Answer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'answer_value' => is_array($inputAnswer) ? json_encode($inputAnswer) : $inputAnswer,
                'is_correct' => $isCorrect,
                'marks_awarded' => $marksAwarded,
            ]);
        }

        $attempt->update(['score' => $totalScore]);

        return redirect()->route('quizzes.results', ['quiz' => $quiz->id, 'attempt' => $attempt->id]);
    }

    public function results(Quiz $quiz, Attempt $attempt)
    {
        if ($attempt->quiz_id !== $quiz->id) abort(404);
        
        $attempt->load('answers.question.options');
        return view('quizzes.results', compact('quiz', 'attempt'));
    }
}
