<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new patient account via mobile.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'nik' => ['required', 'string', 'max:20', 'unique:patients,nik'],
            'gender' => ['required', 'in:L,P'],
            'birth_date' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'patient_type' => ['required', 'in:JEMAAT,UMUM'],
            'church_member_number' => ['nullable', 'string', 'max:50'],
        ]);

        $result = DB::transaction(function () use ($validated) {
            $patientRole = Role::firstOrCreate(['name' => 'Pasien']);

            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role_id' => $patientRole->id,
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ]);

            $count = Patient::count() + 1;
            $patientNumber = 'RM-' . date('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $patient = Patient::create([
                'user_id' => $user->id,
                'patient_number' => $patientNumber,
                'name' => $validated['name'],
                'nik' => $validated['nik'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'patient_type' => $validated['patient_type'],
                'church_member_number' => $validated['church_member_number'] ?? null,
            ]);

            $token = $user->createToken('mobile-app')->plainTextToken;

            return [
                'user' => $user,
                'patient' => $patient,
                'token' => $token,
            ];
        });

        return response()->json([
            'message' => 'Registrasi akun pasien mobile berhasil.',
            'token' => $result['token'],
            'data' => [
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'username' => $result['user']->username,
                ],
                'patient' => $result['patient'],
            ],
        ], 201);
    }

    /**
     * Login patient account via mobile.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;
        $patient = $user->patient;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role?->name,
                ],
                'patient' => $patient,
            ],
        ]);
    }

    /**
     * Get current patient profile info.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $patient = $user->patient;

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role?->name,
                ],
                'patient' => $patient,
            ],
        ]);
    }

    /**
     * Logout and revoke mobile API token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
