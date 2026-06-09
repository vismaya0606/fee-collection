<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'phone',
        'email',
        'salary',
        'join_date',
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
        'salary'    => 'decimal:2',
    ];

    public function salaries()
    {
        return $this->hasMany(TeacherSalary::class);
    }

    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function hasSalaryPaid(int $month, int $year): bool
    {
        return $this->salaries()
            ->where('month', $month)
            ->where('year', $year)
            ->exists();
    }
}
