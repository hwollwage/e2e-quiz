<x-layouts.auth title="Regist | Gym-In">
    <p class="auth-title">Buat Akun Baru</p>

    {{-- show validation error --}}
    @if ($errors->any())
        <div
            style="background:rgba(232,41,42,0.1);border:1px solid rgba(232,41,42,0.3);color:#f87171;padding:10px 14px;font-size:0.82rem;margin-bottom:16px;">
            <ul style="list-style:none;padding:0;margin:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- reg form --}}
    <form method="POST" action="/register">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-input" placeholder="Mamank Kuliner">
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" placeholder="pengguna@gmail.com" required>
        </div>
        <div class="form-group">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-input" required>
                <option value="" disabled selected>Pilih gender</option>
                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('gender')
                <div class="alert-error" style="margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Kata Sandi</label>
            <input type="password" name="password" class="form-input" placeholder="**********" required>
        </div>
        <div class="form-group">
            <label class="form-label">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="**********" required>
        </div>
        <button type="submit" class="btn-primary">Daftar</button>
    </form>

    {{-- name route into /login--}}
    <p class="auth-footer">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
</x-layouts.auth>