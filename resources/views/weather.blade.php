<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="css/app.css" rel="stylesheet">
    <title>Weather page</title>
</head>
<body style="background-color: #9ca3af">
    <h2>Weather page</h2>
    <a href="/">Back to main page</a>
    <form id="weather-form">
        @csrf
        <label for="#city">Choose city: </label>
        <input type="text" id="city" name="city">
        <input type="submit" value="Search">
    </form>

    <div id="weather-results">
        <h4 id="city-name">City name: </h4>
        <h4 id="temperature">Temperature: </h4>
        <img src="" alt="icon" id="weather-icon">
        <h4 id="description">Description: </h4>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>

        $(document).ready(function() {
            getWeatherByDefault();
        });

        // Function for default city
        function getWeatherByDefault() {
            $.ajax({
                url: '{{ route('get_weather') }}',
                method: 'post',
                data: {
                    city: 'Kyiv'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response);
                    $('#city-name').html('City name: ' + response.name);
                    $('#temperature').html('Temperature: ' + response.main.temp + '°C');
                    $('#weather-icon').attr('src', 'https://openweathermap.org/img/wn/' + response.weather[0].icon + '.png');
                    $('#description').html('Description: ' + response.weather[0].description.charAt(0).toUpperCase() + response.weather[0].description.slice(1));
                }
            })
        }

        $('#weather-form').submit(function(event) {
            event.preventDefault();

            var city = $('#city').val();

            $.ajax({
                url: '{{ route('get_weather') }}',
                method: 'post',
                data: {
                    city: city
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response);
                    $('#city-name').html('City name: ' + response.name);
                    $('#temperature').html('Temperature: ' + response.main.temp + '°C');
                    $('#weather-icon').attr('src', 'https://openweathermap.org/img/wn/' + response.weather[0].icon + '.png');
                    $('#description').html('Description: ' + response.weather[0].description.charAt(0).toUpperCase() + response.weather[0].description.slice(1));
                }
            });
        });
    </script>
</body>
</html>
