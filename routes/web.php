<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\RotationController;
use App\Http\Controllers\TrainingUnitController;
use App\Http\Controllers\MedicalReportController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\HospitalAppointmentController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VitalSignsController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\ErController;
use App\Http\Controllers\NotificationController;
use App\Models\Rotation;
use App\Models\TrainingUnit;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\CheckinTreatmentController;
use App\Http\Controllers\HistoryTreatmentController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\IpdController;
use App\Http\Controllers\AdminHospitalController;
use App\Http\Controllers\SoldierController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\MentalHealthController;
use App\Models\Soldier;


// routes/api.php
Route::get('/all-top-diseases', [DashboardAdminController::class, 'alltop5Diseases']);

Route::get('/get-diseases-data', [DashboardAdminController::class, 'getDiseasesData']);


Route::get('/soldier-by-idcard', [ERController::class, 'getByIdCard'])->name('soldier.getByIdCard');
Route::post('/er/store', [ERController::class, 'store'])->name('er.store'); //บันทึกผู้ป่วย
Route::get('/er/form', [ERController::class, 'showForm'])->name('er.form');
Route::get('/er/patients', [ERController::class, 'getPatientsInER'])->name('er.patients');

Route::get('/soldier/{soldierId}/medical-history', [SoldierController::class, 'showMedicalHistory'])
    ->name('soldier.medicalHistory');

Route::post('/treatment/update-status', [CheckinTreatmentController::class, 'updateTreatmentStatus']);


Route::get('/checkins/today', [CheckinTreatmentController::class, 'getTodayCheckins']);
Route::post('/treatment/update-status', [TreatmentController::class, 'updateTreatmentStatus']);


Route::get('/opd/view-checkin', [TreatmentController::class, 'viewCheckin']);

Route::get('/treatments', [TreatmentController::class, 'getAllSoldiersTreatmentStatus']); // ดูรายชื่อทหารทั้งหมด
Route::get('/treatments/{id}', [TreatmentController::class, 'getSoldierTreatmentById']); // ดูข้อมูลการรักษาของทหารแต่ละคน
Route::put('/treatments/{id}/update-status', [TreatmentController::class, 'markSoldierAsTreated']); // อัปเดตสถานะการรักษา
Route::get('/treatments/{id}/checkin-status', [TreatmentController::class, 'getSoldierCheckinStatus']);
// ดูสถานะเช็คอิน
Route::get('/dashboard/training-unit/{id}', [TrainingUnitController::class, 'dashboardTrainingUnit'])
    ->name('dashboard.trainingunit');



Route::get('/appointments/scheduled-by-unit', [AppointmentController::class, 'listScheduledByUnit'])->name('appointments.scheduledByUnit');


Route::post('/appointments/reschedule-to-status', [AppointmentController::class, 'rescheduleStatus'])->name('appointments.rescheduleStatus');

Route::post('/appointments/reschedule-to-scheduled', [AppointmentController::class, 'rescheduleToScheduled'])->name('appointments.rescheduleToScheduled');

// เปลี่ยนชื่อ Route และ Method สำหรับการอัปเดตนัดหมาย
Route::post('/appointments/reschedule-update', [AppointmentController::class, 'rescheduleAppointment'])->name('appointments.rescheduleAppointment');

// เส้นทางสำหรับการแสดงนัดหมายสำเร็จ
Route::get('/appointments/success', [AppointmentController::class, 'success'])->name('appointments.success');

// routes/web.php




// เส้นทางสำหรับการแสดงนัดหมายสำเร็จ
Route::get('/appointments/success', [AppointmentController::class, 'success'])->name('appointments.success');



// ✅ Route แสดงหน้าเช็คอินและการรักษาของวันนี้
Route::get('/opd/view-checkin', [TreatmentController::class, 'viewCheckin'])->name('opd.view_checkin');

// Appointments Routes
Route::prefix('appointments')->group(function () {
    Route::get('/create/{medicalReportId}', [AppointmentController::class, 'showCreateAppointmentForm'])
        ->name('appointments.create');

    Route::post('/store', [AppointmentController::class, 'scheduleAppointments'])
        ->name('appointments.store');

    Route::get('/success-appointments', [AppointmentController::class, 'listApprovedAppointments'])
        ->name('appointments.success');
});

Route::prefix('hospital/appointments')->group(function () {
    Route::get('/', [HospitalAppointmentController::class, 'sentAppointments'])->name('hospital.sentAppointments');
    Route::post('/schedule', [HospitalAppointmentController::class, 'scheduleAppointments'])
        ->name('hospital.scheduleAppointments');
});

