<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
            colors: {
                brand: { 50:'#eef9f1',100:'#d6f0de',200:'#aee0be',300:'#7ccb97',400:'#48b06f',500:'#239456',600:'#137a45',700:'#0d6538',800:'#09572b',900:'#074b25',950:'#06401f' },
                olive: { 50:'#f7f8ee',100:'#eceeda',200:'#d9ddb4',300:'#bfc687',400:'#a3ad5c',500:'#889340',600:'#6b7532',700:'#535b2a',800:'#444a27',900:'#3a3f25',950:'#1e2111' },
                sage:  { 50:'#f3f7f5',100:'#e1ebe6',200:'#c5d8cf',300:'#9dbdae',400:'#77a08c',500:'#5f8f76',600:'#4a7360',700:'#3d5d4e',800:'#334c41',900:'#2b3f36',950:'#16231d' }
            }
        } } }</script>
    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}

        /* ===== Panel kanan (animasi) ===== */
        .hero{background:
            radial-gradient(800px 480px at 85% 8%, rgba(74,222,128,.20), transparent 60%),
            radial-gradient(700px 500px at 5% 95%, rgba(163,230,53,.10), transparent 60%),
            linear-gradient(180deg,#09572b 0%,#074b25 55%,#06401f 100%);}
        .hero::before{content:"";position:absolute;inset:0;
            background-image:linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px);
            background-size:44px 44px;mask-image:radial-gradient(circle at 50% 45%,#000 0%,transparent 70%);}

        /* dokumen kecil yang naik pelan di latar belakang */
        .ghost{position:absolute;bottom:-120px;width:54px;height:70px;border-radius:8px;background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.18);animation:rise linear infinite;opacity:0}
        .ghost::before,.ghost::after{content:"";position:absolute;left:9px;right:9px;height:4px;border-radius:3px;background:rgba(255,255,255,.25)}
        .ghost::before{top:14px}.ghost::after{top:26px;right:20px}
        @keyframes rise{0%{transform:translateY(0) rotate(0);opacity:0}12%{opacity:.9}100%{transform:translateY(-115vh) rotate(38deg);opacity:0}}

        /* ===== Scene dokumen utama ===== */
        .scene{position:relative;width:310px;height:400px}
        .page{position:absolute;inset:0;border-radius:22px;background:#fff;box-shadow:0 40px 80px -30px rgba(0,0,0,.7)}
        .pg-back{translate:-38px 20px;rotate:-9deg;background:#7ccb97;opacity:.55;animation:fly 1s .1s cubic-bezier(.2,.8,.2,1) both,bob 7s 1.2s ease-in-out infinite}
        .pg-mid{translate:30px 10px;rotate:6deg;background:#aee0be;opacity:.8;animation:fly 1s .25s cubic-bezier(.2,.8,.2,1) both,bob 6s 1.3s ease-in-out infinite reverse}
        .pg-front{padding:26px;overflow:hidden;animation:fly 1s .4s cubic-bezier(.2,.8,.2,1) both,bob 6.5s 1.4s ease-in-out infinite}
        @keyframes fly{from{opacity:0;transform:translateY(90px) scale(.88)}}
        @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}

        .pg-head{display:flex;align-items:center;gap:10px;margin-bottom:18px}
        .pg-logo{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#239456,#22c55e);display:grid;place-items:center;color:#fff}
        .ln{height:9px;border-radius:6px;background:#e2e8f0;margin-top:13px;transform-origin:left;animation:write 7s ease-in-out infinite;animation-delay:calc(var(--i)*.14s)}
        .ln.strong{background:#239456;height:12px}
        .ln.soft{background:#aee0be}
        @keyframes write{0%{transform:scaleX(0)}28%,92%{transform:scaleX(1)}100%{transform:scaleX(0)}}

        .beam{position:absolute;left:0;right:0;height:70px;top:-70px;z-index:3;pointer-events:none;
            background:linear-gradient(to bottom,transparent,rgba(35,148,86,.22));border-bottom:2px solid rgba(35,148,86,.8);
            animation:scan 7s ease-in-out infinite}
        @keyframes scan{0%{top:-70px;opacity:0}4%{opacity:1}36%{top:100%;opacity:1}40%,100%{top:100%;opacity:0}}

        .sig{position:absolute;left:26px;bottom:38px;width:130px}
        .sig path{fill:none;stroke:#137a45;stroke-width:3;stroke-linecap:round;stroke-dasharray:280;stroke-dashoffset:280;animation:sign 7s ease-in-out infinite}
        .sig-line{position:absolute;left:26px;bottom:30px;width:140px;height:2px;background:#cbd5e1}
        @keyframes sign{0%,42%{stroke-dashoffset:280}56%,92%{stroke-dashoffset:0}100%{stroke-dashoffset:280}}

        .stamp{position:absolute;right:16px;bottom:26px;width:118px;height:118px;border:4px solid #10b981;border-radius:50%;color:#059669;
            display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;text-align:center;
            font-weight:800;font-size:10px;letter-spacing:.1em;background:rgba(16,185,129,.07);rotate:-14deg;opacity:0;animation:stamp 7s ease-in infinite}
        .stamp::before{content:"";position:absolute;inset:6px;border:1.5px dashed #10b981;border-radius:50%}
        @keyframes stamp{0%,60%{opacity:0;transform:scale(2.6)}66%{opacity:1;transform:scale(.9)}69%{transform:scale(1.05)}72%,92%{opacity:.95;transform:scale(1)}100%{opacity:0;transform:scale(1)}}

        .chip{position:absolute;display:flex;align-items:center;gap:8px;padding:9px 14px;border-radius:14px;color:#fff;font-size:12px;font-weight:600;
            background:rgba(255,255,255,.1);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.22);
            box-shadow:0 12px 30px -12px rgba(0,0,0,.5);animation:float 6s ease-in-out infinite;white-space:nowrap}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}

        /* ===== Panel kiri (form) ===== */
        .form-in{animation:formin .8s .2s cubic-bezier(.2,.8,.2,1) both}
        @keyframes formin{from{opacity:0;transform:translateY(18px)}}

        @media (prefers-reduced-motion:reduce){
            *{animation:none!important}
            .stamp{opacity:.95}.ln{transform:none}.sig path{stroke-dashoffset:0}
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
<div class="min-h-screen lg:grid lg:grid-cols-[1fr_1.1fr]">

    {{-- ============ PANEL KIRI: FORM ============ --}}
    <main class="relative flex min-h-screen items-center justify-center px-6 py-10 sm:px-12"
          style="background:radial-gradient(600px 300px at 100% 0%,rgba(35,148,86,.10),transparent 70%),radial-gradient(500px 300px at 0% 100%,rgba(16,185,129,.08),transparent 70%)">
        <div class="form-in w-full max-w-md" x-data="{ show:false, loading:false }">

            {{-- header mobile --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-500 text-white"><i data-lucide="scale" class="h-6 w-6"></i></div>
                <div class="text-xl font-extrabold">SILEGAL</div>
                <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-600 ring-1 ring-brand-200">v2.0</span>
            </div>

            <div class="mb-8">
                <span class="hidden rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-600 ring-1 ring-brand-200 lg:inline-block">SILEGAL Versi 2</span>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Selamat datang 👋</h1>
                <p class="mt-2 text-sm text-slate-500">Masuk untuk mengelola dokumen legal perusahaan.</p>
            </div>

            <form method="POST" action="{{ route('login.proses') }}" @submit="loading=true" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="w-full rounded-xl border bg-white py-3 pl-10 pr-4 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100 {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-200' }}"
                               placeholder="nama@perusahaan.co.id">
                    </div>
                    @error('email')<p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-rose-600"><i data-lucide="circle-alert" class="h-3.5 w-3.5"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-11 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                               placeholder="••••••••">
                        <button type="button" @click="show=!show" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1 text-xs font-semibold text-slate-400 hover:text-brand-600" x-text="show ? 'Sembunyi' : 'Lihat'"></button>
                    </div>
                </div>

                <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit" :disabled="loading"
                        class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-brand-700 to-brand-500 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:shadow-xl hover:shadow-brand-500/40 active:scale-[.99] disabled:opacity-70">
                    <span x-show="!loading" class="flex items-center gap-2">Masuk ke SILEGAL <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1"></i></span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>
                        Memverifikasi…
                    </span>
                </button>
            </form>

            <p class="mt-10 text-center text-xs text-slate-400">SILEGAL v2.0 · © {{ date('Y') }} · Hak akses diatur oleh administrator</p>
        </div>
    </main>

    {{-- ============ PANEL KANAN: ANIMASI ============ --}}
    <section class="hero relative hidden lg:flex flex-col justify-between overflow-hidden p-12 text-white">
        @foreach ([[8,16,0],[22,21,5],[38,14,2],[55,19,8],[70,23,3],[85,17,10],[93,20,6]] as [$left,$dur,$delay])
            <span class="ghost" style="left:{{ $left }}%;animation-duration:{{ $dur }}s;animation-delay:-{{ $delay }}s"></span>
        @endforeach

        <header class="relative z-10 flex items-center gap-3">
            <div class="grid h-11 w-11 place-items-center rounded-xl bg-white/15 ring-1 ring-white/30 backdrop-blur">
                <i data-lucide="scale" class="h-6 w-6"></i>
            </div>
            <div class="leading-tight">
                <div class="text-xl font-extrabold tracking-wide">SILEGAL</div>
                <div class="text-[11px] text-brand-200">Sistem Informasi Legal</div>
            </div>
            <span class="ml-3 inline-flex items-center gap-2 rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-bold text-emerald-300 ring-1 ring-emerald-300/40">
                <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-300 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-300"></span></span>
                Versi 2.0
            </span>
        </header>

        <div class="relative z-10 flex flex-1 items-center justify-center">
            <div class="scene">
                <div class="page pg-back"></div>
                <div class="page pg-mid"></div>
                <div class="page pg-front">
                    <div class="beam"></div>
                    <div class="pg-head">
                        <div class="pg-logo"><i data-lucide="file-text" class="h-5 w-5"></i></div>
                        <div>
                            <div class="ln strong" style="--i:0;width:120px;margin:0"></div>
                            <div class="ln soft" style="--i:1;width:70px;margin-top:7px;height:7px"></div>
                        </div>
                    </div>
                    <div class="ln" style="--i:2;width:100%"></div>
                    <div class="ln" style="--i:3;width:92%"></div>
                    <div class="ln" style="--i:4;width:97%"></div>
                    <div class="ln soft" style="--i:5;width:60%"></div>
                    <div class="ln" style="--i:6;width:100%;margin-top:24px"></div>
                    <div class="ln" style="--i:7;width:88%"></div>
                    <div class="ln" style="--i:8;width:94%"></div>
                    <div class="ln soft" style="--i:9;width:45%"></div>
                    <div class="sig-line"></div>
                    <svg class="sig" viewBox="0 0 130 44"><path d="M4 32 C 14 4, 26 4, 30 26 S 46 38, 56 16 S 78 10, 84 30 S 106 34, 124 12"/></svg>
                    <div class="stamp">
                        <i data-lucide="badge-check" class="h-6 w-6"></i>
                        <span>TERVERIFIKASI</span>
                        <span class="text-[8px] opacity-70">SILEGAL v2</span>
                    </div>
                </div>

                <div class="chip" style="left:-120px;top:46px;animation-delay:0s"><i data-lucide="file-signature" class="h-4 w-4 text-teal-300"></i>Kontrak</div>
                <div class="chip" style="right:-110px;top:92px;animation-delay:-2s"><i data-lucide="handshake" class="h-4 w-4 text-amber-300"></i>Perjanjian</div>
                <div class="chip" style="left:-90px;bottom:96px;animation-delay:-4s"><i data-lucide="stamp" class="h-4 w-4 text-lime-300"></i>Akta Notaris</div>
                <div class="chip" style="right:-96px;bottom:20px;animation-delay:-1s"><i data-lucide="gavel" class="h-4 w-4 text-olive-300"></i>Surat Keputusan</div>
            </div>
        </div>

        <footer class="relative z-10 max-w-md">
            <h2 class="text-3xl font-extrabold leading-tight">Semua dokumen legal,<br>rapi dalam satu tempat.</h2>
            <p class="mt-3 text-sm leading-relaxed text-brand-200">Kelola kontrak, perjanjian, SOP, risalah rapat, akta, dan surat keputusan. Lengkap dengan pengingat masa berlaku dan jejak aktivitas.</p>
        </footer>
    </section>
</div>
<script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</body>
</html>
