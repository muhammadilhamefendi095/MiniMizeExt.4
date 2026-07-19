<x-app-layout>
    <div class="auth-box">
        <h1>Daftar Sebagai Pembeli</h1>
        <p class="subtitle">Buat akun untuk mengikuti lelang dan membeli karya.</p>

        <form method="POST" action="{{ route('register.buyer') }}">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
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

            <button type="submit" class="btn-submit">Daftar</button>
        </form>
    </div>
</x-app-layout>
