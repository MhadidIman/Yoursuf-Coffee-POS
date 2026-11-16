<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#3A2212">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Yoursuf POS">
    <title>Admin Backoffice &amp; Keuangan — Yoursuf Coffee</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-yoursuf.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-yoursuf.jpg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Yoursuf Coffee Palette -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        coffee: {
                            DEFAULT: '#5C3A21',
                            light: '#7A5032',
                            dark: '#472C18',
                        },
                        espresso: {
                            DEFAULT: '#3A2212',
                            deep: '#26150A',
                        },
                        cream: {
                            DEFAULT: '#F5E6D3',
                            light: '#FAF3EB',
                            dark: '#E8D1B5',
                        },
                        gold: {
                            DEFAULT: '#D4AF37',
                            light: '#E3C359',
                            dark: '#B59022',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                    }
                }
            }
        };
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #FAF3EB;
        }
        ::-webkit-scrollbar-thumb {
            background: #C7A987;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #5C3A21;
        }

        /* Konfigurasi Cetak Rekap Laporan Keuangan (Print / Save PDF) */
        @media print {
            html, body {
                height: auto !important;
                overflow: visible !important;
                background: #ffffff !important;
            }
            aside, header, .no-print {
                display: none !important;
            }
            main {
                overflow: visible !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="h-[100dvh] w-screen overflow-hidden bg-cream-light text-espresso antialiased selection:bg-gold selection:text-espresso"
      x-data="yoursufAdminApp()">

    <!-- ================= LAYOUT UTAMA ADMIN: SIDEBAR KIRI + KONTEN KANAN ================= -->
    <div class="flex h-[100dvh] w-full overflow-hidden">

        <!-- 1. Sidebar Navigasi Kiri -->
        @include('admin.partials.sidebar')

        <!-- 2. Area Konten Kanan (Header Atas + Isi Menu Scrollable) -->
        <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden">

            <!-- Header Atas & Flash Notifikasi -->
            @include('admin.partials.header')

            <!-- Konten Utama per Menu (Scroll Independen) -->
            <main class="flex-1 overflow-y-auto overscroll-contain px-4 sm:px-6 lg:px-8 py-5 pb-16">
                <div class="max-w-6xl mx-auto">

                    <!-- Menu 1: Dashboard Keuangan & Laba Bersih -->
                    @include('admin.partials.tab-keuangan')

                    <!-- Menu 2: Pencatatan Pengeluaran (Expense Tracker) -->
                    @include('admin.partials.tab-pengeluaran')

                    <!-- Menu 3: Manajemen Menu & HPP -->
                    @include('admin.partials.tab-menu-hpp')

                    <!-- Menu 4: Pengingat Stok Bahan Utama -->
                    @include('admin.partials.tab-stok-bahan')

                    <!-- Menu 5: Riwayat & Pembatalan Transaksi (Void Order) -->
                    @include('admin.partials.tab-riwayat-void')

                </div>
            </main>
        </div>

    </div>

    <!-- Modal Tambah/Edit Menu, Edit Pengeluaran, Update Stok & Verifikasi Hapus/Void -->
    @include('admin.partials.modals')

    <!-- State & Logika Alpine.js Backoffice Admin -->
    @include('admin.partials.scripts')

</body>
</html>
