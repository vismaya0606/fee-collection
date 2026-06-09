<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class',
        'phone',
        'parent_phone',
        'source',
        'status',
        'notes',
        'follow_up_date',
        'converted_student_id',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function convertedStudent()
    {
        return $this->belongsTo(Student::class, 'converted_student_id');
    }

    public static function getStatuses(): array
    {
        return ['new', 'follow-up', 'interested', 'not-interested', 'converted'];
    }

    public static function getSources(): array
    {
        return ['Walk-in', 'Referral', 'Online', 'Social Media', 'Advertisement', 'Other'];
    }
}
