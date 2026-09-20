@csrf
<div class="space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700">Nama Lengkap *</label>
            <input name="name" required value="{{ old('name', $user->name ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Username *</label>
            <input name="username" required value="{{ old('username', $user->username ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm">
            @error('username')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Role *</label>
            <select name="role_id" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm">
                <option value="">-- Pilih Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '*' }}</label>
            <input type="password" name="password" {{ isset($user) ? '' : 'required' }} class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Status *</label>
            <select name="status" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm">
                <option value="ACTIVE" {{ old('status', $user->status ?? 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                <option value="INACTIVE" {{ old('status', $user->status ?? '') === 'INACTIVE' ? 'selected' : '' }}>INACTIVE</option>
            </select>
            @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="flex items-center gap-3 pt-3">
        <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Simpan</button>
        <a href="{{ route('users.index') }}" class="text-sm text-slate-600 hover:underline">Batal</a>
    </div>
</div>
