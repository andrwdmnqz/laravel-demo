<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function weatherView() {
        return view('weather');
    }

    public function getWeather(Request $request) {
        $city = $request->city;
        $apiKey = '47f93007cf30db9a684a7f18ca19cbda';

        $response = Http::get('https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&appid=' . $apiKey . '&units=metric');

        return response()->json($response->json());
    }
}
