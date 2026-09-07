<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_number' => $this->patient_number,
            'name' => $this->name,
            'nik' => $this->nik,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'phone' => $this->phone,
            'address' => $this->address,
            'patient_type' => $this->patient_type,
            'church_member_number' => $this->church_member_number,
        ];
    }
}
