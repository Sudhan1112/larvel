<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = ['attempt_id', 'question_id', 'answer_value', 'is_correct', 'marks_awarded'];

    protected $casts = [
        'is_correct' => 'boolean',
        // In case multiple choice answers are stored as JSON arrays:
        'answer_value' => 'array', 
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