// Check-in Routes
Route::prefix('checkin')->group(function () {
    Route::get('/', [CheckinController::class, 'viewCheckin'])->name('checkin.view');
    Route::post('/id-card', [CheckinController::class, 'checkInByIDCard'])->name('checkin.idcard');
});

// Dashboard Route
Route::get('/', function () {
    $totalRotations = Rotation::count();
    $totalSoldiers = Soldier::count();
    $totalTrainingUnits = TrainingUnit::count();
    $soldiers = Soldier::with(['rotation', 'trainingUnit'])->get();

    return view('admin.dashboardadmin', compact('totalRotations', 'totalSoldiers', 'totalTrainingUnits', 'soldiers'));
});

// Soldier Routes
Route::prefix('soldier')->group(function () {
    Route::get('/soldier/{id}/view-assignment', [SoldierController::class, 'viewAssignment'])->name('soldier.view_assignment');
    Route::post('/accept-consent-session', [SoldierController::class, 'acceptConsentSession'])->name('accept.consent.session');
    Route::post('/accept-consent', [SoldierController::class, 'acceptConsent'])->name('accept.consent');

    Route::get('/create', [SoldierController::class, 'create_soldier']);
    Route::post('/store', [SoldierController::class, 'store_soldier']);
    Route::get('/total-soldiers', [SoldierController::class, 'getTotalSoldiers']);
    Route::delete('/delete/{id}', [SoldierController::class, 'delete_soldier'])->name('soldier.delete_soldier');
    Route::get('/edit/{id}', [SoldierController::class, 'edit_soldier'])->name('soldier.edit_soldier');
    Route::get('/image/{id}', [SoldierController::class, 'getImage'])->name('soldier.image');
    Route::get('/view/{id}', [SoldierController::class, 'view_soldier'])->name('soldier.view');
    Route::patch('/update/{id}', [SoldierController::class, 'update_soldier'])->name('soldier.update_soldier');
    Route::get('/individual/{id}', [SoldierController::class, 'individual_soldier'])->name('soldier.individual');
    Route::get('/login', [SoldierController::class, 'showLoginForm'])->name('soldier.login');
    Route::post('/authenticate', [SoldierController::class, 'authenticateSoldier'])->name('soldier.authenticate');
});



// Medical Report Routes
// ✅ Medical Report Routes (แก้ไขให้ชัดเจนขึ้น)
Route::prefix('medical')->group(function () {
    Route::get('/training-unit/{id}/create-medical-report', [MedicalReportController::class, 'showMedicalReportForm'])
        ->name('medicalReport.create');


    Route::post('/store', [MedicalReportController::class, 'saveMedicalReport'])
        ->name('medical.store');

    Route::post('/update-status', [MedicalReportController::class, 'updateStatus'])->name('medical.updateStatus');
    Route::get('/wait-appointment', [MedicalReportController::class, 'showWaitAppointment'])->name('wait_appointment');
    Route::get('/wait-appointment/{id}', [MedicalReportController::class, 'showWaitAppointment']);

    Route::get('/wait-hospital-appointment', [MedicalReportController::class, 'sentAppointments'])->name('hospital.waitAppointments');
    Route::get('/get-report/{id}', [MedicalReportController::class, 'getMedicalReport']);

    Route::get('/sent-appointments', [MedicalReportController::class, 'sentAppointments'])->name('hospital.sentAppointments');
});



// Training Units Routes
Route::get('/training-units', [TrainingUnitController::class, 'fetchTrainingUnit'])->name('training-units.index');

// Rotation and Training Unit Routes
Route::prefix('rotation_training')->group(function () {
    // Rotation Routes
    Route::get('/', [RotationController::class, 'show_rotation_page']);
    Route::post('/store', [RotationController::class, 'store_rotation']);
    Route::get('/edit/{id}', [RotationController::class, 'edit_rotation']);
    Route::put('/update/{id}', [RotationController::class, 'update_rotation']);
    Route::delete('/delete/{id}', [RotationController::class, 'delete_rotation']);
    Route::patch('/change-status/{id}', [RotationController::class, 'toggle_rotation_status']);

    // 🎯 Training Unit Routes
    Route::post('/store-training', [TrainingUnitController::class, 'store_trainingunit']);
    Route::get('/edit-training/{id}', [TrainingUnitController::class, 'edit_trainingunit']);
    Route::put('/update-training/{id}', [TrainingUnitController::class, 'update_trainingunit']);
    Route::delete('/delete-training/{id}', [TrainingUnitController::class, 'delete_trainingunit']);
    Route::patch('/toggle-status-training/{id}', [TrainingUnitController::class, 'toggle_status_trainingunit']);
});

