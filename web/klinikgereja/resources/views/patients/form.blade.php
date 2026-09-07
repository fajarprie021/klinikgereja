@if ($errors->any())
    <div class="text-red-600">{{ $errors->first() }}</div>
@endif
@foreach ([['patient_number','Patient number','text'],['name','Name','text'],['nik','NIK','text'],['gender','Gender','text'],['birth_date','Birth date','date'],['phone','Phone','text'],['church_member_number','Church member number','text']] as [$field, $label, $type])
    <label class="block"><span class="text-gray-700">{{ $label }}</span><input class="mt-1 block w-full rounded border-gray-300" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $patient->{$field} ?? '') }}"></label>
@endforeach
<label class="block"><span class="text-gray-700">Address</span><textarea class="mt-1 block w-full rounded border-gray-300" name="address">{{ old('address', $patient->address ?? '') }}</textarea></label>
<label class="block"><span class="text-gray-700">Patient type</span><select class="mt-1 block w-full rounded border-gray-300" name="patient_type"><option value="UMUM">UMUM</option><option value="JEMAAT" @selected(old('patient_type', $patient->patient_type ?? '') === 'JEMAAT')>JEMAAT</option></select></label>
