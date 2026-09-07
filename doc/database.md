# Database Design Klinik Gereja

## users

Menyimpan akun pengguna.

Field: - id - name - email - password - role_id - status

## roles

Menyimpan hak akses.

Field: - id - name

## patients

Data pasien.

Field: - id - patient_number - name - nik - gender - birth_date -
phone - address - patient_type - church_member_number

patient_type: - JEMAAT - UMUM

## doctors

Data dokter.

Field: - id - user_id - name - specialization

## schedules

Jadwal dokter.

Field: - id - doctor_id - day - start_time - end_time

## appointments

Data antrean.

Field: - id - patient_id - doctor_id - date - queue_number - status

Status: - WAITING - CHECKED - DONE - CANCELLED

## medical_records

Rekam medis.

Field: - id - patient_id - doctor_id - appointment_id - complaint -
diagnosis - treatment - notes

## vital_signs

Pemeriksaan awal.

Field: - id - medical_record_id - blood_pressure - temperature -
weight - height

## subsidies

Data bantuan gereja.

Field: - id - patient_id - medical_record_id - amount - approved_by -
date

## transactions

Pembayaran pelayanan.

Field: - id - patient_id - medical_record_id - service_cost - subsidy -
total_payment - status

## church_funds

Dana pelayanan.

Field: - id - type - description - amount - date
