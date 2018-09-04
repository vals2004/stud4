$(function(){
  var output = document.getElementById("out");

  if (!navigator.geolocation){
    output.innerHTML = "<p>Geolocation is not supported by your browser</p>";
  }

  function success(position) {
    var latitude  = position.coords.latitude;
    var longitude = position.coords.longitude;

      $.get('https://stud.ddns.net/location/?lat=' + latitude + '&long=' + longitude, function (data) {
        console.log(data);
        output.innerHTML = data;
    });
  }

  function error() {
    $.get('/app_dev.php/error');
    output.innerHTML = "Невозможно определить ваше положение, проверьте пожалуйста своё устройство и перегрузите страницу.";
  }

  output.innerHTML = "<p>Позиционирование (может занять время)…</p>";

  navigator.geolocation.getCurrentPosition(success, error);
})

