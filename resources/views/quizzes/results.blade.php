@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header / Score Summary -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8 text-center">
        <div class="bg-indigo-600 p-6 text-white">
            <h1 class="text-3xl font-bold mb-2">Quiz Results</h1>
            <p class="text-indigo-100 text-lg">{{ $quiz->title }}</p>
        </div>
        <div class="p-8">
            <p class="text-xl text-gray-600 mb-2">Attempt by: <span class="font-bold text-gray-800">{{ $attempt->user_name }}</span></p>
            <p class="text-sm text-gray-400 mb-6">Submitted at: {{ $attempt->submitted_at->format('M d, Y h:i A') }}</p>
            
            @php
                $totalPossibleScore = $quiz->questions->sum('marks');
                $percentage = $totalPossibleScore > 0 ? round(($attempt->score / $totalPossibleScore) * 100) : 0;
            @endphp
            
            <div class="inline-block relative w-48 h-48 mb-4">
                <svg class="w-full h-full" viewBox="0 0 36 36">
                    <!-- Background Circle -->
                    <path
                        class="text-gray-200"
                        stroke-width="3"
                        stroke="currentColor"
                        fill="none"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <!-- Progress Circle -->
                    <path
                        class="{{ $percentage >= 50 ? 'text-green-500' : 'text-red-500' }}"
                        stroke-width="3"
                        stroke-dasharray="{{ $percentage }}, 100"
                        stroke="currentColor"
                        fill="none"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                </svg>
                <div class="absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center">
                    <span class="text-4xl font-bold text-gray-800">{{ $attempt->score }}<span class="text-2xl text-gray-500">/{{ $totalPossibleScore }}</span></span>
                    <span class="text-gray-500">{{ $percentage }}%</span>
                </div>
            </div>
            
            <div>
                <a href="{{ route('home') }}" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition mt-4">Back to Quizzes</a>
            </div>
        </div>
    </div>

    <!-- Detailed Answers -->
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Detailed Review</h2>
    
    <div class="space-y-6">
        @foreach($attempt->answers as $index => $answer)
            @php $question = $answer->question; @endphp
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 {{ $answer->is_correct ? 'border-green-500' : 'border-red-500' }}">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-medium text-gray-800 flex-grow">
                        <span class="font-bold text-gray-500 mr-2">Q{{ $index + 1 }}.</span> {!! $question->content !!}
                    </h3>
                    <div class="ml-4 flex-shrink-0 text-right">
                        @if($answer->is_correct)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Correct (+{{ $answer->marks_awarded }} marks)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Incorrect (0 / {{ $question->marks }} marks)
                            </span>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-md">
                    <p class="text-sm font-bold text-gray-700 mb-2">Your Answer:</p>
                    
                    @if($question->type === 'single_choice' || $question->type === 'binary')
                        @php
                            $selectedOption = $question->options->where('id', $answer->answer_value)->first();
                        @endphp
                        <p class="{{ $answer->is_correct ? 'text-green-700' : 'text-red-700' }}">
                            {{ $selectedOption ? $selectedOption->text : 'No answer selected' }}
                        </p>
                        
                    @elseif($question->type === 'multiple_choice')
                        @php
                            $selectedIds = json_decode($answer->answer_value, true) ?: [];
                            $selectedOptions = $question->options->whereIn('id', $selectedIds);
                        @endphp
                        @if($selectedOptions->isEmpty())
                            <p class="text-red-700">No answer selected</p>
                        @else
                            <ul class="list-disc pl-5 {{ $answer->is_correct ? 'text-green-700' : 'text-red-700' }}">
                                @foreach($selectedOptions as $opt)
                                    <li>{{ $opt->text }}</li>
                                @endforeach
                            </ul>
                        @endif
                        
                    @elseif($question->type === 'text_input' || $question->type === 'number_input')
                        <p class="{{ $answer->is_correct ? 'text-green-700' : 'text-red-700' }}">
                            {{ $answer->answer_value ?: 'No answer provided' }}
                        </p>
                    @endif

                    @if(!$answer->is_correct)
                        <div class="mt-4 pt-3 border-t border-gray-200">
                            <p class="text-sm font-bold text-gray-700 mb-1">Correct Answer:</p>
                            @if($question->type === 'multiple_choice')
                                <ul class="list-disc pl-5 text-green-700">
                                    @foreach($question->options->where('is_correct', true) as $opt)
                                        <li>{{ $opt->text }}</li>
                                    @endforeach
                                </ul>
                            @elseif($question->type === 'text_input' || $question->type === 'number_input')
                                <p class="text-green-700">{{ $question->options->where('is_correct', true)->first()->text ?? '' }}</p>
                            @else
                                <p class="text-green-700">{{ $question->options->where('is_correct', true)->first()->text ?? '' }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
