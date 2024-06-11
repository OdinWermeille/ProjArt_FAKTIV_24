<!DOCTYPE html>
<html>

<head>
    <title>Laravel Vue Application</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>

<body>
    <div id="app">
        <sentiers-list></sentiers-list>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>

    <button id="getLocationBtn">Demander la localisation</button>

    <div id="locationInfo" style="display: none;">
        <p>Latitude: <span id="latitude"></span></p>
        <p>Longitude: <span id="longitude"></span></p>
    </div>

    <script src="{{ asset('js/location.js') }}"></script>
</body>

</html>