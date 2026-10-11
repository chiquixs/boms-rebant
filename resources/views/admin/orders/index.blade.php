@extends('layouts.admin')

@section('title', 'Order Management - REBANT BOMS')

@section('content')


{{-- =========================
    PAGE HEADER
========================= --}}

<div class="page-header">

    <div>
        <span class="page-eyebrow">ORDER MANAGEMENT</span>

        <h1>Order Management</h1>

        <p>
            Kelola dan pantau seluruh pesanan dari berbagai platform.
        </p>
    </div>


    {{-- ADD ORDER BUTTON --}}
    <button class="add-order-button">

        <svg width="17" height="17"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">

            <path d="M12 5v14"/>
            <path d="M5 12h14"/>

        </svg>

        <span>Add Order</span>

    </button>

</div>


{{-- =========================
    ORDER STATUS SUMMARY
========================= --}}

<div class="order-stats">

    {{-- NEW --}}
    <div class="stat-card">

        <div class="stat-icon new">

            <svg width="21" height="21"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">

                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
                <circle cx="12" cy="12" r="9"/>

            </svg>

        </div>

        <div class="stat-content">
            <span>New</span>
            <strong>12</strong>
            <small>Pesanan baru masuk</small>
        </div>

    </div>


    {{-- WAITING PRODUCTION --}}
    <div class="stat-card">

        <div class="stat-icon waiting">

            <svg width="21" height="21"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">

                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5l3 2"/>

            </svg>

        </div>

        <div class="stat-content">
            <span>Waiting Production</span>
            <strong>8</strong>
            <small>Menunggu proses produksi</small>
        </div>

    </div>


    {{-- READY TO PACK --}}
    <div class="stat-card">

        <div class="stat-icon ready">

            <svg width="21" height="21"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">

                <path d="M21 8l-9 5-9-5"/>
                <path d="M3 8l9-5 9 5v8l-9 5-9-5V8Z"/>
                <path d="M12 13v8"/>

            </svg>

        </div>

        <div class="stat-content">
            <span>Ready to Pack</span>
            <strong>6</strong>
            <small>Siap untuk dikemas</small>
        </div>

    </div>


    {{-- COMPLETED --}}
    <div class="stat-card">

        <div class="stat-icon completed">

            <svg width="21" height="21"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8">

                <circle cx="12" cy="12" r="9"/>
                <path d="m8 12 2.5 2.5L16 9"/>

            </svg>

        </div>

        <div class="stat-content">
            <span>Completed</span>
            <strong>102</strong>
            <small>Pesanan selesai</small>
        </div>

    </div>

</div>



{{-- =========================
    ORDER TABLE
========================= --}}

