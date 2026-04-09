<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WFA Admin - Monitor Peserta</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090f0b;
            --card-bg: #111a14;
            --emerald: #10b981;
            --text-main: #ecf3f0;
            --text-dim: #94a3b8;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1rem;
        }

        h1 {
            color: var(--emerald);
        }

        .controls {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .controls h3 {
            margin-top: 0;
            color: var(--emerald);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dim);
        }

        .form-group input {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-family: inherit;
            width: 100%;
            max-width: 300px;
        }

        .btn {
            background: var(--emerald);
            color: #000;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.5rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--emerald);
            color: var(--emerald);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .export-btns {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .btn-outline {
            background: transparent;
            color: var(--emerald);
            border: 1px solid var(--emerald);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline:hover {
            background: rgba(16, 185, 129, 0.1);
        }

        @media print {
            body {
                background: white;
                color: black;
                padding: 0;
            }

            .back-link,
            .controls,
            form,
            .export-btns,
            header p,
            footer,
            h1,
            h2:first-of-type,
            #dosen-table-container {
                display: none !important;
            }

            .container {
                max-width: 100%;
                margin: 0;
            }

            th {
                background: #f3f4f6;
                color: #000;
                border: 1px solid #ddd;
            }

            td {
                border: 1px solid #ddd;
                color: #000 !important;
            }

            table {
                border: 1px solid #ddd;
                margin-top: 20px;
            }

            h2 {
                color: black;
            }
        }

        .stats-card {
            background: var(--emerald);
            color: #000;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            margin-bottom: 1rem;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
        }

        th,
        td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        th {
            background: rgba(16, 185, 129, 0.1);
            color: var(--emerald);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-dim);
        }

        .back-link {
            color: var(--emerald);
            text-decoration: none;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <a href="/" class="back-link" style="margin-bottom: 0;">← Kembali ke Jadwal</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit"
                    style="background: none; color: #ef4444; border: 1px solid #ef4444; padding: 5px 15px; border-radius: 20px; cursor: pointer; font-family: inherit; font-size: 0.9rem;">Logout</button>
            </form>
        </div>
        <header>
            <h1>Admin Panel</h1>
            <p>Kelola Dosen, Limit, dan Monitor Booking.</p>
        </header>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Form Dosen -->
            <div class="controls">
                <h3>Tambah Dosen Baru</h3>
                <form action="/admin/dosen" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>NIP:</label>
                        <input type="text" name="nip" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Dosen:</label>
                        <input type="text" name="name" required>
                    </div>
                    <button type="submit" class="btn">Simpan Dosen</button>
                </form>
            </div>

            <!-- Form Settings -->
            <div class="controls">
                <h3>Pengaturan Aplikasi</h3>
                <form action="/admin/setting" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Limit Kuota WFA per Hari:</label>
                        <input type="number" name="daily_limit" value="{{ $limit }}" min="1" required>
                    </div>
                    <button type="submit" class="btn">Update Limit</button>
                </form>
            </div>
        </div>

        <h2 style="margin-bottom: 1rem;">Daftar Dosen Terdaftar</h2>
        <div id="dosen-table-container" style="margin-bottom: 2rem;">
            <table>
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama Dosen</th>
                        <th>Email Default</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosens as $dosen)
                        <tr>
                            <td style="font-weight:bold;">{{ $dosen->nip ?? 'Belum ada NIP' }}</td>
                            <td>{{ $dosen->name }}</td>
                            <td style="color: var(--text-dim); font-size: 0.9rem;">{{ $dosen->email }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-state">Belum ada dosen terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 style="margin-bottom: 1rem;">Daftar Semua Booking</h2>
        <div class="export-btns">
            <button onclick="exportToExcel()" class="btn-outline">Download Excel (CSV)</button>
            <button onclick="window.print()" class="btn-outline">Print ke PDF</button>
        </div>
        <div id="stats-container"></div>

        <div id="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal WFA</th>
                        <th>NIP</th>
                        <th>Nama Dosen</th>
                        <th>Waktu Booking</th>
                    </tr>
                </thead>
                <tbody id="bookings-body">
                    <!-- Rows will be added by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <footer
        style="text-align: center; color: var(--text-dim); padding: 2rem 0; font-size: 0.85rem; margin-top: 3rem; border-top: 1px solid rgba(255,255,255,0.05);">
        &copy; 2026. &trade;ieki_app v.1.0.0. Hak Cipta Dilindungi.
    </footer>

    <script>
        let allBookingsData = [];

        document.addEventListener('DOMContentLoaded', () => {
            fetchAdminData();
        });

        async function fetchAdminData() {
            const res = await fetch(`/api/admin/bookings`);
            const data = await res.json();
            allBookingsData = data.bookings;
            renderData(data);
        }

        function renderData(data) {
            const stats = document.getElementById('stats-container');
            stats.innerHTML = `<div class="stats-card">Total Seluruh Booking: ${data.count}</div>`;

            const tbody = document.getElementById('bookings-body');
            tbody.innerHTML = '';

            if (data.bookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Belum ada booking terdaftar.</td></tr>';
                return;
            }

            data.bookings.forEach(b => {
                tbody.innerHTML += `
                    <tr>
                        <td style="font-weight:bold; color:var(--emerald);">${b.booking_date}</td>
                        <td>${b.user_nip}</td>
                        <td>${b.user_name}</td>
                        <td style="color: var(--text-dim); font-size: 0.8rem;">${b.booked_at}</td>
                    </tr>
                `;
            });
        }

        function exportToExcel() {
            if (allBookingsData.length === 0) {
                alert('Tidak ada data untuk di-export.');
                return;
            }

            // Headers
            let csvContent = "Tanggal WFA,NIP,Nama Dosen,Waktu Booking\n";

            // Rows
            allBookingsData.forEach(row => {
                let r = [row.booking_date, row.user_nip, row.user_name, row.booked_at];
                // Escape commas by quoting string if needed
                let csvRow = r.map(field => `"${field}"`).join(",");
                csvContent += csvRow + "\r\n";
            });

            // Trigger download
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "Laporan_Booking_WFA.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>

</html>