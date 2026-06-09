<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'class',
        'section',
        'parent_name',
        'parent_phone',
        'address',
        'status',
        'admission_date',
        'monthly_fee',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'monthly_fee'    => 'decimal:2',
    ];

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function hasPaidFee(int $month, int $year): bool
    {
        return $this->feePayments()
            ->where('month', $month)
            ->where('year', $year)
            ->exists();
    }

    public function getLatestReceiptForMonth(int $month, int $year): ?FeePayment
    {
        return $this->feePayments()
            ->where('month', $month)
            ->where('year', $year)
            ->latest()
            ->first();
    }
}
