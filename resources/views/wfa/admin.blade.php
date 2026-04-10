<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - WFA Scheduler</title>
    <link rel="icon" href="/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090f0b;
            --card-bg: #111a14;
            --emerald: #10b981;
            --text-main: #ecf3f0;
            --text-dim: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .admin-wrapper {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: var(--card-bg);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
        }

        .brand {
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--emerald);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .brand img {
            height: 40px;
            border-radius: 50%;
            background: white;
            padding: 2px;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            padding: 1.5rem 0;
            flex: 1;
        }

        .nav-link {
            padding: 1rem 1.5rem;
            color: var(--text-dim);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(16, 185, 129, 0.05);
            color: var(--emerald);
            border-left-color: var(--emerald);
        }

        .btn-logout {
            width: 100%;
            background: transparent;
            border: none;
            text-align: left;
            font-family: inherit;
            font-size: 1rem;
            cursor: pointer;
            color: #ef4444;
        }

        .btn-logout:hover {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 2rem 3rem;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 1rem;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Generic Admin UI Elements */
        h1, h2, h3 {
            color: var(--emerald);
            margin-bottom: 1rem;
        }

        .controls {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            max-width: 500px;
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
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-family: inherit;
            width: 100%;
        }

        .btn {
            background: var(--emerald);
            color: #000;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 0.5rem;
            transition: 0.2s;
        }
        
        .btn:hover {
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--emerald);
            color: var(--emerald);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .export-btns {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
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
            transition: 0.2s;
        }

        .btn-outline:hover:not([disabled]) {
            background: rgba(16, 185, 129, 0.1);
        }

        .btn-outline[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: var(--text-dim);
            color: var(--text-dim);
        }

        .stats-card {
            background: var(--card-bg);
            border: 1px solid var(--emerald);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        
        .stats-card h4 {
            color: var(--text-dim);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .stats-card .val {
            font-size: 2rem;
            font-weight: bold;
            color: var(--emerald);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        th, td {
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
        }

        @media print {
            body { background: white; color: black; height: auto; overflow: visible; }
            .sidebar, .topbar, form, .export-btns, #dosen-pagination, #bookings-pagination { display: none !important; }
            .admin-wrapper { display: block; }
            .main-content { padding: 0; overflow: visible; }
            .tab-content { display: block !important; } /* Tampilkan semua tab saat diprint jika diperlukan, atau filter via JS */
            .controls { display: none !important; }
            th { background: #f3f4f6; color: #000; border: 1px solid #ddd; }
            td { border: 1px solid #ddd; color: #000 !important; }
            table { border: 1px solid #ddd; margin-top: 20px; }
            h2, h3, .stats-card .val { color: black; }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand">
                <img src="/logo.png" alt="WFA Logo">
                <span>Admin Panel</span>
            </div>
            <nav class="nav-menu">
                <a href="#dashboard" class="nav-link active" onclick="switchTab('dashboard')">
                    <span style="font-size: 1.2rem;">📊</span> Dashboard
                </a>
                <a href="#dosen" class="nav-link" onclick="switchTab('dosen')">
                    <span style="font-size: 1.2rem;">👥</span> Manajemen Dosen
                </a>
                <a href="#booking" class="nav-link" onclick="switchTab('booking')">
                    <span style="font-size: 1.2rem;">📅</span> Rekap Booking
                </a>
                <a href="#setting" class="nav-link" onclick="switchTab('setting')">
                    <span style="font-size: 1.2rem;">⚙️</span> Pengaturan
                </a>
                
                <div style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link btn-logout">
                            <span style="font-size: 1.2rem;">🚪</span> Keluar / Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <a href="/" class="back-link">&larr; Beranda Utama Jadwal</a>
                <div style="color: var(--text-dim); font-size: 0.9rem;">Masuk sebagai Administrator</div>
            </header>

            @if(session('success'))
                <div class="alert-success">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- Tab 1: Dashboard -->
            <section id="section-dashboard" class="tab-content active">
                <h1>Selamat Datang di WFA Scheduler Admin</h1>
                <p style="color: var(--text-dim); margin-bottom: 2rem;">Gunakan panel di sebelah kiri untuk mengelola aspek sistem.</p>
                
                <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                    <div class="stats-card">
                        <h4>Total Dosen</h4>
                        <div class="val">{{ $dosens->total() }}</div>
                    </div>
                    <div class="stats-card" id="dashboard-booking-stat">
                        <h4>Total Booking Data</h4>
                        <div class="val">...</div>
                    </div>
                    <div class="stats-card" id="dashboard-unbooked-stat">
                        <h4>Belum Pilih Jadwal (Minggu Ini)</h4>
                        <div class="val">...</div>
                    </div>
                    <div class="stats-card">
                        <h4>Limit Harian Saat Ini</h4>
                        <div class="val">{{ $limit }}</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-top: 2rem;">
                    <!-- Matriks Jadwal -->
                    <div class="controls" style="max-width: 100%;">
                        <h3>Matriks WFA Minggu Ini</h3>
                        <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 1rem;">Memetakan siapa saja dosen yang bekerja dari rumah (WFA) dan yang beraktivitas di kampus (Kantor) pada minggu ini.</p>
                        <div id="matriks-jadwal-container" style="overflow-x: auto;">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                    
                    <!-- Kepatuhan -->
                    <div class="controls" style="max-width: 100%;">
                        <h3>Status Kuota WFA Dosen (Minggu Ini)</h3>
                        <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 1rem;">Gunakan data ini untuk mengingatkan dosen yang belum memilih jadwal WFA-nya.</p>
                        <div id="kepatuhan-jadwal-container" style="overflow-x: auto;">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tab 2: Manajemen Dosen -->
            <section id="section-dosen" class="tab-content">
                <h2>Manajemen Dosen</h2>
                <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
                    <!-- Table Section -->
                    <div style="flex: 1; min-width: 400px;">
                        <div id="dosen-table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>NIP</th>
                                        <th>Nama Lengkap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dosens as $dosen)
                                        <tr>
                                            <td style="font-weight:bold;">{{ $dosen->nip ?? 'Belum ada NIP' }}</td>
                                            <td>{{ $dosen->name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="empty-state">Belum ada dosen terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($dosens->hasPages())
                        <div id="dosen-pagination" style="display: flex; justify-content: space-between; align-items: center;">
                            @if($dosens->onFirstPage())
                                <button class="btn-outline" disabled>&laquo; Sebelumnya</button>
                            @else
                                <a href="{{ $dosens->previousPageUrl() }}" class="btn-outline">&laquo; Sebelumnya</a>
                            @endif

                            <span style="color: var(--text-dim); font-size: 0.9rem;">Halaman {{ $dosens->currentPage() }} dari {{ $dosens->lastPage() }}</span>

                            @if($dosens->hasMorePages())
                                <a href="{{ $dosens->nextPageUrl() }}" class="btn-outline">Selanjutnya &raquo;</a>
                            @else
                                <button class="btn-outline" disabled>Selanjutnya &raquo;</button>
                            @endif
                        </div>
                        @endif
                    </div>
                    
                    <!-- Form Section -->
                    <div class="controls" style="margin-top: 0; min-width: 300px;">
                        <h3>Tambah Dosen</h3>
                        <form action="/admin/dosen" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>NIP:</label>
                                <input type="text" name="nip" required placeholder="Masukkan NIP...">
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap:</label>
                                <input type="text" name="name" required placeholder="Masukkan Nama Lengkap...">
                            </div>
                            <button type="submit" class="btn">Simpan Data Dosen</button>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Tab 3: Rekap Booking -->
            <section id="section-booking" class="tab-content">
                <h2>Seluruh Laporan Booking WFA</h2>
                <div class="export-btns">
                    <button onclick="exportToExcel()" class="btn-outline">📥 Download Excel (CSV)</button>
                    <button onclick="printAllBookings()" class="btn-outline">🖨️ Print ke PDF</button>
                </div>
                
                <div id="stats-container" style="display: none;"></div>

                <div id="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal WFA</th>
                                <th>NIP</th>
                                <th>Nama Dosen</th>
                                <th>Waktu Entry/Booking</th>
                            </tr>
                        </thead>
                        <tbody id="bookings-body">
                            <!-- Rows will be added by JS -->
                        </tbody>
                    </table>
                </div>
                <div id="bookings-pagination"></div>
            </section>

            <!-- Tab 4: Pengaturan -->
            <section id="section-setting" class="tab-content">
                <h2>Pengaturan Sistem</h2>
                <div class="controls">
                    <h3>Aturan Booking</h3>
                    <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 1rem;">Tentukan berapa banyak dosen (kuota maksimal) yang diperbolehkan WFA di tanggal yang sama setiap harinya.</p>
                    <form action="/admin/setting" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Batas Limit Kuota WFA per Hari:</label>
                            <input type="number" name="daily_limit" value="{{ $limit }}" min="1" required>
                        </div>
                        <button type="submit" class="btn">Simpan Perubahan</button>
                    </form>
                </div>
            </section>

            <footer style="margin-top: auto; text-align: center; color: var(--text-dim); padding-top: 1.5rem; padding-bottom: 0.5rem; font-size: 0.85rem; border-top: 1px solid rgba(255,255,255,0.05);">
                &copy; 2026. &trade;ieki_app v.1.0.0. Hak Cipta Dilindungi.
            </footer>
        </main>
    </div>

    <script>
        let allBookingsData = [];
        let bookingPage = 1;
        const bookingPerPage = 10;

        // Auto-switch tab based on URL or session interaction
        document.addEventListener('DOMContentLoaded', () => {
            fetchAdminData();
            
            // Check if URL has valid pagination params or hash
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('dosen_page')) {
                switchTab('dosen');
            } else if (window.location.hash) {
                const tabId = window.location.hash.substring(1);
                if (['dashboard', 'dosen', 'booking', 'setting'].includes(tabId)) {
                    switchTab(tabId);
                }
            }
        });

        function switchTab(tabId) {
            // Update Menu
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            const activeLink = Array.from(document.querySelectorAll('.nav-link')).find(link => link.getAttribute('onclick').includes(tabId));
            if(activeLink) activeLink.classList.add('active');

            // Update Sections
            document.querySelectorAll('.tab-content').forEach(sec => sec.classList.remove('active'));
            document.getElementById('section-' + tabId).classList.add('active');
            
            // Update URL Hash without scroll
            if(history.pushState) {
                history.pushState(null, null, '#' + tabId);
            } else {
                window.location.hash = '#' + tabId;
            }
        }

        async function fetchAdminData() {
            const res = await fetch(`/api/admin/bookings`);
            const data = await res.json();
            allBookingsData = data.bookings;
            
            // Update logic for Dashboard stat
            document.getElementById('dashboard-booking-stat').querySelector('.val').innerText = data.count;
            
            renderDashboardStats(data);
            renderData();
        }

        function renderDashboardStats(data) {
            const dosens = data.dosens || [];
            const bookings = data.bookings || [];
            
            const curr = new Date();
            const day = curr.getDay(),
                  diff = curr.getDate() - day + (day == 0 ? -6:1);
            const monday = new Date(curr.setDate(diff));
            
            let weekDates = [];
            for(let i=0; i<5; i++) {
                let d = new Date(monday);
                d.setDate(monday.getDate() + i);
                
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                const yyyy = d.getFullYear();
                
                weekDates.push({
                    raw: `${yyyy}-${m}-${dd}`,
                    label: d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short' })
                });
            }
            
            let matriksHtml = `<table><thead><tr><th>Nama Dosen</th>`;
            weekDates.forEach(day => {
                matriksHtml += `<th>${day.label}</th>`;
            });
            matriksHtml += `</tr></thead><tbody>`;
            
            dosens.forEach(dosen => {
                let dosenBookings = bookings.filter(b => b.user_nip === dosen.nip || b.user_name === dosen.name);
                let row = `<tr><td>${dosen.name}</td>`;
                
                let wfaCount = 0;
                weekDates.forEach(day => {
                    let isWfa = dosenBookings.some(b => b.booking_date === day.raw);
                    if(isWfa) {
                        row += `<td style="color: var(--emerald); font-weight: bold; font-size: 0.9rem;">🏠 WFA</td>`;
                        wfaCount++;
                    } else {
                        row += `<td style="color: var(--text-dim); font-size: 0.9rem;">🏢 Kantor</td>`;
                    }
                });
                row += `</tr>`;
                matriksHtml += row;
                
                dosen.wfaCountThisWeek = wfaCount;
            });
            matriksHtml += `</tbody></table>`;
            document.getElementById('matriks-jadwal-container').innerHTML = matriksHtml;
            
            let kuotaHtml = `<table><thead><tr><th>NIP</th><th>Nama Dosen</th><th>WFA Minggu Ini</th><th>Status Lengkap</th></tr></thead><tbody>`;
            let unbookedCount = 0;
            dosens.forEach(dosen => {
                let status = dosen.wfaCountThisWeek >= 1 
                             ? `<span style="color: var(--emerald); font-weight: bold;">✅ Sudah Memilih</span>` 
                             : `<span style="color: #ef4444; font-weight: bold;">❌ Belum Memilih</span>`;
                
                if (dosen.wfaCountThisWeek === 0) unbookedCount++;
                
                kuotaHtml += `<tr>
                    <td>${dosen.nip ?? '-'}</td>
                    <td>${dosen.name}</td>
                    <td style="font-weight: bold;">${dosen.wfaCountThisWeek} Hari</td>
                    <td>${status}</td>
                </tr>`;
            });
            kuotaHtml += `</tbody></table>`;
            document.getElementById('kepatuhan-jadwal-container').innerHTML = kuotaHtml;
            
            const unbookedEl = document.getElementById('dashboard-unbooked-stat');
            if (unbookedEl) {
                unbookedEl.querySelector('.val').innerText = unbookedCount + " Orang";
                unbookedEl.querySelector('.val').style.color = unbookedCount > 0 ? "#ef4444" : "var(--emerald)";
            }
        }

        function renderData() {
            const tbody = document.getElementById('bookings-body');
            tbody.innerHTML = '';

            if (allBookingsData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Belum ada riwayat booking terdaftar.</td></tr>';
                renderPagination();
                return;
            }

            const start = (bookingPage - 1) * bookingPerPage;
            const end = start + bookingPerPage;
            const paginatedData = allBookingsData.slice(start, end);

            paginatedData.forEach(b => {
                tbody.innerHTML += `
                    <tr>
                        <td style="font-weight:bold; color:var(--emerald);">${b.booking_date_formatted}</td>
                        <td>${b.user_nip}</td>
                        <td>${b.user_name}</td>
                        <td style="color: var(--text-dim); font-size: 0.8rem;">${b.booked_at}</td>
                    </tr>
                `;
            });

            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(allBookingsData.length / bookingPerPage);
            const pagContainer = document.getElementById('bookings-pagination');
            
            if (totalPages <= 1) {
                pagContainer.innerHTML = '';
                return;
            }
            
            let paginationHtml = '<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">';
            
            if (bookingPage > 1) {
                paginationHtml += `<button class="btn-outline" onclick="changeBookingPage(${bookingPage - 1})">&laquo; Sebelumnya</button>`;
            } else {
                paginationHtml += `<button class="btn-outline" disabled>&laquo; Sebelumnya</button>`;
            }

            paginationHtml += `<span style="color: var(--text-dim); font-size: 0.9rem;">Halaman ${bookingPage} dari ${totalPages}</span>`;

            if (bookingPage < totalPages) {
                paginationHtml += `<button class="btn-outline" onclick="changeBookingPage(${bookingPage + 1})">Selanjutnya &raquo;</button>`;
            } else {
                paginationHtml += `<button class="btn-outline" disabled>Selanjutnya &raquo;</button>`;
            }
            
            paginationHtml += '</div>';
            pagContainer.innerHTML = paginationHtml;
        }

        function changeBookingPage(page) {
            bookingPage = page;
            renderData();
        }

        function exportToExcel() {
            if (allBookingsData.length === 0) {
                alert('Tidak ada data untuk di-export.');
                return;
            }

            // Headers
            let csvContent = "Tanggal WFA,NIP,Nama Lengkap,Waktu Entry/Booking\n";

            // Rows
            allBookingsData.forEach(row => {
                let r = [row.booking_date_formatted, row.user_nip, row.user_name, row.booked_at];
                let csvRow = r.map(field => `"${field}"`).join(",");
                csvContent += csvRow + "\r\n";
            });

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

        window.onafterprint = function() {
            if(allBookingsData.length > 0) {
                renderData();
            }
            // Restore hidden sections automatically via CSS media query
        };

        function printAllBookings() {
            // First display section-booking if it's not active just to be safe it prints correctly
            document.querySelectorAll('.tab-content').forEach(sec => sec.classList.remove('active'));
            document.getElementById('section-booking').classList.add('active');

            const tbody = document.getElementById('bookings-body');
            tbody.innerHTML = '';
            
            allBookingsData.forEach(b => {
                tbody.innerHTML += `
                    <tr>
                        <td style="font-weight:bold; color:var(--emerald);">${b.booking_date_formatted}</td>
                        <td>${b.user_nip}</td>
                        <td>${b.user_name}</td>
                        <td style="color: var(--text-dim); font-size: 0.8rem;">${b.booked_at}</td>
                    </tr>
                `;
            });
            
            window.print();
        }
    </script>
</body>

</html>