// Training Unit Dashboard Route
Route::get('/missed-appointments', [AppointmentController::class, 'listMissedAppointments'])->name('appointments.missed');
// Define the route for updating missed appointments

Route::post('appointments/update-missed', [AppointmentController::class, 'updateMissedAppointments'])->name('appointments.update-missed');



Route::post('/treatment/add-diagnosis', [TreatmentController::class, 'addDiagnosis']);
Route::get('/api/vital-signs/from-treatment/{treatmentId}', [VitalSignsController::class, 'getVitalSignsFromTreatment']);
// เส้นทางสำหรับค้นหาข้อมูลโรคจากรหัส ICD10
Route::get('/diseases/{codes}', [TreatmentController::class, 'getDiseaseInfoByCodes']);





Route::get('/dashboard-admin', [DashboardAdminController::class, 'showDashboardAdmin'])->name('dashboard.admin');



Route::get('/hospital/statistics', [HistoryTreatmentController::class, 'showHospitalhistoryDetails'])->name('hospital.statistics');









// เส้นทางสำหรับแสดงยอดเจ็บป่วยสะสมและรายวัน




Route::post('/treatment/add-diagnosis', [TreatmentController::class, 'addDiagnosis'])->name('diagnosis.save');
;
Route::get('/diagnosis/form', [TreatmentController::class, 'showDiagnosisForm'])->name('diagnosis.form');
Route::get('/api/vital-signs/from-treatment/{treatmentId}', [VitalSignsController::class, 'getVitalSignsFromTreatment']);
Route::put('/treatment/update-vital-sign/{treatmentId}', [TreatmentController::class, 'updateVitalSign']);
Route::post('/treatment/create-follow-up-medical-report-and-appointment/{treatmentId}', [TreatmentController::class, 'createFollowUpMedicalReportAndAppointment']);



Route::get('/opd/dashboard', function () {
    return view('opd.dashboard_opd');
})->name('opd.dashboard');








Route::get('/approved-appointments', [AppointmentController::class, 'sentAppointments'])
    ->name('approved.appointments');








Route::get('/hospital/appointments/{id}/download', [HospitalAppointmentController::class, 'download'])->name('appointments.download');
Route::get('/hospital/appointments/download-all', [HospitalAppointmentController::class, 'downloadAll'])->name('appointments.downloadAll');

use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/pdf-thai-test', function () {
    $pdf = Pdf::loadView('pdf.thai-test')
        ->setOptions([
            'defaultFont' => 'Sarabun',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
            'enable_font_subsetting' => true,
        ]);

    return $pdf->download('thai-test.pdf');
});




// Route สำหรับเรียกข้อมูลในหน้า dashboard หรือรายงาน
Route::get('/hospital/static-details', [StaticController::class, 'showStaticDetails'])->name('hospital.static-details');
Route::get('/disease-statistics', [StaticController::class, 'getDiseaseStatistics'])->name('hospital.getDiseaseStatistics');


use App\Http\Controllers\OpdController;

Route::get('/hospital/opd-dashboard', action: [OpdController::class, 'OpdCountdashboard'])->name('opd.dashboard');



Route::get('/opd/history_opd', [OpdController::class, 'opdDiagnosisStats'])->name('opd.diagnosis.stats');
Route::get('/opd/appointmenttoday', [OpdController::class, 'viewTodayAppointment'])->name('opd.appointmenttoday');





//Route::get('/ipd/admit', [IpdController::class, 'admitList'])->name('ipd.admit_list');//แสดงข้อมูลController
Route::get('/soldier-by-idcard', [ERController::class, 'getByIdCard'])->name('soldier.getByIdCard');
Route::post('/er/store', [ERController::class, 'store'])->name('er.store'); //บันทึกผู้ป่วย
Route::get('/er/form', [ERController::class, 'showForm'])->name('er.form');
Route::get('/er/patients', [ERController::class, 'getPatientsInER'])->name('er.patients');
Route::get('/er/diagnosis/{treatmentId}', [ERController::class, 'showDiagnosisForm'])->name('er_diagnosis.page');


