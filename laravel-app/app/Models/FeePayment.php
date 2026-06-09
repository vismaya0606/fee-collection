<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'amount',
        'month',
        'year',
        'payment_date',
        'payment_mode',
        'receipt_no',
        'notes',
        'collected_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public static function generateReceiptNo(int $month, int $year): string
    {
        $prefix = 'RCP-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-';
        $last   = static::where('receipt_no', 'like', $prefix . '%')
            ->orderByDesc('receipt_no')
            ->value('receipt_no');

        $nextNum = $last ? (intval(substr($last, -4)) + 1) : 1;

        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }
}
