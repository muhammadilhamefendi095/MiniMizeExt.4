<x-app-layout>
    <div class="auth-box">
        <h1>Daftar Sebagai Artis / Submiter</h1>
        <p class="subtitle">Setelah mendaftar, kamu bisa unggah karya dari dashboard. Karya tayang setelah disetujui admin.</p>

        <form method="POST" action="{{ route('register.artist') }}">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap / Nama Seniman</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label>Nomor HP / WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required>
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label>Kata Sandi</label>
                <input type="password" name="password" required>
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label>Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <div class="form-group">
                <label>Bio Singkat / Latar Belakang Seni</label>
                <textarea name="bio" rows="4">{{ old('bio') }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Daftar</button>
        </form>
    </div>
</x-app-layout>