Route::get('/er/dashboard', [ErController::class, 'dashboardEr'])->name('er.dashboard');

Route::post('/er/add-diagnosis', [TreatmentController::class, 'addDiagnosis']);


Route::get('/er/diagnosis-stats', [ERController::class, 'erDiagnosisStats'])->name('er.diagnosis.stats');


Route::get('/er/today', [ERController::class, 'viewTodayAppointment'])
    ->name('er.today');

// 📱 Route สำหรับ API
Route::get('/er/appointments', [ERController::class, 'apiTodayAppointment'])
    ->name('er.appointments.api');

// หรือถ้าต้องการใส่ไว้ใน routes/api.php
// routes/api.php


Route::post('/er/add-diagnosis', [TreatmentController::class, 'addDiagnosis']);

Route::get('/diseases/ipd/{codes}', [IpdController::class, 'getDiseaseInfoByCodes']);
Route::put('/ipd/{treatmentId}/update-diagnosis', [IpdController::class, 'updateDiagnosisForm'])->name('treatment.updateDiagnosis');
//Route::post('/ipd/diagnosis/save', [IpdController::class,'saveDiagnosis'])->name('ipd_diagnosis.save');
Route::get('/ipd/diagnosis/{treatmentId}', [IpdController::class, 'showDiagnosisForm'])->name('ipd_diagnosis.page');
Route::get('/ipd/admit', [IpdController::class, 'admitList'])->name('ipd.admit_list');//แสดงข้อมูลController



Route::get('/ipd/dashboard', [IpdController::class, 'dashboardIpd'])->name('ipd.dashboard');


Route::get('/ipd/diagnosis-stats', [IpdController::class, 'ipdDiagnosisStats'])->name('ipd.diagnosis.stats');


Route::get('/ipd/patient-details', [IpdController::class, 'getPatientDetails'])->name('ipd.patient-details');




Route::get('/users/create', [UserController::class, 'create'])->name('users.create');// หน้าสร้าง User
Route::get('add/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store'); //เก็บ User ลง database




Route::get('/appointments/{id}/edit', [AppointmentController::class, 'loadAppointmentForEdit'])
    ->name('appointments.loadEditForm');

Route::put('/appointments/{id}', [AppointmentController::class, 'updateAppointmentDetails'])
    ->name('appointments.updateDetails');



Route::get('/opd/appointments', [OpdController::class, 'opdTodayAppointments'])->name('opd.todayAppointments');


Route::post('/er/store-with-diagnosis', [ERController::class, 'storeWithDiagnosis'])->name('er.storeWithDiagnosis');
Route::get('/soldier/by-name', [ERController::class, 'getByName'])->name('soldier.getByName'); //หาชื่อด้วยเลชบัตร




Route::post('/ipd/{treatmentId}/store-diagnosis', [IpdController::class, 'storeNewDiagnosis'])->name('ipd.storeDiagnosis');


Route::get('/admin/static-table', [StaticController::class, 'tableStaticAdminHospital'])->name('admin.static-table');



Route::get('/admin/hospital/static', [StaticController::class, 'showStaticHospital']);
Route::get('/admin/hospital/staticgraph', [StaticController::class, 'showStaticgraph']);
Route::get('/admin/hospital/treatment-statistics', [StaticController::class, 'showTreatmentStatistics']);


Route::get('/admin/patient/admit', [DashboardAdminController::class, 'getPatientAdmit'])->name('admin.patient.admit');


Route::get('/search-patient', [DashboardAdminController::class, 'searchPatient'])->name('search.patient');





Route::get('/search-appointments', [DashboardAdminController::class, 'searchAppointments'])->name('appointments.search');



Route::get('/medical-reports/soldier-info', [DashboardAdminController::class, 'getMedicalReportsWithSoldierInfo']);



Route::get('/disease-report/current-month', [DashboardAdminController::class, 'getCurrentMonthTopDiseases']);

Route::get('/daily-treatment/status', [DashboardAdminController::class, 'getTodayTreatmentStatus']);





use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminHospitalMiddleware;









