<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VitalSignsController extends Controller
{
    public function getVitalSignsFromTreatment($treatmentId)
    {
        // ค้นหาข้อมูลจากตาราง treatment โดยใช้ treatment_id
        $treatment = DB::table('treatment')
            ->where('id', $treatmentId)
            ->first();

        if (!$treatment) {
            return response()->json(['message' => 'ไม่พบข้อมูลการรักษา'], 404);
        }

        // ค้นหาข้อมูลจาก appointment โดยใช้ appointment_id ที่เชื่อมโยงกับ treatment
        $appointment = DB::table('appointment')
            ->where('id', $treatment->checkin_id)  // ใช้ checkin_id หรือ appointment_id ตามที่เชื่อมโยง
            ->first();

        if (!$appointment) {
            return response()->json(['message' => 'ไม่พบข้อมูลการนัดหมาย'], 404);
        }

        // ค้นหาข้อมูลจาก medical_report โดยใช้ medical_report_id ที่เชื่อมโยงกับ appointment
        $medicalReport = DB::table('medical_report')
            ->where('id', $appointment->medical_report_id)
            ->first();

        if (!$medicalReport || !$medicalReport->vital_signs_id) {
            return response()->json(['message' => 'ไม่พบข้อมูล Vital Signs'], 404);
        }

        // ดึงข้อมูล vital_signs_id จาก medical_report
        $vitalSigns = DB::table('vital_signs')
            ->where('id', $medicalReport->vital_signs_id)
            ->first();

        if (!$vitalSigns) {
            return response()->json(['message' => 'ไม่พบข้อมูล Vital Signs'], 404);
        }

        return response()->json($vitalSigns);
    }

}