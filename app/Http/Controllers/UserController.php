<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TrainingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect('/login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
            }

            if (Auth::user()->role !== 'admin') {
                return redirect('/login')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะ Admin เท่านั้น)');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $users = User::with('trainingUnit')->get();
        $trainingUnits = TrainingUnit::all();

        return view('admin.add_userform', compact('users', 'trainingUnits'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,opd,ipd,er,training_unit,adminhospital',
            'training_unit_id' => 'nullable|exists:training_unit,id|required_if:role,training_unit'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'training_unit_id' => $request->role === 'training_unit' ? $request->training_unit_id : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTrainingUnits()
    {
        try {
            $trainingUnits = TrainingUnit::select('id', 'unit_name')->get();

            return response()->json([
                'success' => true,
                'data' => $trainingUnits
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch training units',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $user = User::with('trainingUnit')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบผู้ใช้'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validationRules = [
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'role' => 'required|in:admin,opd,ipd,er,training_unit,adminhospital',
            'training_unit_id' => 'nullable|exists:training_unit,id|required_if:role,training_unit'
        ];

        if ($request->filled('password')) {
            $validationRules['password'] = 'string|min:8';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($id);

            $updateData = [
                'username' => $request->username,
                'role' => $request->role,
                'training_unit_id' => $request->role === 'training_unit' ? $request->training_unit_id : null,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'อัพเดทผู้ใช้สำเร็จ',
                'data' => $user->load('trainingUnit')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอัพเดทผู้ใช้ได้'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบผู้ใช้สำเร็จ'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถลบผู้ใช้ได้'
            ], 500);
        }
    }
}