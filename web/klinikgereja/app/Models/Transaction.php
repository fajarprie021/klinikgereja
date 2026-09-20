<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'patient_id',
        'medical_record_id',
        'service_cost',
        'subsidy_amount',
        'total_payment',
        'payment_method',
        'status',
        'paid_at',
        'cashier_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'service_cost' => 'decimal:2',
            'subsidy_amount' => 'decimal:2',
            'total_payment' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
