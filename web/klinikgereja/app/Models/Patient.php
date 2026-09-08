<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
