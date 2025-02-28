<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employee'; // Menentukan nama tabel secara eksplisit

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'position',
    ];

    protected $casts = [
        'hire_date' => 'date', // Mengonversi hire_date ke tipe date otomatis
    ];
    /**
     * Get all of the contracts for the employee.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
    public function performancereviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }
    public function contract()
    {
        return $this->hasOne(Contract::class, 'employee_id');
    }

}
