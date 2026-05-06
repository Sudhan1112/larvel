@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Quiz Info & Delete -->
    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold mb-4">Edit Quiz Details</h2>
            <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Quiz Title</label>
                    <input type="text" name="title" value="{{ $quiz->title }}" required class="w-full px-4 py-2 border rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-md">{{ $quiz->description }}</textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">Update Details</button>
            </form>
        </div>

        <div class="bg-red-50 p-6 rounded-lg shadow-md border border-red-200">
            <h2 class="text-xl font-bold text-red-700 mb-4">Danger Zone</h2>
            <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quiz and all its questions?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-md hover:bg-red-700 transition">Delete Entire Quiz</button>
            </form>
        </div>
    </div>

    <!-- Questions Management -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Existing Questions -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Questions ({{ $quiz->questions->count() }})</h2>
            @if($quiz->questions->isEmpty())
                <p class="text-gray-500 italic">No questions added yet.</p>
            @else
                <div class="space-y-4">
                    @foreach($quiz->questions as $idx => $question)
                        <div class="border rounded-md p-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-lg text-indigo-800">Q{{ $idx + 1 }}. {!! $question->content !!}</h3>
                                <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">{{ str_replace('_', ' ', Str::title($question->type)) }} ({{ $question->marks }} marks)</span>
                            </div>
                            @if($question->media_path)
                                @if($question->media_type === 'image')
                                    <img src="{{ Storage::url($question->media_path) }}" alt="Question Media" class="max-h-40 rounded mb-2">
                                @elseif($question->media_type === 'video_url')
                                    <a href="{{ $question->media_path }}" target="_blank" class="text-blue-500 hover:underline mb-2 block">View Video</a>
                                @endif
                            @endif
                            <ul class="list-disc pl-5 mt-2 space-y-1">
                                @foreach($question->options as $option)
                                    <li class="{{ $option->is_correct ? 'text-green-600 font-bold' : 'text-gray-700' }}">
                                        @if($option->image_path)
                                            <img src="{{ Storage::url($option->image_path) }}" alt="Option Image" class="h-8 inline align-middle mr-2 rounded">
                                        @endif
                                        {{ $option->text }}
                                        @if($option->is_correct) <span class="text-xs ml-1 bg-green-100 px-1 rounded text-green-800">Correct</span> @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Add Question Form -->
        <div class="bg-white p-6 rounded-lg shadow-md" x-data="questionForm()">
            <h2 class="text-2xl font-bold mb-4">Add New Question</h2>
            <form action="{{ route('admin.questions.store', $quiz) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Question Type</label>
                        <select name="type" x-model="type" class="w-full px-4 py-2 border rounded-md bg-white">
                            <option value="single_choice">Single Choice</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="binary">True / False (Binary)</option>
                            <option value="text_input">Text Input</option>
                            <option value="number_input">Number Input</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Marks</label>
                        <input type="number" name="marks" value="1" min="1" required class="w-full px-4 py-2 border rounded-md">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Question Content (HTML supported)</label>
                    <textarea name="content" rows="3" required class="w-full px-4 py-2 border rounded-md placeholder-gray-400" placeholder="E.g., What is the capital of France? or <strong>HTML tags</strong>"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded border">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1 text-sm">Upload Image (Optional)</label>
                        <input type="file" name="media" accept="image/*" class="w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1 text-sm">Video URL (Optional)</label>
                        <input type="url" name="video_url" class="w-full px-3 py-1 border rounded-md text-sm" placeholder="e.g., YouTube Link">
                    </div>
                </div>

                <h3 class="text-lg font-bold mb-3 border-b pb-1">Options</h3>
                
                <div class="space-y-3">
                    <template x-for="(option, index) in options" :key="index">
                        <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded border">
                            <!-- Correct Answer Indicator -->
                            <div class="w-10 flex justify-center">
                                <template x-if="type === 'single_choice' || type === 'binary'">
                                    <input type="radio" name="correct_option" :value="index" :checked="index === 0" class="w-5 h-5 text-indigo-600">
                                </template>
                                <template x-if="type === 'multiple_choice'">
                                    <input type="checkbox" :name="'options['+index+'][is_correct]'" value="1" class="w-5 h-5 text-indigo-600 rounded">
                                </template>
                                <template x-if="type === 'text_input' || type === 'number_input'">
                                    <span class="text-green-600 font-bold" title="This is the correct answer">✓</span>
                                </template>
                            </div>

                            <div class="flex-grow flex space-x-2">
                                <input type="text" :name="'options['+index+'][text]'" x-model="option.text" class="w-full px-3 py-2 border rounded-md" :placeholder="getPlaceholder(index)" :required="index === 0">
                                <template x-if="type !== 'text_input' && type !== 'number_input'">
                                    <input type="file" :name="'options['+index+'][image]'" accept="image/*" class="w-48 text-sm pt-1 border border-dashed rounded px-1" title="Option Image">
                                </template>
                            </div>

                            <div class="w-10">
                                <template x-if="options.length > 1 && (type === 'single_choice' || type === 'multiple_choice')">
                                    <button type="button" @click="removeOption(index)" class="text-red-500 hover:text-red-700 p-2 font-bold text-xl">&times;</button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4" x-show="type === 'single_choice' || type === 'multiple_choice'">
                    <button type="button" @click="addOption()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition text-sm font-medium">+ Add Option</button>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-md font-bold hover:bg-green-700 transition">Save Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function questionForm() {
        return {
            type: 'single_choice',
            options: [ { text: '' }, { text: '' } ],
            init() {
                this.$watch('type', value => {
                    if (value === 'binary') {
                        this.options = [ { text: 'True' }, { text: 'False' } ];
                    } else if (value === 'text_input' || value === 'number_input') {
                        this.options = [ { text: '' } ];
                    } else {
                        if(this.options.length < 2) {
                            this.options = [ { text: '' }, { text: '' } ];
                        }
                    }
                });
            },
            addOption() {
                this.options.push({ text: '' });
            },
            removeOption(index) {
                this.options.splice(index, 1);
            },
            getPlaceholder(index) {
                if (this.type === 'binary') return (index === 0) ? 'True' : 'False';
                if (this.type === 'text_input') return 'Enter the correct text answer';
                if (this.type === 'number_input') return 'Enter the correct numeric answer';
                return 'Option ' + (index + 1);
            }
        }
    }
</script>
@endsection
