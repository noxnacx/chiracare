<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait RolePermission
{
    /**
     * ตรวจสอบสิทธิ์การเข้าถึงตาม Role
     * 
     * @param array $allowedRoles
     * @param string $redirectRoute
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function checkRoleAccess(array $allowedRoles, string $redirectRoute = '/login')
    {
        if (!Auth::check()) {
            return redirect($redirectRoute)->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }
        
        $userRole = Auth::user()->role;
        
        if (!in_array($userRole, $allowedRoles)) {
            $allowedRolesText = $this->getRoleDisplayNames($allowedRoles);
            return redirect($redirectRoute)->with('error', "คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะ {$allowedRolesText} เท่านั้น)");
        }
        
        return null; // ผ่านการตรวจสอบ
    }

    /**
     * สร้าง Middleware สำหรับตรวจสอบ Role
     * 
     * @param array $allowedRoles
     * @return \Closure
     */
    protected function roleMiddleware(array $allowedRoles)
    {
        return function ($request, $next) use ($allowedRoles) {
            $redirect = $this->checkRoleAccess($allowedRoles);
            
            if ($redirect) {
                return $redirect;
            }
            
            return $next($request);
        };
    }

    /**
     * ตรวจสอบว่า User มีสิทธิ์หรือไม่ (ไม่ redirect)
     * 
     * @param array $allowedRoles
     * @return bool
     */
    protected function hasRoleAccess(array $allowedRoles): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return in_array(Auth::user()->role, $allowedRoles);
    }

    /**
     * แปลง Role เป็นชื่อที่แสดงผล
     * 
     * @param array $roles
     * @return string
     */
    private function getRoleDisplayNames(array $roles): string
    {
        $roleNames = [
            'admin' => 'ผู้ดูแลระบบ',
            'opd' => 'OPD',
            'ipd' => 'IPD',
            'er' => 'ER',
            'training_unit' => 'หน่วยฝึกอบรม'
        ];

        $displayNames = array_map(function($role) use ($roleNames) {
            return $roleNames[$role] ?? $role;
        }, $roles);

        return implode(', ', $displayNames);
    }
}