<x-guest-layout>
    <div class="auth-box">
        <h1>Masuk</h1>
        <p class="subtitle">Masuk ke akun pembeli atau artis kamu.</p>

        @if (session('status'))
            <div class="status-flash">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label>Kata Sandi</label>
                <input type="password" name="password" required>
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="remember" style="width:auto;">
                <label style="margin:0; text-transform:none;">Ingat saya</label>
            </div>

            <button type="submit" class="btn-submit">Masuk</button>

            <p style="text-align:center; margin-top:20px; font-size:0.85rem; color:#777;">
                Belum punya akun?
                <a href="{{ route('register.buyer') }}" style="color:#FFF200;">Daftar sebagai pembeli</a>
                atau
                <a href="{{ route('register.artist') }}" style="color:#FFF200;">daftar sebagai artis</a>
            </p>
        </form>
    </div>
</x-guest-layout>
