<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AuditHub — Survey &amp; Inspection Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="font-sans antialiased bg-white text-slate-800">

    <header class="border-b border-slate-100 bg-white sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">A</div>
                <span class="text-lg font-bold text-slate-800 tracking-tight">AuditHub</span>
            </div>
            <a href="{{ route("login") }}" class="px-4 py-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                Sign In &rarr;
            </a>
        </div>
    </header>

    <section class="max-w-6xl mx-auto px-6 pt-24 pb-20 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold mb-8 border border-indigo-100">
            SaaS platform for inspections &amp; surveys
        </div>
        <h1 class="text-5xl sm:text-6xl font-extrabold text-slate-900 leading-tight tracking-tight mb-6">
            Inspection management<br>
            <span class="text-indigo-600">made simple</span>
        </h1>
        <p class="text-xl text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
            Build dynamic checklists, collect responses securely, and generate PDF reports automatically.
        </p>
        <a href="{{ route("login") }}"
            class="inline-block px-8 py-3.5 text-base font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md hover:shadow-lg transition">
            Go to Dashboard &rarr;
        </a>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center mb-4 text-indigo-600 font-bold text-lg">&#9776;</div>
                <h3 class="text-base font-semibold text-slate-800 mb-2">Drag &amp; Drop Form Builder</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Build forms with text inputs, dropdowns, file uploads, and ratings through an intuitive no-code interface.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center mb-4 text-green-600 font-bold text-lg">&#128274;</div>
                <h3 class="text-base font-semibold text-slate-800 mb-2">Security &amp; Privacy</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Cryptographic public tokens, private storage uploads, and rate limiting protect every submission.
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center mb-4 text-amber-600 font-bold text-lg">&#128196;</div>
                <h3 class="text-base font-semibold text-slate-800 mb-2">Async PDF Reports</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Every submission automatically generates a PDF report in the background. Zero wait time for your users.
                </p>
            </div>

        </div>
    </section>

    <footer class="border-t border-slate-100 py-8">
        <p class="text-center text-xs text-slate-400">&copy; {{ date("Y") }} AuditHub. All rights reserved.</p>
    </footer>

</body>
</html>