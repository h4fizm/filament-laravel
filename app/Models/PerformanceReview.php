<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $table = 'performance_review';

    protected $fillable = [
        'employee_id',
        'review_date',
        'attendance',
        'productivity',
        'discipline',
        'total_score',
        'evaluation',
    ];

    // Relasi ke Employee
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Relasi ke Contract yang diperbaiki// Relasi ke Contract yang diperbaiki
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'employee_id', 'employee_id');
    }

}