<div class="orders-card">


    {{-- HEADER --}}
    <div class="orders-card-header">

        <div>
            <h2>Daftar Order</h2>

            <p>
                Pantau seluruh pesanan TikTok Shop dan Shopee.
            </p>
        </div>


        {{-- FILTER AREA --}}
        <div class="order-tools">


            {{-- SEARCH --}}
            <div class="search-box">

                <svg width="18" height="18"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>

                </svg>

                <input
                    type="text"
                    placeholder="Cari order ID..."
                >

            </div>


            {{-- PLATFORM FILTER --}}
            <select class="platform-filter">

                <option value="">
                    Semua Platform
                </option>

                <option value="tiktok">
                    TikTok Shop
                </option>

                <option value="shopee">
                    Shopee
                </option>

            </select>


            {{-- STATUS FILTER --}}
            <select class="status-filter">

                <option value="">
                    Semua Status
                </option>

                <option value="new">
                    New
                </option>

                <option value="waiting-production">
                    Waiting Production
                </option>

                <option value="ready-to-pack">
                    Ready to Pack
                </option>

                <option value="completed">
                    Completed
                </option>

            </select>


        </div>

    </div>



    {{-- =========================
        TABLE
    ========================= --}}

    <div class="table-wrapper">

        <table class="orders-table">

            <thead>

                <tr>

                    <th>Order ID</th>

                    <th>Tanggal</th>

                    <th>Platform</th>

                    <th>Total Nominal</th>

                    <th>Status</th>

                    <th>Aksi</th>

                </tr>

            </thead>


            <tbody>


                {{-- ORDER 1 --}}
                <tr>

                    <td>
                        <span class="order-id">
                            #RB-1001
                        </span>
                    </td>

                    <td>
                        11 Oct 2026
                    </td>


                    <td>

                        <span class="platform-badge tiktok">
                            TikTok Shop
                        </span>

                    </td>


                    <td>
                        <strong>
                            Rp350.000
                        </strong>
                    </td>


                    <td>

                        <span class="order-status new">

                            <span></span>

                            New

                        </span>

                    </td>


                    <td>

                        <button
                            class="action-button"
                            title="Lihat detail order"
                        >

                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path
                                    d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                />

                            </svg>

                        </button>

                    </td>

                </tr>



                {{-- ORDER 2 --}}
                <tr>

                    <td>
                        <span class="order-id">
                            #RB-1002
                        </span>
                    </td>

                    <td>
                        11 Oct 2026
                    </td>


                    <td>

                        <span class="platform-badge shopee">
                            Shopee
                        </span>

                    </td>


                    <td>
                        <strong>
                            Rp275.000
                        </strong>
                    </td>


                    <td>

                        <span class="order-status waiting">

                            <span></span>

                            Waiting Production

                        </span>

                    </td>


                    <td>

                        <button
                            class="action-button"
                            title="Lihat detail order"
                        >

                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path
                                    d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                />

                            </svg>

                        </button>

                    </td>

                </tr>



                {{-- ORDER 3 --}}
                <tr>

                    <td>
                        <span class="order-id">
                            #RB-1003
                        </span>
                    </td>

                    <td>
                        10 Oct 2026
                    </td>


                    <td>

                        <span class="platform-badge tiktok">
                            TikTok Shop
                        </span>

                    </td>


                    <td>
                        <strong>
                            Rp420.000
                        </strong>
                    </td>


                    <td>

                        <span class="order-status ready">

                            <span></span>

                            Ready to Pack

                        </span>

                    </td>


                    <td>

                        <button
                            class="action-button"
                            title="Lihat detail order"
                        >

                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path
                                    d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                />

                            </svg>

                        </button>

                    </td>

                </tr>



                {{-- ORDER 4 --}}
                <tr>

                    <td>
                        <span class="order-id">
                            #RB-1004
                        </span>
                    </td>

                    <td>
                        09 Oct 2026
                    </td>


                    <td>

                        <span class="platform-badge shopee">
                            Shopee
                        </span>

                    </td>


                    <td>
                        <strong>
                            Rp180.000
                        </strong>
                    </td>


                    <td>

                        <span class="order-status completed">

                            <span></span>

                            Completed

                        </span>

                    </td>


                    <td>

                        <button
                            class="action-button"
                            title="Lihat detail order"
                        >

                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path
                                    d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                />

                            </svg>

                        </button>

                    </td>

                </tr>



                {{-- ORDER 5 --}}
                <tr>

                    <td>
                        <span class="order-id">
                            #RB-1005
                        </span>
                    </td>

                    <td>
                        09 Oct 2026
                    </td>


                    <td>

                        <span class="platform-badge tiktok">
                            TikTok Shop
                        </span>

                    </td>


                    <td>
                        <strong>
                            Rp515.000
                        </strong>
                    </td>


                    <td>

                        <span class="order-status waiting">

                            <span></span>

                            Waiting Production

                        </span>

                    </td>


                    <td>

                        <button
                            class="action-button"
                            title="Lihat detail order"
                        >

                            <svg width="18" height="18"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8">

                                <path
                                    d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="2.5"
                                />

                            </svg>

                        </button>

                    </td>

                </tr>


            </tbody>

        </table>

    </div>



    {{-- =========================
        TABLE FOOTER
    ========================= --}}

    <div class="table-footer">

        <span>
            Menampilkan 1–5 dari 128 order
        </span>


        <div class="pagination">

            <button disabled>
                ‹
            </button>

            <button class="active">
                1
            </button>

            <button>
                2
            </button>

            <button>
                3
            </button>

            <button>
                ›
            </button>

        </div>

    </div>


</div>

@endsection