<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WfaBooking;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class WfaController extends Controller
{
    public function index()
    {
        $dosens = User::where('is_admin', false)->get();
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
        
        while ($currentDate->lte($endOfMonth)) {
            $weekDates = [];
            for ($i = 0; $i < 5; $i++) {
                $date = $currentDate->copy()->addDays($i);
                
                $bookingsCount = WfaBooking::where('booking_date', $date->toDateString())->count();
                $limitSetting = Setting::where('key', 'daily_limit')->first();
                $limit = $limitSetting ? (int)$limitSetting->value : 5; // Default limit 5
                
                $weekDates[] = [
                    'date' => $date->toDateString(),
                    'day_name' => $date->format('l'),
                    'day_name_id' => $this->translateDay($date->format('l')),
                    'is_full' => $bookingsCount >= $limit,
                    'remaining' => $limit - $bookingsCount,
                    'in_month' => $date->month == $month
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
            $userBookings = WfaBooking::where('user_id', $userId)->get()
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

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id'
        ]);

        $date = Carbon::parse($request->date);
        $userId = $request->user_id;
        
        $limitSetting = Setting::where('key', 'daily_limit')->first();
        $limit = $limitSetting ? (int)$limitSetting->value : 5;

        // Rule: 1 day per week
        $weekNumber = $date->weekOfYear;
        $year = $date->year;

        $exists = WfaBooking::where('user_id', $userId)
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
            'user_id' => $userId,
            'booking_date' => $request->date,
            'week_number' => $weekNumber,
            'year' => $year
        ]);

        return response()->json(['message' => 'Berhasil memilih jadwal WFA.']);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id'
        ]);
        
        $booking = WfaBooking::where('user_id', $request->user_id)
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
        $dosens = User::where('is_admin', false)->get();
        $limitSetting = Setting::where('key', 'daily_limit')->first();
        $limit = $limitSetting ? $limitSetting->value : 5;

        return view('wfa.admin', compact('dosens', 'limit'));
    }

    public function storeDosen(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:users',
            'name' => 'required|string|max:255',
        ]);

        User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => strtolower(str_replace(' ', '', $request->name)) . rand(10,99). '@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
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
        $bookings = WfaBooking::with('user')
            ->orderBy('booking_date', 'desc')
            ->get();

        return response()->json([
            'count' => $bookings->count(),
            'bookings' => $bookings->map(fn($b) => [
                'user_name' => $b->user->name,
                'user_nip' => $b->user->nip ?? '-',
                'booking_date' => Carbon::parse($b->booking_date)->format('d M Y'),
                'booked_at' => $b->created_at->format('d M Y H:i'),
            ])
        ]);
    }
}
