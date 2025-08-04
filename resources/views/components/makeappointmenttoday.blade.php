{{-- Make Appointment Today Component --}}
<div class="modern-card">
    <div class="modern-header">
        <div class="header-content">
            <h6 class="header-title">

                รายการบันทึกนัดหมายวันนี้
                <span class="patient-count" id="totalCount_sent"> เคส</span>
            </h6>
            <a href="/admin/patient/admit?period=daily" class="view-all-btn">
                ดูทั้งหมด
            </a>
        </div>
    </div>



    <!-- Table -->
    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ชื่อ - นามสกุล</th>
                    <th>เลขประจำตัว</th>
                    <th>ผลัด</th>
                    <th>หน่วยฝึก</th>
                    <th>ระดับเสี่ยง</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- แถวข้อความเมื่อไม่มีข้อมูล -->
                <tr id="emptyRow">
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-text">ไม่มีรายการนัดหมายวันนี้
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/components/makeappointmenttoday.css') }}">
<link rel="stylesheet" href="{{ asset(path: 'css/components/moderntable.css') }}">

<script src="{{ asset('js/components/makeappointmenttoday.js') }}"></script>