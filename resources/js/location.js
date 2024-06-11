document.addEventListener('DOMContentLoaded', function () {
    const getLocationBtn = document.getElementById('getLocationBtn');
    const locationInfo = document.getElementById('locationInfo');
    const latitudeSpan = document.getElementById('latitude');
    const longitudeSpan = document.getElementById('longitude');

    getLocationBtn.addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            alert("La géolocalisation n'est pas prise en charge par ce navigateur.");
        }
    });

    function showPosition(position) {
        latitudeSpan.textContent = position.coords.latitude;
        longitudeSpan.textContent = position.coords.longitude;
        locationInfo.style.display = 'block';
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("L'utilisateur a refusé la demande de géolocalisation.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Les informations de localisation sont indisponibles.");
                break;
            case error.TIMEOUT:
                alert("La demande de localisation a expiré.");
                break;
            case error.UNKNOWN_ERROR:
                alert("Une erreur inconnue s'est produite.");
                break;
        }
    }
});
