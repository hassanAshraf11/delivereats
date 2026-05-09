<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DeliverEats' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#0D0D0D] text-[#B0B0B0] antialiased min-h-screen">
    {{ $slot }}
    @livewireScripts
</body>
</html>
