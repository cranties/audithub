<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank you! — {{ config('app.name') }}</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex items-center justify-center">
<div class="text-center max-w-md px-6">
    <div class="text-6xl mb-6">✅</div>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Thank you!</h1>
    <p class="text-gray-500">Your response has been recorded. A PDF report will be generated shortly.</p>
</div>
</body>
</html>
