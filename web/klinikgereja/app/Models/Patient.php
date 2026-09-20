<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_number',
        'name',
        'nik',
        'gender',
        'birth_date',
        'phone',
        'address',
        'patient_type',
        'church_member_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function subsidies()
    {
        return $this->hasMany(Subsidy::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
