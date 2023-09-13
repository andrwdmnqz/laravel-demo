<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function weatherView() {
        return view('weather');
    }

    public function getWeather(Request $request) {
        $city = $request->city;
        $apiKey = Config::get('services.openweathermap.key');

        $response = Http::get('https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&appid=' . $apiKey . '&units=metric');

        return response()->json($response->json());
    }
}
