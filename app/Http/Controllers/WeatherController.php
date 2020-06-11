<?php

namespace App\Http\Controllers;
use Zttp\Zttp;
use App\City;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    function getCity()
    {
    	$query = trim(request('query'));
	    $cities = City::where('city_name', 'like', $query.'%')->get();
    	return response()->json($cities);
    }

    function getWeather()
    {
    	$api_key 	= config('services.weather.key');
		$uri 		= config('services.weather.uri');
		$cityid		= request('cityid');
	    $response 	= Zttp::get("$uri?id=$cityid&units=metric&APPID=$api_key");

	    return $response->json();
    }
}
