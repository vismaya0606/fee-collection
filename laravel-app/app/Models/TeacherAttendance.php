<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'date',
        'status',
        'notes',
        'approved',
        'approved_by',
    ];

    protected $casts = [
        'date'     => 'date',
        'approved' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function getStatuses(): array
    {
        return ['present', 'late', 'half_day', 'absent'];
    }
}
