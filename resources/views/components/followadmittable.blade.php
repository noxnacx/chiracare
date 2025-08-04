{{--
Follow Admit Table Component
File: resources/views/components/followadmittable.blade.php
Description: แสดงตารางรายชื่อผู้ป่วย Admit รายวัน

Props:
- $patients: Collection ของข้อมูลผู้ป่วย (required)
- $title: หัวข้อตาราง (optional, default: "รายชื่อผู้ป่วย Admit รายวัน")
- $viewAllUrl: URL สำหรับดูทั้งหมด (optional, default: "/admin/patient/admit?period=daily")
- $maxRows: จำนวนแถวสูงสุดที่แสดง (optional, default: 3)
- $showViewAll: แสดงปุ่มดูทั้งหมดหรือไม่ (optional, default: true)
--}}

@props([
    'patients' => collect(),
    'title' => 'รายชื่อผู้ป่วย Admit รายวัน',
    'viewAllUrl' => '/admin/patient/admit?period=daily',
    'maxRows' => 3,
    'showViewAll' => true
])

<div class="modern-card">
    <!-- Header -->
    <div class="modern-header">
        <div class="header-content">
            <h6 class="header-title">
                {{ $title }}
                <span class="patient-count">{{ $patients->count() }} เคส</span>
            </h6>
            @if($showViewAll)
                <a href="{{ $viewAllUrl }}" class="view-all-btn">
                    ดูทั้งหมด
                </a>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th style="width: 20%;">ชื่อ - นามสกุล</th>
                    <th style="width: 15%;">เลขประจำตัว</th>
                    <th style="width: 15%;">ผลัด</th>
                    <th style="width: 15%;">หน่วยฝึก</th>
                    <th style="width: 20%;">หน่วยต้นสังกัด</th>
                    <th style="width: 10%;">โรค (ICD10)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients->take($maxRows) as $patient)
                    <tr>
                        <td class="patient-name-cell">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </td>
                        <td class="patient-id-cell">{{ $patient->soldier_id_card }}</td>
                        <td>
                            <span class="unit-badge rotation-badge">{{ $patient->rotation_name }}</span>
                        </td>
                        <td>
                            <span class="unit-badge training-badge">{{ $patient->training_unit_name }}</span>
                        </td>
                        <td>{{ $patient->affiliated_unit }}</td>
                        <td>
                            <div class="disease-container">
                                @php
                                    $codes = explode(',', $patient->icd10_codes);
                                    $names = explode(',', $patient->disease_names);
                                @endphp
                                @foreach ($codes as $index => $code)
                                    <div class="disease-code">{{ $code }} :
                                        {{ $names[$index] ?? '-' }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-user-injured empty-icon"></i>
                                <div class="empty-text">ไม่มีข้อมูลผู้ป่วย Admit รายวัน</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="{{ asset(path: 'css/components/followadmit.css') }}">
<link rel="stylesheet" href="{{ asset(path: 'css/components/moderntable.css') }}">
