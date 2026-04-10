<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WfaBooking;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WfaController extends Controller
{
    public function index()
    {
        $dosens = Dosen::all();
        return view('wfa.index', compact('dosens'));
    }

    public function getSchedule(Request $request, $month)
    {
        $year = Carbon::now()->year;
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $weeks = [];
        // Start from the first Monday that could belong to this month's first week
        $currentDate = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        
        $holidays = $this->getHolidays();

        while ($currentDate->lte($endOfMonth)) {
            $weekDates = [];
            for ($i = 0; $i < 5; $i++) {
                $date = $currentDate->copy()->addDays($i);
                $dateStr = $date->toDateString();
                
                $dayBookings = WfaBooking::with('dosen')->where('booking_date', $dateStr)->get();
                $bookingsCount = $dayBookings->count();
                $bookers = $dayBookings->map(fn($b) => $b->dosen->name)->toArray();
                $limitSetting = Setting::where('key', 'daily_limit')->first();
                $limit = $limitSetting ? (int)$limitSetting->value : 5; // Default limit 5
                
                $isHoliday = isset($holidays[$dateStr]);
                $holidayName = $isHoliday ? $holidays[$dateStr]['summary'] : null;

                $weekDates[] = [
                    'date' => $dateStr,
                    'day_name' => $date->format('l'),
                    'day_name_id' => $this->translateDay($date->format('l')),
                    'is_full' => $bookingsCount >= $limit,
                    'remaining' => $limit - $bookingsCount,
                    'in_month' => $date->month == $month,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                    'bookers' => $bookers
                ];
            }
            
            $weeks[] = [
                'week_number' => $currentDate->weekOfYear,
                'dates' => $weekDates
            ];
            
            $currentDate->addWeek();
        }

        $userId = $request->query('user_id');
        $userBookings = [];
        if ($userId) {
            $userBookings = WfaBooking::where('dosen_id', $userId)->get()
                ->map(fn($b) => [
                    'id' => $b->id,
                    'date' => $b->booking_date,
                ]);
        }

        return response()->json([
            'weeks' => $weeks,
            'user_bookings' => $userBookings
        ]);
    }

    private function translateDay($day)
    {
        $days = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat'
        ];
        return $days[$day] ?? $day;
    }

    private function getHolidays()
    {
        return Cache::remember('indo_holidays', 60 * 24 * 30, function () { // Cache for 30 days
            try {
                // adding withoutVerifying() to prevent SSL issues on local dev like Laragon
                $response = Http::withoutVerifying()->timeout(10)->get('https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/holidays.json');
                
                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                \Log::error("Gagal mengambil data libur: " . $e->getMessage());
            }
            return [];
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:dosens,id'
        ]);

        $date = Carbon::parse($request->date);
        $userId = $request->user_id;

        // Cek apakah tanggal adalah hari libur (backend protection)
        $holidays = $this->getHolidays();
        if (isset($holidays[$date->toDateString()])) {
            return response()->json(['message' => 'Tanggal ini adalah hari libur nasional.'], 422);
        }
        
        $limitSetting = Setting::where('key', 'daily_limit')->first();
        $limit = $limitSetting ? (int)$limitSetting->value : 5;

        // Rule: 1 day per week
        $weekNumber = $date->weekOfYear;
        $year = $date->year;

        $exists = WfaBooking::where('dosen_id', $userId)
            ->where('week_number', $weekNumber)
            ->where('year', $year)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah memilih hari WFA untuk minggu ini.'], 422);
        }

        // Rule: Daily limit
        $count = WfaBooking::where('booking_date', $request->date)->count();
        if ($count >= $limit) {
            return response()->json(['message' => 'Kuota untuk hari ini sudah penuh.'], 422);
        }

        WfaBooking::create([
            'dosen_id' => $userId,
            'booking_date' => $date->format('Y-m-d'),
            'week_number' => $weekNumber,
            'year' => $year,
        ]);

        return response()->json(['message' => 'Berhasil memilih jadwal WFA.']);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:dosens,id'
        ]);
        
        $booking = WfaBooking::where('dosen_id', $request->user_id)
            ->where('booking_date', $request->date)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Jadwal tidak ditemukan.'], 404);
        }

        $booking->delete();

        return response()->json(['message' => 'Jadwal berhasil dibatalkan.']);
    }

    public function admin()
    {
        $dosens = Dosen::latest()->paginate(5, ['*'], 'dosen_page');
        $limitSetting = Setting::where('key', 'daily_limit')->first();
        $limit = $limitSetting ? $limitSetting->value : 5;

        return view('wfa.admin', compact('dosens', 'limit'));
    }

    public function storeDosen(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|unique:dosens',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:dosens'
        ]);

        Dosen::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email ?? strtolower(str_replace(' ', '', $request->name)) . rand(10,99). '@example.com',
        ]);

        return back()->with('success', 'Dosen berhasil ditambahkan!');
    }

    public function storeSetting(Request $request)
    {
        $request->validate([
            'daily_limit' => 'required|integer|min:1',
        ]);

        Setting::updateOrCreate(
            ['key' => 'daily_limit'],
            ['value' => $request->daily_limit]
        );

        return back()->with('success', 'Limit per hari berhasil diupdate!');
    }

    public function getAdminBookings(Request $request)
    {
        $bookings = WfaBooking::with('dosen')
            ->orderBy('booking_date', 'desc')
            ->get();
            
        $allDosens = \App\Models\Dosen::all(['id', 'nip', 'name']);

        return response()->json([
            'count' => $bookings->count(),
            'dosens' => $allDosens,
            'bookings' => $bookings->map(fn($b) => [
                'user_name' => $b->dosen->name,
                'user_nip' => $b->dosen->nip ?? '-',
                'booking_date' => \Carbon\Carbon::parse($b->booking_date)->format('Y-m-d'),
                'booking_date_formatted' => \Carbon\Carbon::parse($b->booking_date)->format('d M Y'),
                'booked_at' => $b->created_at->format('d M Y H:i'),
            ])
        ]);
    }
}
