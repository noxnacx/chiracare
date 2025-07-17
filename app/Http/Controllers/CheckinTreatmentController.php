<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checkin;
use App\Models\Treatment;
use Carbon\Carbon;

class CheckinTreatmentController extends Controller
{


    public function getTodayCheckins()
    {
        $today = Carbon::now()->format('Y-m-d');

        $checkins = Checkin::with(['appointment', 'treatment'])
            ->whereHas('appointment', function ($query) use ($today) {
                $query->whereDate('appointment_date', $today);
            })
            ->get();

        return response()->json($checkins);
    }




    public function updateTreatmentStatus(Request $request)
    {
        $request->validate(['checkin_id' => 'required|exists:treatment,checkin_id',]);

        $treatment = Treatment::where('checkin_id', $request->checkin_id)->first();

        if (!$treatment) {
            return response()->json(['status' => 'error', 'message' => 'ไม่พบข้อมูลการรักษา'], 404);
        }

        $treatment->update(['treatment_status' => 'treated']);

        return response()->json(['status' => 'success', 'message' => 'สถานะการรักษาถูกอัปเดตแล้ว']);
    }




}