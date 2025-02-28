<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report';

    protected $fillable = [
        'employee_id',
        'performance_id',
    ];

    // Relasi ke Employee
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Relasi ke PerformanceReview
    public function performanceReview(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_id');
    }
}