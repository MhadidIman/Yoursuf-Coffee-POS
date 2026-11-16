<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#3A2212">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Yoursuf POS">
    <title>Login Pemilik — Yoursuf Coffee POS</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-yoursuf.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-yoursuf.jpg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
</head>
<body class="min-h-full bg-gradient-to-br from-espresso-deep via-espresso to-coffee flex items-center justify-center p-4 antialiased">

    <div class="w-full max-w-md bg-cream-light rounded-3xl border-2 border-gold/50 shadow-2xl overflow-hidden">
        <!-- Header Card dengan Logo Resmi Yoursuf Coffee -->
        <div class="bg-espresso px-6 py-7 text-center border-b border-gold/30">
            <div class="w-24 h-24 rounded-2xl bg-white p-2 flex items-center justify-center mx-auto shadow-xl border-2 border-gold mb-3.5">
                <img src="{{ asset('images/logo-yoursuf.jpg') }}"
                     alt="Logo Yoursuf Coffee"
                     class="w-full h-full object-contain rounded-xl">
            </div>
            <h1 class="text-xl font-extrabold text-cream tracking-tight">
                YOURSUF COFFEE <span class="text-gold">POS</span>
            </h1>
            <p class="text-xs text-cream/70 mt-1">
                Sistem Kasir Tablet &amp; Manajemen Keuangan
            </p>
        </div>

        <!-- Form Login -->
        <form action="{{ route('login.submit') }}" method="POST" class="p-6 space-y-4">
            @csrf

            @if(session('status'))
                <div class="p-3 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 rounded-xl bg-red-100 border border-red-300 text-red-800 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1.5">
                    Email Pemilik
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="Masukkan email pemilik..."
                       required
                       autofocus
                       autocomplete="username"
                       class="w-full min-h-[46px] px-4 py-2.5 rounded-xl bg-white border border-cream-dark focus:border-coffee focus:ring-2 focus:ring-coffee/20 text-sm text-espresso outline-none transition">
            </div>

            <div>
                <label for="password" class="block text-xs font-extrabold uppercase tracking-wider text-espresso mb-1.5">
                    Password
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Masukkan password..."
                       required
                       autocomplete="current-password"
                       class="w-full min-h-[46px] px-4 py-2.5 rounded-xl bg-white border border-cream-dark focus:border-coffee focus:ring-2 focus:ring-coffee/20 text-sm text-espresso outline-none transition">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="inline-flex items-center gap-2 cursor-pointer text-espresso/80 font-medium">
                    <input type="checkbox" name="remember" checked class="rounded border-cream-dark text-coffee focus:ring-coffee">
                    <span>Tetap masuk di perangkat ini</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full min-h-[48px] py-3 px-4 rounded-xl bg-gradient-to-r from-coffee to-espresso hover:from-espresso hover:to-coffee text-gold font-extrabold text-sm shadow-lg transition flex items-center justify-center gap-2">
                <span>Masuk ke Kasir &amp; Backoffice</span>
                <span>→</span>
            </button>

            <div class="pt-2 border-t border-cream-dark text-center">
                <p class="text-[11px] text-espresso/60">
                    &copy; {{ date('Y') }} <strong>Yoursuf Coffee</strong> — Home Coffee &amp; Campus Delivery
                </p>
            </div>
        </form>
    </div>

</body>
</html>
