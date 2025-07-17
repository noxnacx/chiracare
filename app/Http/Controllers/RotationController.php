<?php

namespace App\Http\Controllers;

use App\Models\Rotation;
use App\Models\TrainingUnit;
use Illuminate\Http\Request;

class RotationController extends Controller
{

    public function totalRotation()
    {
        return Rotation::count();
    }
    // แสดงหน้าสร้าง Rotation และดึงข้อมูล Rotation & Training Unit
    public function show_rotation_page()
    {
        $rotations = Rotation::all();
        $units = TrainingUnit::all();

        return view('admin.rotation_training', compact('rotations', 'units'));
    }

    // เพิ่ม Rotation ใหม่ โดยมีค่าเริ่มต้น status เป็น 'active'
    public function store_rotation(Request $request)
    {
        $request->validate([
            'rotation_name' => 'required|string|max:255',
        ]);

        Rotation::create([
            'rotation_name' => $request->rotation_name,
            'status' => 'active', // ค่าเริ่มต้นเป็น 'active'
        ]);

        return redirect('/rotation_training')->with('success', 'Rotation added successfully!');
    }

    // ดึงข้อมูล Rotation ที่ต้องการแก้ไข
    public function edit_rotation($id)
    {
        $rotation = Rotation::findOrFail($id);
        return response()->json($rotation); // ส่งข้อมูลกลับในรูปแบบ JSON
    }

    // อัปเดตข้อมูล Rotation
    public function update_rotation(Request $request, $id)
    {
        $request->validate([
            'rotation_name' => 'required|string|max:255',
            'status' => 'in:active,inactive',
        ]);

        $rotation = Rotation::findOrFail($id);
        $rotation->update([
            'rotation_name' => $request->rotation_name,
            'status' => $request->status,
        ]);

        return redirect('/rotation_training')->with('success', 'Rotation updated successfully!');
    }

    // ลบ Rotation
    public function delete_rotation($id)
    {
        $rotation = Rotation::findOrFail($id);
        $rotation->delete();

        return redirect('/rotation_training')->with('success', 'Rotation deleted successfully!');
    }

    // เปลี่ยนสถานะ Rotation (active ↔ inactive)
    public function toggle_rotation_status($id)
    {
        $rotation = Rotation::findOrFail($id);
        $rotation->status = ($rotation->status == 'active') ? 'inactive' : 'active';
        $rotation->save();

        return redirect('/rotation_training')->with('success', 'Status updated successfully!');
    }
}
