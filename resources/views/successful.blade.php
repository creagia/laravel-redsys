<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>{{ config('app.name') }}</title>
</head>
<body class="bg-gray-200 min-h-screen grid place-items-center">

<div class="w-full max-w-xl mx-auto px-4">
    <div class="mt-4 p-5 bg-white rounded-xl shadow-md overflow-hidden text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mx-auto text-green-700" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
        </svg>
        <h2 class="font-bold text-2xl mt-2">Successful payment</h2>
        <a href="/" class="mt-5 text-sm border border-gray-500 text-gray-500 py-1 px-3 rounded-full inline-block transition hover:text-gray-600 hover:border-gray-200 hover:bg-gray-200">Return to home</a>
    </div>
</div>

</body>
</html>
