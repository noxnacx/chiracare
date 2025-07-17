<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TrainingUnit;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ฟังก์ชันแสดงฟอร์มการเพิ่มผู้ใช้
    public function create()
    {
        // ดึงข้อมูล training_unit ทั้งหมด
        $trainingUnits = TrainingUnit::all();
        $users = User::with('trainingUnit')->get();
        // ส่งข้อมูลไปยัง View
        return view('admin.add_user_form', compact('trainingUnits', 'users'));
    }

    // ฟังก์ชันบันทึกข้อมูลผู้ใช้
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:4',
            'role' => 'required',
            'training_unit_id' => 'nullable|exists:training_unit,id',  // ใช้เฉพาะเมื่อ role เป็น training_unit
        ]);

        // สร้างผู้ใช้ใหม่
        $user = new User();
        $user->username = $request->username;
        $user->password = bcrypt($request->password);  // เข้ารหัสรหัสผ่าน
        $user->role = $request->role;  // กำหนด role

        // ถ้าเลือก role เป็น training_unit
        if ($request->role == 'training_unit') {
            $user->training_unit_id = $request->training_unit_id;  // ผูกกับ training_unit
        } else {
            $user->training_unit_id = null;  // ถ้าเลือก role อื่น ๆ ตั้งค่า training_unit_id เป็น NULL
        }

        // บันทึกผู้ใช้
        $user->save();

        // ส่งผู้ใช้กลับไปยังหน้าที่ต้องการพร้อมข้อความสำเร็จ
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function index()
    {
        // ดึงข้อมูลผู้ใช้ทั้งหมดพร้อมกับข้อมูลที่เกี่ยวข้องกับ training unit
        $users = User::with('trainingUnit')->get();
        $trainingUnits = TrainingUnit::all();  // ดึงข้อมูล training units

        // ส่งข้อมูลไปยัง view
        return view('admin.add_user_form', compact('users', 'trainingUnits'));
    }


}