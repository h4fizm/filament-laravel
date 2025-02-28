<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contract';

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'contract_status',
        'description',
    ];

    /**
     * Get the employee that owns the contract.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