Route::get('/', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }

    return match (Auth::user()->role) {
        'admin' => redirect('/admin/overview'),
        'adminhospital' => redirect('/dashboard-admin'),
        'opd' => redirect('/hospital/opd-dashboard'),
        'er' => redirect('/er/dashboard'),
        'ipd' => redirect('/ipd/dashboard'),
        'training_unit' => redirect('/training/dashboard'),
        default => redirect('/dashboard')
    };
})->name('home');
// เพิ่มที่ท้าย routes/web.php
Route::get('/simple-test', function () {
    if (!Auth::check()) {
        return 'Not logged in';
    }

    $user = Auth::user();
    if ($user->role !== 'admin') {
        return 'Not admin. Your role: ' . $user->role;
    }

    return 'You are admin! Username: ' . $user->username;
});
// routes/web.php หรือ routes/api.php
Route::middleware('auth')->group(function () {
    Route::get('/api/notifications/check', [NotificationController::class, 'checkNew']);
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});
Route::middleware('auth')->group(function () {

    // API สำหรับดึงสรุปผู้ป่วย
    Route::get('/api/notifications/patient-summary', function () {
        $user = Auth::user();

        // สรุปผู้ป่วยวันนี้
        $todaySummary = $user->today_patient_summary;

        // การแจ้งเตือนอื่น ๆ
        $otherNotifications = $user->other_notifications;

        // จำนวนรวม
        $totalUnreadToday = $user->today_unread_patients_count;
        $totalOtherUnread = $user->customUnreadNotifications()
            ->where('type', '!=', 'new_patient')
            ->count();

        return response()->json([
            'today_patient_summary' => $todaySummary,
            'other_notifications' => $otherNotifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'priority' => $notification->priority,
                    'created_at' => $notification->created_at->toISOString()
                ];
            }),
            'counts' => [
                'total_patients_today' => $totalUnreadToday,
                'total_other' => $totalOtherUnread,
                'grand_total' => $totalUnreadToday + $totalOtherUnread
            ]
        ]);
    });

    // API สำหรับทำเครื่องหมายอ่านทั้งหมด
    Route::post('/api/notifications/mark-all-read', function () {
        $user = Auth::user();
        $updated = $user->markAllNotificationsAsRead();

        return response()->json([
            'success' => true,
            'message' => "ทำเครื่องหมายอ่าน {$updated} รายการสำเร็จ",
            'updated_count' => $updated
        ]);
    });
});
// เพิ่ม route สำหรับแสดงหน้าเว็บ
Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');

// Auth Routes
// ✅ เป็น
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/api/check-auth', [AuthController::class, 'checkAuth']);

// Protected Routes (ต้อง login ก่อน)


Route::middleware(['auth', 'opd'])->group(function () {
    // OPD Dashboard Routes
    Route::get('/opd/dashboard', function () {
        return view('opd.dashboard_opd');
    })->name('opd.dashboard.view');

    Route::get('/hospital/opd-dashboard', [OpdController::class, 'OpdCountdashboard'])->name('opd.dashboard');

    // OPD Check-in
    Route::get('/opd/view-checkin', [TreatmentController::class, 'viewCheckin'])->name('opd.view_checkin');

    // OPD History & Statistics
    Route::get('/opd/history_opd', [OpdController::class, 'opdDiagnosisStats'])->name('opd.diagnosis.stats');
    Route::get('/opd/appointmenttoday', [OpdController::class, 'viewTodayAppointment'])->name('opd.appointmenttoday');
});
Route::middleware(['auth', 'admin'])->group(function () {
    // ✅ หน้า Admin Overview (ย้ายมาจาก route /)
    Route::get('/admin/overview', function () {
        $totalRotations = Rotation::count();
        $totalSoldiers = Soldier::count();
        $totalTrainingUnits = TrainingUnit::count();
        $soldiers = Soldier::with(['rotation', 'trainingUnit'])->get();

        return view('admin.dashboardadmin', compact('totalRotations', 'totalSoldiers', 'totalTrainingUnits', 'soldiers'));
    })->name('admin.overview');
});












Route::prefix('api')->group(function () {
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/training-units', [UserController::class, 'getTrainingUnits']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // เพิ่มสำหรับลบ
    Route::get('/users/{id}/edit', [UserController::class, 'edit']); // เพิ่ม
    Route::put('/users/{id}', [UserController::class, 'update']); // เพิ่มสำหรับแก้ไข
});


