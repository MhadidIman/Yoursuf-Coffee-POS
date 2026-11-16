<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#3A2212">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Yoursuf POS">
    <title>Kasir POS — Yoursuf Coffee</title>
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

        /* Optimasi Sentuhan Tablet (Touch-Friendly) */
        button, a, input, select, textarea, [role="button"] {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }

        /* Custom Scrollbar bertema Yoursuf Coffee */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
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

        /* Konfigurasi Khusus Cetak Struk Thermal 58mm / 80mm */
        @media print {
            @page {
                margin: 0;
            }
            html, body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                overflow: visible !important;
            }
            .no-print {
                display: none !important;
            }
            .print-only-container {
                display: block !important;
                position: static !important;
                width: 100% !important;
                background: #ffffff !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .thermal-paper-58mm {
                width: 58mm !important;
                max-width: 58mm !important;
                padding: 3mm 2mm !important;
                margin: 0 !important;
                font-family: 'JetBrains Mono', monospace !important;
                font-size: 11px !important;
                line-height: 1.35 !important;
                color: #000000 !important;
            }
            .thermal-paper-80mm {
                width: 80mm !important;
                max-width: 80mm !important;
                padding: 4mm 3mm !important;
                margin: 0 !important;
                font-family: 'JetBrains Mono', monospace !important;
                font-size: 12px !important;
                line-height: 1.4 !important;
                color: #000000 !important;
            }
        }
    </style>
</head>
<body class="h-[100dvh] w-screen overflow-hidden bg-cream-light text-espresso antialiased selection:bg-gold selection:text-espresso select-none"
      x-data="yoursufPosApp()"
      x-init="initPos()">

    <!-- ================= MAIN APP WRAPPER (LOCKED SPLIT-SCREEN FOR TABLET & DESKTOP) ================= -->
    <div class="no-print flex flex-col h-[100dvh] w-full overflow-hidden">

        <!-- 1. Header Navigasi Atas & Toast Notifikasi -->
        @include('pos.partials.header')

        <!-- 2. Menu 1: Katalog POS (Kiri) & Keranjang Checkout Sticky (Kanan) -->
        <main x-show="activeMainTab === 'catalog'"
              class="flex-1 flex flex-col md:flex-row overflow-hidden max-w-[1600px] w-full mx-auto h-full">
            @include('pos.partials.katalog')
            @include('pos.partials.keranjang')
        </main>

        <!-- 3. Menu 2: Manajemen Antrean Pesanan Aktif (3 Tahap) -->
        @include('pos.partials.antrean')

        <!-- 4. Modal Output Nota Ganda (Struk Thermal & WA) & Konfirmasi Keranjang -->
        @include('pos.partials.modals-struk')

    </div>

    <!-- 5. Layout Khusus Cetak Thermal (@media print) -->
    @include('pos.partials.thermal-print')

    <!-- 6. State & Logika Alpine.js Kasir POS -->
    @include('pos.partials.scripts')

</body>
</html>
