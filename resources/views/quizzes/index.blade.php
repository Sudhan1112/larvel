@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">Available Quizzes</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($quizzes as $quiz)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-2 text-indigo-700">{{ $quiz->title }}</h2>
                <p class="text-gray-600 mb-4 h-12 overflow-hidden">{{ Str::limit($quiz->description, 100) }}</p>
                <div class="flex justify-between items-center mt-4">
                    <a href="{{ route('quizzes.show', $quiz) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded font-medium transition">Take Quiz</a>
                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="text-indigo-500 hover:text-indigo-700 font-medium">Edit / Manage</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white p-8 text-center rounded-lg shadow-md">
            <p class="text-gray-500 text-lg mb-4">No quizzes available yet.</p>
            <a href="{{ route('admin.quizzes.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-indigo-700 transition">Create Your First Quiz</a>
        </div>
    @endforelse
</div>
@endsection
