<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'user_id',
        'employee_id',
        'position',
        'department',
        'hire_date',
        'salary',
        'hourly_rate',
        'work_schedule',
        'permissions',
        'performance_rating',
        'last_review_date',
        'employment_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
    ];

    protected $casts = [
        'work_schedule' => 'array',
        'permissions' => 'array',
        'hire_date' => 'date',
        'last_review_date' => 'date',
        'salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function generateEmployeeId()
    {
        $employeeId = 'E' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
        return $employeeId;
    }
}
