<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>Buy apartment</title>
</head>
<body>
    <h1>Buy apartment</h1>
    <a href="/">Back to main page</a>
    @if(!empty($data) && count($data) > 0)
        @foreach($data as $apartment)
            <div class="image-content-row">
                <img src="{{ $apartment['src'] }}" alt="Apartment Image" class="image">
                <div class="border-house">
                    <h3>
                        <span style="font-size: 1.2em;">{{ $apartment['price'] }}, </span>
                        <span style="font-size: 0.8em;">{{ $apartment['meter_price'] }}</span>
                    </h3>
                    <h4><a class="black-text" href="https://dom.ria.com{{ $apartment['href'] }}">{{ $apartment['name'] }}</a></h4>
                    @if($apartment['area'] === null)
                        <h5>{{ $apartment['city'] }}</h5>
                    @else
                        <h5>{{ $apartment['area'] }} {{ $apartment['city'] }}</h5>
                    @endif
                    <h6>{{ $apartment['rooms'] }}, {{ $apartment['square'] }}, {{ $apartment['floor'] }}</h6>
                </div>
            </div>
        @endforeach
    @else
        <p>No data about apartments</p>
    @endif

</body>
</html>
