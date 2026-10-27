<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Astronova Travel & Reimbursement - Live Backend Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #0B0F19;
            --bg-card: rgba(18, 24, 38, 0.85);
            --bg-card-hover: rgba(26, 34, 53, 0.95);
            --border-color: rgba(255, 255, 255, 0.08);
            --primary: #38BDF8;
            --primary-glow: rgba(56, 189, 248, 0.25);
            --accent-green: #34D399;
            --accent-purple: #A78BFA;
            --accent-amber: #FBBF24;
            --accent-rose: #FB7185;
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(56, 189, 248, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 70%, rgba(167, 139, 250, 0.08) 0%, transparent 45%);
            background-attachment: fixed;
            padding: 2rem 1.5rem;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-badge {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, #0284C7, #6366F1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #FFFFFF, #93C5FD);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .badge-live {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            background: rgba(52, 211, 153, 0.12);
            color: var(--accent-green);
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-green);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
        }

        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            backdrop-filter: blur(12px);
            transition: all 0.25s ease;
        }

        .card:hover {
            border-color: rgba(255, 255, 255, 0.18);
            transform: translateY(-2px);
            background: var(--bg-card-hover);
        }

        .stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .stat-desc {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .main-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2.5rem;
        }

        @media (max-width: 900px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .interactive-box {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        input, select {
            background: rgba(11, 15, 25, 0.8);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus, select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .btn-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 0.5rem;
        }

        button {
            cursor: pointer;
            border: none;
            padding: 0.65rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0284C7, #2563EB);
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
        }

        .btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        pre.code-terminal {
            background: #060913;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 1.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            color: #38BDF8;
            max-height: 380px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.85rem;
        }

        th {
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-secondary);
            font-weight: 600;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-primary);
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .status-pill {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-disetujui, .status-selesai { background: rgba(52, 211, 153, 0.15); color: #34D399; }
        .status-menunggu_persetujuan, .status-diajukan { background: rgba(251, 191, 36, 0.15); color: #FBBF24; }
        .status-draft { background: rgba(148, 163, 184, 0.15); color: #94A3B8; }
        .status-ditolak, .status-cancelled { background: rgba(251, 113, 133, 0.15); color: #FB7185; }

        footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="logo-area">
                <div class="logo-badge">🚀</div>
                <div>
                    <h1>ASTRONOVA ENTERPRISE</h1>
                    <div class="subtitle">Sistem Manajemen Perjalanan Dinas & Reimbursement Dinamis</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="badge-live">
                    <span class="pulse-dot"></span>
                    <span>Database MySQL Terhubung</span>
                </div>
                <div class="badge-live" style="background: rgba(56, 189, 248, 0.12); color: var(--primary); border-color: rgba(56, 189, 248, 0.3);">
                    <span>Laravel v{{ app()->version() }} / PHP v{{ PHP_VERSION }}</span>
                </div>
            </div>
        </header>

        <!-- KPI Stats Cards (Direct from Persistent Database) -->
        <div class="grid-stats">
            <div class="card">
                <div class="stat-label">Total Pengajuan Perjalanan</div>
                <div class="stat-value" id="kpi-perjalanan">{{ \App\Models\PengajuanPerjalananTugas::count() }}</div>
                <div class="stat-desc">{{ \App\Models\PengajuanPerjalananTugas::where('status', 'disetujui')->count() }} Disetujui / {{ \App\Models\PengajuanPerjalananTugas::where('status', 'menunggu_persetujuan')->count() }} Menunggu</div>
            </div>
            <div class="card">
                <div class="stat-label">Total Klaim Reimbursement</div>
                <div class="stat-value" id="kpi-reimburse">Rp {{ number_format(\App\Models\PengajuanPenggantian::sum('total_pengeluaran'), 0, ',', '.') }}</div>
                <div class="stat-desc">{{ \App\Models\PengajuanPenggantian::count() }} Dokumen Terverifikasi</div>
            </div>
            <div class="card">
                <div class="stat-label">Total Pengeluaran Riil</div>
                <div class="stat-value" id="kpi-pengeluaran">Rp {{ number_format(\App\Models\PengeluaranPerjalanan::sum('jumlah'), 0, ',', '.') }}</div>
                <div class="stat-desc">{{ \App\Models\PengeluaranPerjalanan::count() }} Transaksi Tercatat</div>
            </div>
            <div class="card">
                <div class="stat-label">Master Data Aktif</div>
                <div class="stat-value" id="kpi-karyawan">{{ \App\Models\Karyawan::count() }} Karyawan</div>
                <div class="stat-desc">{{ \App\Models\Departemen::count() }} Departemen / {{ \App\Models\Proyek::count() }} Proyek</div>
            </div>
        </div>

        <!-- Main Interactive Layout -->
        <div class="main-layout">
            <!-- Left: Interactive API Runner -->
            <div class="card">
                <div class="section-title">
                    <span>⚡</span>
                    <span>Live Backend Console & API Runner</span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                    Jalankan request langsung ke endpoint Laravel API untuk menguji data persisten database MySQL secara real-time.
                </p>

                <div class="interactive-box">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="input-group">
                            <label>Email Akun</label>
                            <input type="email" id="auth-email" value="admin@astronova.co.id">
                        </div>
                        <div class="input-group">
                            <label>Password</label>
                            <input type="password" id="auth-password" value="password123">
                        </div>
                    </div>
                    
                    <div class="btn-row">
                        <button class="btn-primary" onclick="loginAuth()">1. Login & Generate Token</button>
                        <button class="btn-secondary" onclick="fetchEndpoint('/api/me')">2. Get Profile (/me)</button>
                        <button class="btn-secondary" onclick="fetchEndpoint('/api/dashboard/stats')">3. Get Stats</button>
                        <button class="btn-secondary" onclick="fetchEndpoint('/api/pengajuan-perjalanan')">4. List Perjalanan</button>
                        <button class="btn-secondary" onclick="fetchEndpoint('/api/laporan/pengeluaran')">5. Laporan Pengeluaran</button>
                    </div>

                    <div class="input-group" style="margin-top: 0.5rem;">
                        <label>Simulasi Tambah Data Cepat (Langsung Tersimpan ke MySQL)</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" id="new-dept-name" placeholder="Nama Departemen Baru..." style="flex: 1;">
                            <button class="btn-primary" onclick="createDepartemen()">Simpan ke DB</button>
                        </div>
                    </div>

                    <div style="margin-top: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <label>JSON Response Output Real-Time:</label>
                            <span id="response-status" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-green);"></span>
                        </div>
                        <pre class="code-terminal" id="api-output">// Klik salah satu tombol di atas untuk melihat respon API secara langsung...</pre>
                    </div>
                </div>
            </div>

            <!-- Right: Real Database Data Records -->
            <div class="card">
                <div class="section-title">
                    <span>📋</span>
                    <span>Data Riil Pengajuan Perjalanan Dinas Terbaru</span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                    Data aktual dari tabel <code>pengajuan_perjalanan_tugas</code> di database MySQL.
                </p>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Karyawan</th>
                                <th>Tujuan</th>
                                <th>Estimasi Biaya</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="perjalanan-table-body">
                            @forelse (\App\Models\PengajuanPerjalananTugas::with(['karyawan', 'tujuan'])->latest('id_pengajuan')->limit(6)->get() as $item)
                                <tr>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">{{ $item->nomor_pengajuan }}</td>
                                    <td>{{ $item->karyawan?->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $item->tujuan?->nama_tujuan ?? '-' }}</td>
                                    <td>Rp {{ number_format($item->estimasi_biaya, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="status-pill status-{{ $item->status instanceof \App\Enums\StatusPengajuanEnum ? $item->status->value : $item->status }}">
                                            {{ $item->status instanceof \App\Enums\StatusPengajuanEnum ? $item->status->label() : $item->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data perjalanan tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 1.5rem;">
                    <div class="section-title" style="font-size: 1rem;">
                        <span>🏢</span>
                        <span>Daftar Departemen & Jumlah Karyawan</span>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Departemen</th>
                                    <th>Karyawan Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (\App\Models\Departemen::withCount('karyawan')->get() as $dept)
                                    <tr>
                                        <td>#{{ $dept->id_departemen }}</td>
                                        <td><strong>{{ $dept->nama_departemen }}</strong></td>
                                        <td>{{ $dept->karyawan_count }} Karyawan</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <div>
                &copy; {{ date('Y') }} PT Astronova - Sistem Backend Terverifikasi & Bebas Mock Data.
            </div>
            <div>
                Arsitektur: Laravel 13 MVC & REST API Sanctum | Format Response: JSON Standar
            </div>
        </footer>
    </div>

    <!-- Client-side script for API testing -->
    <script>
        let authToken = localStorage.getItem('astronova_token') || '';

        async function loginAuth() {
            const email = document.getElementById('auth-email').value;
            const password = document.getElementById('auth-password').value;
            const output = document.getElementById('api-output');
            const statusLabel = document.getElementById('response-status');

            output.textContent = '// Menghubungi POST /api/login...';
            statusLabel.textContent = 'CONNECTING...';
            statusLabel.style.color = '#FBBF24';

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();
                statusLabel.textContent = `HTTP ${res.status} ${res.ok ? 'OK' : 'ERROR'}`;
                statusLabel.style.color = res.ok ? '#34D399' : '#FB7185';

                if (data.success && data.data && data.data.token) {
                    authToken = data.data.token;
                    localStorage.setItem('astronova_token', authToken);
                }
                output.textContent = JSON.stringify(data, null, 2);
            } catch (err) {
                statusLabel.textContent = 'NETWORK ERROR';
                statusLabel.style.color = '#FB7185';
                output.textContent = String(err);
            }
        }

        async function fetchEndpoint(url) {
            const output = document.getElementById('api-output');
            const statusLabel = document.getElementById('response-status');

            if (!authToken) {
                output.textContent = '// Perhatian: Token belum ada. Lakukan Login terlebih dahulu!';
                statusLabel.textContent = 'AUTH REQUIRED';
                statusLabel.style.color = '#FB7185';
                return;
            }

            output.textContent = `// Menghubungi GET ${url}...`;
            statusLabel.textContent = 'FETCHING...';
            statusLabel.style.color = '#FBBF24';

            try {
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const data = await res.json();
                statusLabel.textContent = `HTTP ${res.status} ${res.ok ? 'OK' : 'ERROR'}`;
                statusLabel.style.color = res.ok ? '#34D399' : '#FB7185';
                output.textContent = JSON.stringify(data, null, 2);
            } catch (err) {
                statusLabel.textContent = 'ERROR';
                statusLabel.style.color = '#FB7185';
                output.textContent = String(err);
            }
        }

        async function createDepartemen() {
            const nameInput = document.getElementById('new-dept-name');
            const name = nameInput.value.trim();
            const output = document.getElementById('api-output');
            const statusLabel = document.getElementById('response-status');

            if (!name) {
                alert('Silakan masukkan nama departemen!');
                return;
            }
            if (!authToken) {
                alert('Silakan lakukan login terlebih dahulu untuk mendapatkan token!');
                return;
            }

            output.textContent = '// Mengirim POST /api/departemen...';
            try {
                const res = await fetch('/api/departemen', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${authToken}`
                    },
                    body: JSON.stringify({ nama_departemen: name })
                });
                const data = await res.json();
                statusLabel.textContent = `HTTP ${res.status} ${res.ok ? 'CREATED' : 'ERROR'}`;
                statusLabel.style.color = res.ok ? '#34D399' : '#FB7185';
                output.textContent = JSON.stringify(data, null, 2);
                if (data.success) {
                    nameInput.value = '';
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (err) {
                statusLabel.textContent = 'ERROR';
                output.textContent = String(err);
            }
        }
    </script>
</body>
</html>
