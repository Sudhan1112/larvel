<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'type' => 'required|in:binary,single_choice,multiple_choice,number_input,text_input',
            'content' => 'required|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|url',
            'marks' => 'required|integer|min:1',
            'options' => 'required|array',
            'options.*.text' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',
            'options.*.image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $mediaPath = null;
        $mediaType = null;

        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('questions', 'public');
            $mediaType = 'image';
        } elseif ($request->filled('video_url')) {
            $mediaPath = $request->video_url;
            $mediaType = 'video_url';
        }

        $question = $quiz->questions()->create([
            'type' => $validated['type'],
            'content' => $validated['content'],
            'media_type' => $mediaType,
            'media_path' => $mediaPath,
            'marks' => $validated['marks']
        ]);

        foreach ($validated['options'] as $index => $optionData) {
            $optionImagePath = null;
            if (isset($optionData['image'])) {
                $optionImagePath = $optionData['image']->store('options', 'public');
            }

            // check if there's any text or image, to prevent empty options
            if (!empty($optionData['text']) || $optionImagePath) {
                // Ensure at least boolean true/false for is_correct
                $isCorrect = false;
                if ($validated['type'] === 'binary' || $validated['type'] === 'single_choice') {
                    // if correct_option radio match index
                    if ($request->input('correct_option') == $index) {
                        $isCorrect = true;
                    }
                } elseif ($validated['type'] === 'multiple_choice') {
                    $isCorrect = isset($optionData['is_correct']) && $optionData['is_correct'];
                } elseif ($validated['type'] === 'number_input' || $validated['type'] === 'text_input') {
                    // For text/number input, the provided option is always the correct one
                    $isCorrect = true;
                }

                $question->options()->create([
                    'text' => $optionData['text'] ?? '',
                    'image_path' => $optionImagePath,
                    'is_correct' => $isCorrect
                ]);
            }
        }

        return redirect()->back()->with('success', 'Question added successfully.');
    }
}
