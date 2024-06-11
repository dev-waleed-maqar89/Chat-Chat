<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css']) <title>Chat-chat</title>
</head>

<body>
    <div id="app">
        <router-view>

        </router-view>
    </div>
    @vite(['resources/js/app.js'])
</body>

</html>
