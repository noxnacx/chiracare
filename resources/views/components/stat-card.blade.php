<link rel="stylesheet" href="/css/components/stat-card.css">

@props([
    'icon' => 'chart-line',
    'title' => 'ชื่อสถิติ',
    'value' => 0,
    'unit' => 'ราย'
])

<div class="stat-card stat-horizontal">
    <div class="stat-top d-flex align-items-center gap-2 mb-2">
        <div class="icon-wrapper">
            <i class="fas fa-{{ $icon }} icon-bordered-shadow"></i>
        </div>
        <span class="card-label">{{ $title }}</span>
    </div>
    <span class="card-value">
        {{ $value }} <small class="unit-label">{{ $unit }}</small>
    </span>
</div>



