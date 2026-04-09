<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WFA Scheduler - Modern Emerald</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090f0b;
            --card-bg: #111a14;
            --emerald: #10b981;
            --emerald-soft: #064e3b;
            --text-main: #ecf3f0;
            --text-dim: #94a3b8;
            --glass: rgba(16, 185, 129, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 3rem;
        }

        header h1 {
            font-size: 2.5rem;
            background: linear-gradient(to right, #10b981, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        header p {
            color: var(--text-dim);
            font-weight: 300;
        }

        /* Month Selector */
        .month-selector {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding: 1rem 0 1.5rem 0;
            /* extra padding for scrollbar */
            margin-bottom: 2rem;
            scroll-behavior: smooth;
        }

        /* Sleek scrollbar for the month selector */
        .month-selector::-webkit-scrollbar {
            height: 6px;
        }

        .month-selector::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
        }

        .month-selector::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.3);
            border-radius: 10px;
        }

        .month-selector::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.6);
        }

        .month-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1rem 2rem;
            border-radius: 12px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--text-dim);
        }

        .month-card.active {
            background: var(--emerald);
            color: #fff;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
            border-color: var(--emerald);
        }

        .month-card:hover:not(.active) {
            border-color: var(--emerald);
            transform: translateY(-2px);
        }

        /* Week Section */
        #schedule-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .week-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            animation: fadeIn 0.5s ease;
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

        .week-header {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--emerald);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .week-header::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--emerald);
            border-radius: 2px;
        }

        .days-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }

        .day-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .day-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .day-card.booked {
            background: var(--emerald-soft);
            border-color: var(--emerald);
        }

        .day-card:hover:not(.disabled):not(.booked) {
            background: rgba(16, 185, 129, 0.05);
            border-color: var(--emerald);
            cursor: pointer;
            transform: scale(1.02);
        }

        .day-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .day-date {
            font-size: 0.85rem;
            color: var(--text-dim);
            margin-bottom: 1rem;
        }

        .spots-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-dim);
        }

        .spots-count {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--emerald);
        }

        .progress-bar {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--emerald);
            transition: width 0.3s ease;
        }

        .full-text {
            color: #ef4444;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Loading Spinner */
        #loader {
            display: none;
            justify-content: center;
            padding: 3rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--glass);
            border-top: 3px solid var(--emerald);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 2rem;
            border-radius: 12px;
            background: var(--card-bg);
            border: 1px solid var(--emerald);
            color: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            transform: translateY(100px);
            transition: all 0.3s cubic-bezier(0.18, 0.89, 0.32, 1.28);
            z-index: 1000;
        }

        .toast.show {
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>WFA Scheduler</h1>
            <p>Pilih jadwal kerja remote Anda secara mingguan.</p>
        </header>

        <div class="month-selector" id="month-selector">
            <!-- JavaScript will populate months here -->
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div style="flex-grow: 1;">
                <label for="dosen-select" style="display: block; margin-bottom: 0.5rem; color: var(--text-dim);">Pilih
                    Nama Dosen (Untuk Booking):</label>
                <select id="dosen-select" onchange="handleDosenChange()"
                    style="background: var(--card-bg); color: var(--text-main); border: 1px solid var(--emerald); padding: 0.75rem 1rem; border-radius: 8px; width: 100%; max-width: 400px; font-family: inherit; font-size: 1rem; cursor: pointer; outline: none;">
                    <option value="">-- Pilih Dosen --</option>
                    @foreach($dosens as $dosen)
                        <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <a href="/login"
                    style="color: var(--emerald); text-decoration: none; font-size: 0.9rem; border: 1px solid var(--emerald); padding: 5px 15px; border-radius: 20px;">Admin
                    Login</a>
            </div>
        </div>

        <div id="loader">
            <div class="spinner"></div>
        </div>

        <div id="schedule-container">
            <!-- JavaScript will populate schedule here -->
        </div>
    </div>

    <div id="toast" class="toast">Berhasil!</div>

    <footer
        style="text-align: center; color: var(--text-dim); padding: 2rem 0; font-size: 0.85rem; margin-top: 3rem; border-top: 1px solid rgba(255,255,255,0.05);">
        &copy; 2026. &trade;ieki_app v.1.0.0. Hak Cipta Dilindungi.
    </footer>

    <script>
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const today = new Date();
        const currentMonth = today.getMonth() + 1;
        let selectedMonth = currentMonth;
        let userBookings = []; // Array of {id, date}
        let currentUserId = '';

        document.addEventListener('DOMContentLoaded', () => {
            initMonths();
            fetchSchedule(currentMonth);
        });

        function handleDosenChange() {
            currentUserId = document.getElementById('dosen-select').value;
            fetchSchedule(selectedMonth);
        }

        function initMonths() {
            const selector = document.getElementById('month-selector');
            for (let m = currentMonth; m <= 12; m++) {
                const card = document.createElement('div');
                card.className = `month-card ${m === currentMonth ? 'active' : ''}`;
                card.textContent = monthNames[m - 1];
                card.onclick = () => selectMonth(m, card);
                selector.appendChild(card);
            }
        }

        function selectMonth(m, el) {
            document.querySelectorAll('.month-card').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            selectedMonth = m;
            fetchSchedule(m);
        }

        async function fetchSchedule(month) {
            showLoader(true);
            try {
                let url = `/api/schedule/${month}`;
                if (currentUserId) {
                    url += `?user_id=${currentUserId}`;
                }
                const res = await fetch(url);
                const data = await res.json();
                userBookings = data.user_bookings || [];
                renderSchedule(data.weeks);
            } catch (err) {
                showToast("Gagal memuat jadwal", true);
            } finally {
                showLoader(false);
            }
        }

        function renderSchedule(weeks) {
            const container = document.getElementById('schedule-container');
            container.innerHTML = '';

            weeks.forEach((week, index) => {
                const weekDiv = document.createElement('div');
                weekDiv.className = 'week-card';
                weekDiv.style.animationDelay = `${index * 0.1}s`;

                const bookedDates = userBookings.map(b => b.date);
                const isWeekBooked = week.dates.some(d => bookedDates.includes(d.date));

                weekDiv.innerHTML = `
                    <div class="week-header">Minggu ${index + 1}</div>
                    <div class="days-grid">
                        ${week.dates.map(day => renderDay(day, isWeekBooked)).join('')}
                    </div>
                `;
                container.appendChild(weekDiv);
            });
        }

        function renderDay(day, isWeekBooked) {
            const bookedDates = userBookings.map(b => b.date);
            const isBooked = bookedDates.includes(day.date);
            const canBook = !isBooked && !isWeekBooked && !day.is_full && day.in_month;

            let classes = ['day-card'];
            if (isBooked) classes.push('booked');
            if (!canBook && !isBooked) classes.push('disabled');
            if (!day.in_month) classes.push('not-in-month');

            const spotsPercent = (5 - day.remaining) / 5 * 100;

            return `
                <div class="${classes.join(' ')}" ${canBook ? `onclick="bookDate('${day.date}')"` : ''}>
                    <div class="day-name">${day.day_name_id}</div>
                    <div class="day-date">${formatDate(day.date)}</div>
                    ${day.is_full && !isBooked ? '<div class="full-text">PENUH</div>' : `
                        <div class="spots-label">Sisa Kuota</div>
                        <div class="spots-count">${day.remaining}</div>
                        <div class="progress-bar"><div class="progress-fill" style="width: ${spotsPercent}%"></div></div>
                    `}
                    ${isBooked ? `
                        <div style="margin-top: 10px; font-size: 0.7rem; color: #fff; font-weight: bold;">SAYA DIPILIH</div>
                        <button onclick="cancelDate('${day.date}')" style="margin-top: 10px; background: #ef4444; border: none; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; cursor: pointer;">Batalkan</button>
                    ` : ''}
                </div>
            `;
        }

        async function bookDate(date) {
            if (!currentUserId) {
                showToast("Silakan pilih Nama Dosen terlebih dahulu", true);
                return;
            }
            if (!confirm(`Konfirmasi pilihan WFA untuk tanggal ${formatDate(date)}?`)) return;

            try {
                const res = await fetch('/api/book', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ date: date, user_id: currentUserId })
                });

                const data = await res.json();
                if (res.ok) {
                    showToast(data.message);
                    fetchSchedule(selectedMonth);
                } else {
                    showToast(data.message || "Gagal memilih jadwal", true);
                }
            } catch (err) {
                showToast("Terjadi kesalahan sistem", true);
            }
        }

        async function cancelDate(date) {
            event.stopPropagation();
            if (!currentUserId) return;
            if (!confirm(`Yakin ingin membatalkan jadwal WFA tanggal ${formatDate(date)}?`)) return;

            try {
                const res = await fetch('/api/cancel', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ date: date, user_id: currentUserId })
                });

                const data = await res.json();
                if (res.ok) {
                    showToast(data.message);
                    fetchSchedule(selectedMonth);
                } else {
                    showToast(data.message || "Gagal membatalkan", true);
                }
            } catch (err) {
                showToast("Terjadi kesalahan sistem", true);
            }
        }

        function formatDate(dateStr) {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }

        function showLoader(show) {
            document.getElementById('loader').style.display = show ? 'flex' : 'none';
        }

        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.style.borderColor = isError ? '#ef4444' : '#10b981';
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    </script>
</body>

</html>