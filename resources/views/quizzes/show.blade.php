@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-indigo-600 text-white p-8 rounded-t-lg shadow-md">
        <h1 class="text-3xl font-bold mb-2">{{ $quiz->title }}</h1>
        <p class="text-indigo-100">{{ $quiz->description }}</p>
    </div>

    @if($quiz->questions->isEmpty())
        <div class="bg-white p-8 rounded-b-lg shadow-md text-center">
            <p class="text-gray-500 text-xl">This quiz has no questions yet.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block text-indigo-600 font-semibold hover:underline">&larr; Back to Home</a>
        </div>
    @else
        <form action="{{ route('quizzes.attempt', $quiz) }}" method="POST" class="bg-white p-8 rounded-b-lg shadow-md">
            @csrf
            
            <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <label for="user_name" class="block text-gray-700 font-bold mb-2">Enter your name to start:</label>
                <input type="text" name="user_name" id="user_name" required class="w-full md:w-1/2 px-4 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500" placeholder="John Doe">
            </div>

            <div class="space-y-10">
                @foreach($quiz->questions as $index => $question)
                    <div class="border-b pb-8 last:border-0 last:pb-0">
                        <div class="flex items-start mb-4">
                            <span class="bg-indigo-100 text-indigo-800 font-bold px-3 py-1 rounded-full mr-4">{{ $index + 1 }}</span>
                            <div class="flex-grow text-lg text-gray-800 font-medium">
                                {!! $question->content !!}
                                <span class="text-sm text-gray-500 ml-2 font-normal">({{ $question->marks }} marks)</span>
                            </div>
                        </div>

                        @if($question->media_path)
                            <div class="ml-14 mb-4">
                                @if($question->media_type === 'image')
                                    <img src="{{ Storage::url($question->media_path) }}" alt="Question Image" class="max-h-64 rounded-lg shadow-sm border">
                                @elseif($question->media_type === 'video_url')
                                    <a href="{{ $question->media_path }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-4 py-2 rounded">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Watch Video associated with this question
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div class="ml-14 space-y-3">
                            @if($question->type === 'single_choice' || $question->type === 'binary')
                                @foreach($question->options as $option)
                                    <label class="flex items-center p-3 rounded border hover:bg-gray-50 cursor-pointer transition">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300" required>
                                        <div class="ml-3 flex items-center">
                                            @if($option->image_path)
                                                <img src="{{ Storage::url($option->image_path) }}" alt="Option Image" class="h-10 w-10 object-cover rounded mr-3 border">
                                            @endif
                                            <span class="text-gray-700">{{ $option->text }}</span>
                                        </div>
                                    </label>
                                @endforeach

                            @elseif($question->type === 'multiple_choice')
                                @foreach($question->options as $option)
                                    <label class="flex items-center p-3 rounded border hover:bg-gray-50 cursor-pointer transition">
                                        <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->id }}" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 rounded border-gray-300">
                                        <div class="ml-3 flex items-center">
                                            @if($option->image_path)
                                                <img src="{{ Storage::url($option->image_path) }}" alt="Option Image" class="h-10 w-10 object-cover rounded mr-3 border">
                                            @endif
                                            <span class="text-gray-700">{{ $option->text }}</span>
                                        </div>
                                    </label>
                                @endforeach

                            @elseif($question->type === 'text_input')
                                <input type="text" name="answers[{{ $question->id }}]" required class="w-full px-4 py-3 border rounded-md focus:ring-2 focus:ring-indigo-500" placeholder="Type your answer here...">

                            @elseif($question->type === 'number_input')
                                <input type="number" step="any" name="answers[{{ $question->id }}]" required class="w-full md:w-1/2 px-4 py-3 border rounded-md focus:ring-2 focus:ring-indigo-500" placeholder="Enter numeric value...">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 pt-6 border-t flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-1">Submit Quiz</button>
            </div>
        </form>
    @endif
</div>
@endsection
