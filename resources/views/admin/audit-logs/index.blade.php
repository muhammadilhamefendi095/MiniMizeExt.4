<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Audit Log</h2>
            <p>Riwayat semua aktivitas penting di sistem</p>
        </div>

        <form method="GET" style="margin-bottom:30px;">
            <input type="text" name="action" value="{{ request('action') }}" placeholder="Filter aksi, misal: artwork, bid, order..."
                   style="padding:12px; width:320px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
            <button class="nav-btn" style="padding:12px 20px; font-size:0.75rem;">Filter</button>
        </form>

        <div class="table-wrapper">
            <table class="minimal-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Detail</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td style="white-space:nowrap; font-size:0.8rem;">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                            <td>{{ $log->user->name ?? 'Sistem' }}</td>
                            <td><span class="status-badge">{{ $log->action }}</span></td>
                            <td style="font-size:0.8rem; color:#999;">
                                @foreach ($log->meta ?? [] as $key => $value)
                                    <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                                @endforeach
                            </td>
                            <td style="font-size:0.75rem; color:#666;">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="color:#666;">Belum ada aktivitas tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:30px;">{{ $logs->links() }}</div>
    </section>
</x-app-layout>
