<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <meta name="theme-color" content="#facc15" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#facc15">
</head>
<body class="bg-gray-200 min-h-screen w-full">

<div class="bg-yellow-400 py-4">
    <div class="w-full flex justify-between">
        <div class="container mx-auto px-4">
            <h1 class="font-bold text-3xl">Redsys Local Gateway</h1>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 mt-5">
    <div class="mt-4 p-5 bg-white rounded-xl shadow-md overflow-hidden">
        <div class="grid grid-cols-3 gap-10">
            <div class="col-span-2">
                <h2 class="font-bold text-xl">Received data</h2>
                <div class="mt-4 flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Field
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Value
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($params as $key => $value)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $key }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $value }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="font-bold text-xl">Response</h2>
                <form class="p-4 border rounded-xl mt-4 overflow-hidden" method="post" action="{{ action([\Creagia\LaravelRedsys\Controllers\RedsysLocalGatewayController::class, 'post']) }}">
                    <select name="responseCode" class="
                    max-w-full
                    block
                    w-full
                    mt-1
                    rounded-md
                    border-gray-300
                    shadow-sm
                    focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                ">
                        <option value="0000" selected>Transacción autorizada para pagos y preautorizaciones</option>
                        <option value="0900">Transacción autorizada para devoluciones y confirmaciones</option>
                        <option value="0400">Transacción autorizada para anulaciones</option>
                    </select>
                    <input type="hidden" name="Ds_Signature" value="{{ $originalPost['Ds_Signature'] }}">
                    <input type="hidden" name="Ds_MerchantParameters" value="{{ $originalPost['Ds_MerchantParameters'] }}">
                    <input type="hidden" name="Ds_SignatureVersion" value="{{ $originalPost['Ds_SignatureVersion'] }}">
                    <button class="mt-2 w-full shadow hover:shadow-md text-white bg-green-600 transition hover:bg-green-700 focus:ring-4 focus:ring-green-100 font-medium rounded-lg px-5 py-2.5 text-center"
                    >Authorize payment</button>
                </form>

                <form class="p-4 border rounded-xl mt-4 overflow-hidden" method="post" action="{{ action([\Creagia\LaravelRedsys\Controllers\RedsysLocalGatewayController::class, 'post']) }}">
                    <select name="responseCode" class="
                    max-w-full
                    block
                    w-full
                    mt-1
                    rounded-md
                    border-gray-300
                    shadow-sm
                    focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                  ">
                        @foreach(\Creagia\Redsys\ResponseCodes\ResponseCode::$messages as $code => $message)
                            <option value="{{ $code }}">{{ $message }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="Ds_Signature" value="{{ $originalPost['Ds_Signature'] }}">
                    <input type="hidden" name="Ds_MerchantParameters" value="{{ $originalPost['Ds_MerchantParameters'] }}">
                    <input type="hidden" name="Ds_SignatureVersion" value="{{ $originalPost['Ds_SignatureVersion'] }}">
                    <button class="mt-2 w-full shadow hover:shadow-md text-white bg-red-600 transition hover:bg-red-700 focus:ring-4 focus:ring-red-100 font-medium rounded-lg px-5 py-2.5 text-center"
                    >Deny payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="w-full mt-5">
    <svg class="mx-auto" style="width: 200px" viewBox="0 0 1320 180" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M170.907 12.181V115.21h49.41L217.206 139h-76.311V12.181h30.012ZM309.608 110.818c0 3.66.488 6.344 1.464 8.052 1.098 1.708 2.806 2.989 5.124 3.843l-6.039 18.849c-5.978-.488-10.858-1.769-14.64-3.843-3.782-2.196-6.71-5.612-8.784-10.248-6.344 9.76-16.104 14.64-29.28 14.64-9.638 0-17.324-2.806-23.058-8.418-5.734-5.612-8.601-12.932-8.601-21.96 0-10.614 3.904-18.727 11.712-24.339 7.808-5.612 19.093-8.418 33.855-8.418h9.882v-4.209c0-5.734-1.22-9.638-3.66-11.712-2.44-2.196-6.71-3.294-12.81-3.294-3.172 0-7.015.488-11.529 1.464-4.514.854-9.15 2.074-13.908 3.66l-6.588-19.032a112.002 112.002 0 0 1 18.666-5.307c6.466-1.22 12.444-1.83 17.934-1.83 13.908 0 24.095 2.867 30.561 8.601 6.466 5.734 9.699 14.335 9.699 25.803v37.698Zm-43.737 10.614c6.588 0 11.712-3.111 15.372-9.333V94.897h-7.137c-6.588 0-11.529 1.159-14.823 3.477-3.172 2.318-4.758 5.917-4.758 10.797 0 3.904.976 6.954 2.928 9.15 2.074 2.074 4.88 3.111 8.418 3.111ZM385.343 39.082c3.538 0 6.771.427 9.699 1.281l-4.575 27.999c-3.66-.854-6.588-1.281-8.784-1.281-5.734 0-10.126 2.013-13.176 6.039-2.928 3.904-5.246 9.821-6.954 17.751V139h-28.914V41.827h25.254l2.379 18.849c2.196-6.71 5.49-11.956 9.882-15.738 4.514-3.904 9.577-5.856 15.189-5.856ZM476.882 110.818c0 3.66.488 6.344 1.464 8.052 1.098 1.708 2.806 2.989 5.124 3.843l-6.039 18.849c-5.978-.488-10.858-1.769-14.64-3.843-3.782-2.196-6.71-5.612-8.784-10.248-6.344 9.76-16.104 14.64-29.28 14.64-9.638 0-17.324-2.806-23.058-8.418-5.734-5.612-8.601-12.932-8.601-21.96 0-10.614 3.904-18.727 11.712-24.339 7.808-5.612 19.093-8.418 33.855-8.418h9.882v-4.209c0-5.734-1.22-9.638-3.66-11.712-2.44-2.196-6.71-3.294-12.81-3.294-3.172 0-7.015.488-11.529 1.464-4.514.854-9.15 2.074-13.908 3.66l-6.588-19.032a112.002 112.002 0 0 1 18.666-5.307c6.466-1.22 12.444-1.83 17.934-1.83 13.908 0 24.095 2.867 30.561 8.601 6.466 5.734 9.699 14.335 9.699 25.803v37.698Zm-43.737 10.614c6.588 0 11.712-3.111 15.372-9.333V94.897h-7.137c-6.588 0-11.529 1.159-14.823 3.477-3.172 2.318-4.758 5.917-4.758 10.797 0 3.904.976 6.954 2.928 9.15 2.074 2.074 4.88 3.111 8.418 3.111ZM580.863 41.827 550.302 139h-34.221l-31.293-97.173h31.476l17.202 75.213L551.4 41.827h29.463ZM674.354 88.492c0 4.026-.183 7.503-.549 10.431h-60.207c.976 8.174 3.294 13.908 6.954 17.202 3.66 3.294 8.723 4.941 15.189 4.941 3.904 0 7.686-.671 11.346-2.013 3.66-1.464 7.625-3.66 11.895-6.588l11.895 16.104c-11.346 9.028-23.973 13.542-37.881 13.542-15.738 0-27.816-4.636-36.234-13.908-8.418-9.272-12.627-21.716-12.627-37.332 0-9.882 1.769-18.727 5.307-26.535 3.538-7.93 8.723-14.152 15.555-18.666 6.832-4.636 15.006-6.954 24.522-6.954 14.03 0 25.01 4.392 32.94 13.176 7.93 8.784 11.895 20.984 11.895 36.6Zm-28.365-8.235c-.244-14.884-5.49-22.326-15.738-22.326-5.002 0-8.906 1.83-11.712 5.49-2.684 3.66-4.331 9.699-4.941 18.117h32.391v-1.281ZM716.4 142.111c-8.418 0-15.006-2.379-19.764-7.137-4.636-4.88-6.954-11.773-6.954-20.679V3.214L718.596.103v113.094c0 4.026 1.647 6.039 4.941 6.039 1.708 0 3.294-.305 4.758-.915l5.673 20.496c-5.246 2.196-11.102 3.294-17.568 3.294Z" fill="#565A5D"/><path d="M788.619 90.505h-11.712V139h-30.012V12.181h41.541c17.324 0 30.317 3.172 38.979 9.516 8.784 6.344 13.176 16.043 13.176 29.097 0 8.174-1.952 15.006-5.856 20.496-3.904 5.368-10.065 9.943-18.483 13.725L848.46 139h-33.855l-25.986-48.495Zm-11.712-20.679h12.627c6.71 0 11.712-1.525 15.006-4.575 3.416-3.05 5.124-7.869 5.124-14.457 0-6.1-1.83-10.553-5.49-13.359-3.538-2.806-9.028-4.209-16.47-4.209h-10.797v36.6Z" fill="url(#a)"/><path d="M941.34 88.492c0 4.026-.183 7.503-.549 10.431h-60.207c.976 8.174 3.294 13.908 6.954 17.202 3.66 3.294 8.723 4.941 15.189 4.941 3.904 0 7.686-.671 11.346-2.013 3.66-1.464 7.625-3.66 11.895-6.588l11.895 16.104c-11.346 9.028-23.973 13.542-37.881 13.542-15.738 0-27.816-4.636-36.234-13.908-8.418-9.272-12.627-21.716-12.627-37.332 0-9.882 1.769-18.727 5.307-26.535 3.538-7.93 8.723-14.152 15.555-18.666 6.832-4.636 15.006-6.954 24.522-6.954 14.03 0 25.01 4.392 32.94 13.176 7.93 8.784 11.895 20.984 11.895 36.6Zm-28.365-8.235c-.244-14.884-5.49-22.326-15.738-22.326-5.002 0-8.906 1.83-11.712 5.49-2.684 3.66-4.331 9.699-4.941 18.117h32.391v-1.281Z" fill="url(#b)"/><path d="M1044.69 3.214V139h-25.62l-1.46-11.346c-6.84 9.638-16.05 14.457-27.637 14.457-12.078 0-21.289-4.636-27.633-13.908-6.222-9.272-9.333-21.96-9.333-38.064 0-9.882 1.647-18.727 4.941-26.535 3.294-7.808 7.93-13.908 13.908-18.3 6.1-4.392 13.054-6.588 20.862-6.588 9.272 0 16.962 3.05 23.062 9.15V.103l28.91 3.111Zm-45.75 117.669c6.71 0 12.32-3.66 16.84-10.98V67.996c-2.32-2.806-4.7-4.88-7.14-6.222-2.32-1.342-5-2.013-8.05-2.013-5.371 0-9.641 2.501-12.813 7.503-3.172 5.002-4.758 12.688-4.758 23.058 0 11.346 1.342 19.276 4.026 23.79 2.806 4.514 6.771 6.771 11.895 6.771Z" fill="url(#c)"/><path d="M1101.53 38.716c6.96 0 13.48 1.037 19.58 3.111 6.1 2.074 11.47 5.002 16.11 8.784l-10.62 16.287c-7.93-5.002-15.92-7.503-23.97-7.503-3.78 0-6.71.671-8.78 2.013-1.96 1.22-2.93 2.989-2.93 5.307 0 1.83.42 3.355 1.28 4.575.97 1.098 2.87 2.257 5.67 3.477 2.81 1.22 7.14 2.684 12.99 4.392 10.13 2.928 17.63 6.771 22.51 11.529 5.01 4.636 7.51 11.102 7.51 19.398 0 6.588-1.89 12.322-5.68 17.202-3.78 4.758-8.96 8.418-15.55 10.98-6.59 2.562-13.91 3.843-21.96 3.843-8.18 0-15.8-1.281-22.88-3.843-6.95-2.562-12.87-6.1-17.75-10.614l14.09-15.738c8.18 6.344 16.78 9.516 25.81 9.516 4.39 0 7.8-.793 10.24-2.379 2.57-1.586 3.85-3.843 3.85-6.771 0-2.318-.49-4.148-1.47-5.49-.97-1.342-2.86-2.562-5.67-3.66-2.81-1.22-7.26-2.684-13.36-4.392-9.64-2.806-16.83-6.71-21.59-11.712-4.76-5.002-7.14-11.224-7.14-18.666 0-5.612 1.59-10.614 4.76-15.006 3.29-4.514 7.93-8.052 13.91-10.614 6.1-2.684 13.11-4.026 21.04-4.026Z" fill="url(#d)"/><path d="M1206 139.183c-3.91 12.566-10.31 22.204-19.22 28.914-8.78 6.832-20.62 10.675-35.5 11.529l-3.11-20.313c9.27-1.22 16.1-3.294 20.49-6.222 4.52-2.928 8.12-7.625 10.8-14.091h-9.88l-29.65-97.173h30.75l17.38 78.69 19.03-78.69h29.83L1206 139.183Z" fill="url(#e)"/><path d="M1279.71 38.716c6.95 0 13.48 1.037 19.58 3.111 6.1 2.074 11.47 5.002 16.1 8.784l-10.61 16.287c-7.93-5.002-15.92-7.503-23.98-7.503-3.78 0-6.71.671-8.78 2.013-1.95 1.22-2.93 2.989-2.93 5.307 0 1.83.43 3.355 1.28 4.575.98 1.098 2.87 2.257 5.68 3.477 2.8 1.22 7.13 2.684 12.99 4.392 10.13 2.928 17.63 6.771 22.51 11.529 5 4.636 7.5 11.102 7.5 19.398 0 6.588-1.89 12.322-5.67 17.202-3.78 4.758-8.97 8.418-15.56 10.98-6.58 2.562-13.9 3.843-21.96 3.843-8.17 0-15.8-1.281-22.87-3.843-6.96-2.562-12.87-6.1-17.75-10.614l14.09-15.738c8.17 6.344 16.77 9.516 25.8 9.516 4.39 0 7.81-.793 10.25-2.379 2.56-1.586 3.84-3.843 3.84-6.771 0-2.318-.49-4.148-1.46-5.49-.98-1.342-2.87-2.562-5.67-3.66-2.81-1.22-7.26-2.684-13.36-4.392-9.64-2.806-16.84-6.71-21.6-11.712-4.76-5.002-7.13-11.224-7.13-18.666 0-5.612 1.58-10.614 4.75-15.006 3.3-4.514 7.93-8.052 13.91-10.614 6.1-2.684 13.12-4.026 21.05-4.026Z" fill="url(#f)"/><path d="M126 79c0 34.794-28.206 63-63 63S0 113.794 0 79s28.206-63 63-63 63 28.206 63 63Z" fill="url(#g)"/><defs><linearGradient id="a" x1="1323" y1="78" x2="749" y2="78" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5D02"/><stop offset="1" stop-color="#FAB911"/></linearGradient><linearGradient id="b" x1="1323" y1="78" x2="749" y2="78" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5D02"/><stop offset="1" stop-color="#FAB911"/></linearGradient><linearGradient id="c" x1="1323" y1="78" x2="749" y2="78" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5D02"/><stop offset="1" stop-color="#FAB911"/></linearGradient><linearGradient id="d" x1="1323" y1="78" x2="749" y2="78" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5D02"/><stop offset="1" stop-color="#FAB911"/></linearGradient><linearGradient id="e" x1="1323" y1="78" x2="749" y2="78" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5D02"/><stop offset="1" stop-color="#FAB911"/></linearGradient><linearGradient id="f" x1="1323" y1="78" x2="749" y2="78" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5D02"/><stop offset="1" stop-color="#FAB911"/></linearGradient><linearGradient id="g" x1="21" y1="38.721" x2="83.311" y2="99.656" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5D02"/><stop offset="1" stop-color="#FAB911"/></linearGradient></defs></svg>
</div>

</body>
</html>
