@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')

<div class="activity-logs-page">

    {{-- ========================================
        PAGE HEADER
    ========================================= --}}
    <div class="activity-logs-header">

        <div>
            <h1>Log Aktivitas</h1>

            <p>
                Pantau riwayat aktivitas pengguna dan perubahan yang terjadi di dalam sistem.
            </p>
        </div>

    </div>


    {{-- ========================================
        SUMMARY
    ========================================= --}}
    <div class="activity-summary">

        {{-- TOTAL AKTIVITAS --}}
        <div class="activity-summary-card">

            <div class="activity-summary-icon total">
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 12a9 9 0 1 0 3-6.7"/>
                    <path d="M3 4v5h5"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>

            <div>
                <span class="activity-summary-label">
                    Total Aktivitas
                </span>

                <strong class="activity-summary-value" id="totalActivities">{{ $summary['total'] }}</strong>

                <span class="activity-summary-caption">
                    aktivitas tercatat
                </span>
            </div>

        </div>


        {{-- AKTIVITAS HARI INI --}}
        <div class="activity-summary-card">

            <div class="activity-summary-icon today">
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="16"
                        rx="2"
                    />
                    <path d="M16 3v4"/>
                    <path d="M8 3v4"/>
                    <path d="M3 10h18"/>
                    <path d="m9 16 2 2 4-4"/>
                </svg>
            </div>

            <div>
                <span class="activity-summary-label">
                    Aktivitas Hari Ini
                </span>

                <strong class="activity-summary-value">{{ $summary['today'] }}</strong>

                <span class="activity-summary-caption">
                    aktivitas hari ini
                </span>
            </div>

        </div>


        {{-- ADMIN --}}
        <div class="activity-summary-card">

            <div class="activity-summary-icon admin">
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
            </div>

            <div>
                <span class="activity-summary-label">
                    Aktivitas Admin
                </span>

                <strong class="activity-summary-value">{{ $summary['admin'] }}</strong>

                <span class="activity-summary-caption">
                    dilakukan oleh admin
                </span>
            </div>

        </div>


        {{-- PRODUKSI --}}
        <div class="activity-summary-card">

            <div class="activity-summary-icon production">
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 21V10l5 3V9l5 3V5l6 4v12Z"/>
                    <path d="M8 21v-4"/>
                    <path d="M12 21v-4"/>
                    <path d="M16 21v-4"/>
                </svg>
            </div>

            <div>
                <span class="activity-summary-label">
                    Aktivitas Produksi
                </span>

                <strong class="activity-summary-value">{{ $summary['production'] }}</strong>

                <span class="activity-summary-caption">
                    dilakukan oleh produksi
                </span>
            </div>

        </div>

    </div>


    {{-- ========================================
        MAIN CARD
    ========================================= --}}
    <div class="activity-logs-card">

        {{-- CARD HEADER --}}
        <div class="activity-logs-card-header">

            <div>
                <h2>Riwayat Aktivitas</h2>

                <p>
                    Daftar aktivitas pengguna yang tercatat di dalam sistem.
                </p>
            </div>

        </div>


        {{-- ========================================
            FILTERS
        ========================================= --}}
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="activity-logs-toolbar">

            {{-- SEARCH (tekan Enter untuk mencari) --}}
            <div class="activity-logs-search">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input type="text" id="activitySearch" name="search"
                    value="{{ request('search') }}" placeholder="Cari aktivitas...">
            </div>

            <div class="activity-logs-filters">

                <select class="activity-logs-filter" name="module" onchange="this.form.submit()">
                    <option value="all">Semua Modul</option>
                    @foreach (\App\Models\ActivityLog::MODULES as $value => $label)
                        <option value="{{ $value }}" @selected(request('module') === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select class="activity-logs-filter" name="user" onchange="this.form.submit()">
                    <option value="all">Semua User</option>
                    <option value="admin" @selected(request('user') === 'admin')>Admin</option>
                    <option value="production" @selected(request('user') === 'production')>Produksi</option>
                </select>

                <select class="activity-logs-filter" name="period" onchange="this.form.submit()">
                    <option value="all">Semua Periode</option>
                    <option value="today" @selected(request('period') === 'today')>Hari Ini</option>
                    <option value="7days" @selected(request('period') === '7days')>7 Hari Terakhir</option>
                    <option value="30days" @selected(request('period') === '30days')>30 Hari Terakhir</option>
                </select>

            </div>

        </form>


        {{-- ========================================
            TABLE
        ========================================= --}}
        <div class="activity-logs-table-wrapper">

            <table class="activity-logs-table">

                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>Modul</th>
                        <th>Detail</th>
                    </tr>
                </thead>


                <tbody id="activityTableBody">

                    @foreach ($logs as $log)
                        <tr>

                            <td>
                                <div class="activity-time">
                                    <strong>{{ $log->created_at->translatedFormat('d M Y') }}</strong>
                                    <span>{{ $log->created_at->format('H:i') }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="activity-user">

                                    <div class="activity-user-avatar {{ $log->user_class }}">
                                        {{ strtoupper(mb_substr($log->user?->name ?? 'S', 0, 1)) }}
                                    </div>

                                    <div>
                                        <strong>{{ $log->user?->name ?? 'Sistem' }}</strong>
                                        <span>{{ $log->user?->role?->name ? ucfirst($log->user->role->name) : 'Sistem' }}</span>
                                    </div>

                                </div>
                            </td>

                            <td>
                                <div class="activity-description">
                                    <strong>{{ $log->title }}</strong>
                                    <span>{{ $log->description ?: '—' }}</span>
                                </div>
                            </td>

                            <td>
                                <span class="activity-module {{ $log->entity_type }}">
                                    {{ $log->module_label }}
                                </span>
                            </td>

                            <td>
                                <div class="activity-detail">
                                    <strong>{{ $log->detail_title }}</strong>
                                    @if ($log->detail_subtitle)
                                        <span>{{ $log->detail_subtitle }}</span>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @endforeach

                    </tbody>

            </table>

        </div>


        {{-- ========================================
            EMPTY STATE
        ========================================= --}}
        @if ($logs->isEmpty())
            <div
                class="activity-logs-empty"
                id="activityEmpty"
            >
                <svg
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                >
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <strong>
                    Aktivitas tidak ditemukan
                </strong>

                <span>
                    Tidak ada aktivitas yang sesuai dengan pencarian atau filter.
                </span>
            </div>
        @endif

        @if ($logs->hasPages())
            <div class="activity-pagination">
                <span>
                    Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} aktivitas
                </span>

                <div>
                    @if ($logs->onFirstPage())
                        <span class="disabled">‹ Sebelumnya</span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}">‹ Sebelumnya</a>
                    @endif

                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}">Berikutnya ›</a>
                    @else
                        <span class="disabled">Berikutnya ›</span>
                    @endif
                </div>
            </div>
        @endif

    </div>

</div>

@endsection