// ✅ ทหาร
    Route::get('/soldier/view/{id}', [SoldierController::class, 'view_soldier'])->name('soldier.view');
    Route::patch('/soldier/update/{id}', [SoldierController::class, 'update_soldier'])->name('soldier.update_soldier');
    Route::post('/soldier/{soldier_id}/assessment/{type}', [AssessmentController::class, 'submitAssessment'])->name('assessment.submit');
    Route::get('/soldier/view_assessment/{id}', [AssessmentController::class, 'viewAssessment'])->name('soldier.view_assessment');
    Route::get('/soldier/login', [SoldierController::class, 'showLoginForm'])->name('soldier.login');
    Route::post('/soldier/authenticate', [SoldierController::class, 'authenticateSoldier'])->name('soldier.authenticate');
    Route::get('/soldier/individual/{id}', [SoldierController::class, 'individual_soldier'])->name('soldier.individual');
    Route::post('/{id}/accept-consent', [SoldierController::class, 'acceptConsent'])->name('soldier.accept_consent');
    Route::get('/soldier/{id}/profile-inv', [SoldierController::class, 'showInvProfile'])    ->name('profile.inv.soldier');
    Route::get('/soldier/{id}/dashboard', [SoldierController::class, 'dashboard'])->name('soldier.dashboard');
    Route::get('/soldier/{soldierId}/assessment-history', [AssessmentController::class, 'assessmentHistory'])->name('assessment.history');
    Route::post('/logout', function (Illuminate\Http\Request $request) {
    Session::flush();
    return redirect()->route('soldier.login')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    })->name('soldier.logout');

    Route::get('/soldier/{id}/edit-personal-info', [SoldierController::class, 'editPersonalInfo'])->name('soldier.edit_personal_info');
    Route::post('/soldier/{id}/update-personal-info', [SoldierController::class, 'updatePersonalInfo'])->name('soldier.update_personal_info');
    Route::get('/soldier/{id}/dashboard', [SoldierController::class, 'dashboard'])->name('soldier.dashboard');

    Route::get('/login-soldier', [SoldierController::class, 'showLoginForm'])->name('soldier.login');
    Route::post('/login-soldier', [SoldierController::class, 'authenticate'])->name('soldier.authenticate');

    Route::get('/medical-diagnosis/details/{id}', [SoldierController::class, 'getDiagnosisDetails'])->name('diagnosis.details');
    Route::get('/soldier/{id}/my-appointments', [App\Http\Controllers\SoldierController::class, 'myAppointments'])->name('soldier.my_appointments');


// ✅ แบบประเมิน
Route::prefix('assessment')->name('assessment.')->group(function () {
    Route::controller(AssessmentController::class)->group(function () {
        Route::get('/view_assignment/{soldier_id}', 'viewAssignment')->name('view_assignment');
        Route::get('/{soldier_id}/assessment/{type}/{questionIndex?}', 'showAssessmentForm')->where('questionIndex', '[0-9]+')->name('show');
        Route::post('/{soldier_id}/assessment/{type}', 'submitAssessment')->name('submit');
         // ✅ เพิ่ม Route นี้เข้าไป
        Route::get('/{soldier_id}/{type}/skip', 'skipAssessment')->name('skip');
        Route::get('/{soldierId}/assessment-history', 'assessmentHistory')->name('history');
        Route::get('/view_assessment/{id}', 'viewAssessment')->name('view_assessment');

    });
});

// ✅ ส่งป่วยพิเศษ
Route::prefix('mental-health')->name('mental-health.')->group(function () {
    Route::get('/dashboard', [MentalHealthController::class, 'index'])->name('dashboard');
    Route::get('/completed-history', [MentalHealthController::class, 'showCompletedHistory'])->name('completed');
    Route::post('/create-appointment', [MentalHealthController::class, 'createAppointment'])->name('appointment.create');
    Route::get('/history/{soldier}', [MentalHealthController::class, 'showHistory'])->name('history');
    Route::post('/close-case/{tracking}', [MentalHealthController::class, 'closeCase'])->name('case.close');
    Route::post('/bulk-close-cases', [MentalHealthController::class, 'bulkCloseCases'])->name('case.bulk-close');
    Route::post('/update-risk-type/{tracking}', [MentalHealthController::class, 'updateRiskType'])->name('risk-type.update');
    Route::post('/update-appointments', [MentalHealthController::class, 'updateAppointments'])->name('appointment.update');
    Route::post('/completed-history/download-pdf', [MentalHealthController::class, 'downloadCompletedHistoryPDF'])->name('download.pdf');
    Route::get('/history/{soldier_id}/download', [MentalHealthController::class, 'downloadIndividualHistoryPDF'])->name('history.download');
    Route::post('/dashboard/download', [MentalHealthController::class, 'downloadDashboardPDF'])->name('dashboard.download');
    Route::get('/assessment-summary', [MentalHealthController::class, 'assessmentSummary'])->name('assessment.summary');


});
