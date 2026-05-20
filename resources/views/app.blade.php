<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeMuFe</title>
    @vite(['resources/ts/main.ts', 'resources/css/main.css'])
</head>

<body>
    <div id="app" class="h-screen">
        <App />
    </div>
</body>

</html>
