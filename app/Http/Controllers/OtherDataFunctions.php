<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
class OtherDataFunctions extends Controller
{
    //
    public static function GetResultsYearMonth ($location, $year, $month) {
        if (strtolower($location) == 'namibia') {
            if (is_null($month)){
                $results = DB::select( DB::raw("SELECT Datum as date,Humidity, BaromPress, WindSpeedAvg, StationName, Latitude, Longitude, Country
                                                FROM MCS_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE YEAR(DATUM) = :year
                                                AND country = :location
                                                ORDER BY Datum"),
                                                array('location' => $location,
                                                'year' => $year));
            } else {
                $results = DB::select( DB::raw("SELECT Datum as date,Humidity, BaromPress, WindSpeedAvg, StationName, Latitude, Longitude, Country
                                                FROM MCS_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE YEAR(DATUM) = :year
                                                AND MONTH(Datum) = :month
                                                AND country = :location
                                                ORDER BY Datum"),
                                                array('location' => $location,
                                                'year' => $year,
                                                'month' => $month));
            }
        } elseif (strtolower($location) == 'zambia' || strtolower($location) == 'angola' || strtolower($location) == 'botswana' || strtolower($location) == 'south africa'){
            if (is_null($month)){
                $results = DB::select( DB::raw("SELECT Datum as date,Humidity, BaromPress, WindSpeedAvg, StationName, Latitude, Longitude, Country
                                                FROM Typ1_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE YEAR(DATUM) = :year
                                                AND country = :location
                                                ORDER BY Datum"),
                                                array('location' => $location,
                                                'year' => $year));
            } else {
                $results = DB::select( DB::raw("SELECT Datum as date,Humidity, BaromPress, WindSpeedAvg, StationName, Latitude, Longitude, Country
                                                FROM Typ1_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE YEAR(DATUM) = :year
                                                AND MONTH(Datum) = :month
                                                AND country = :location
                                                ORDER BY Datum"),
                                                array('location' => $location,
                                                'year' => $year,
                                                'month' => $month));
            }
        }
        return $results;
    }
    public static function GetResultsStationType ($location, $stationtype, $interval, $from, $to) {
        $Instance = new OtherDataFunctions();
        if (is_null($stationtype)){
            $results = $Instance->IntervalsNoStations($location, $interval, $from, $to);
        } else {
            $results = $Instance->IntervalsWithStations($stationtype, $location, $interval, $from, $to);
        }
        return $results;
    }
    public static function IntervalsNoStations($location, $interval, $from, $to){
        $Instance = new OtherDataFunctions();
        if (strtolower($location) == 'namibia') {
                    if(!is_null($to)) {
                        $results = $Instance->toNotNullNamibia ($location, $interval, $from, $to);
                    } else {
                        $results = $Instance->toNullNamibia ($location, $interval, $from);
                }
        } elseif (strtolower($location) == 'zambia' || strtolower($location) == 'angola' || strtolower($location) == 'botswana' || strtolower($location) == 'south africa'){
                    if (!is_null($to)) {
                            $results = $Instance->toNotNullOther ($location, $interval, $from, $to);
                    } else {
                            $results = $Instance->toNullOther ($location, $interval, $from);
                    }
        } else {
                return response()->json([
                       'message' => 'Invalid country',
                   ], 400);
            }
            return $results;
    }
    public static function IntervalsWithStations($station, $location, $interval, $from, $to ) {
        $Instance = new OtherDataFunctions();
        if (strtolower($location) == 'namibia') {
            if(is_null($to)){
                $results = $Instance->NamibiaStationsNullTo($station, $location, $interval, $from);
            } else {
                $results = $Instance->NamibiaStationTo($station, $location, $interval, $from, $to);
            }
        } elseif (strtolower($location) == 'zambia' || strtolower($location) == 'angola' || strtolower($location) == 'botswana' || strtolower($location) == 'south africa') {
            if(is_null($to)){
                $results = $Instance->OtherStationToNull($station, $location, $interval, $from);
            } else {
                $results = $Instance->OtherStationTo($station, $location, $interval, $from, $to);
            }
        } else {
            return response()->json([
                   'message' => 'Invalid country',
               ], 400);
        }
        return $results;
    }
    public function NamibiaStationsNullTo($station, $location, $interval, $from) {
        if (strtolower($station) == 'mcs') {
            switch (strtolower($interval)) {
                case 'daily':
                    $results = $this->LocationFrom($location, $from);
                break;
                case 'monthly':
                    $results = $this->MonthlyLocationFrom($location, $from);
                break;
                case 'hourly':
                    $results = $this->HourlyLocationFrom($location, $from);
                break;
                default:
                 return response()->json([
                        'message' => 'Invalid interval',
                    ], 400);
                break;
            }
        } elseif (strtolower($station) == 'typ1') {
            switch ($interval) {
                case 'daily':
                    $results = $this->LocationFrom($location, $from);
                break;
                case 'monthly':
                    $results = $this->MonthlyLocationFrom($location, $from);
                break;
                case 'hourly':
                    $results = $this->HourlyLocationFrom($location, $from);
                break;
                default:
                 return response()->json([
                        'message' => 'Invalid interval',
                    ], 400);
                break;
            }
        } else {
            return response()->json([
                   'message' => 'Invalid station',
               ], 400);
        }
    }
    public  function NamibiaStationTo($station, $location, $interval, $from, $to) {
        if (strtolower($station) == 'mcs') {
            switch (strtolower($interval)) {
                case 'daily':
                    $results = $this->LocationFromTo($location, $from, $to);
                break;
                case 'monthly':
                    $results = $this->MonthlyLocationFromTo($location, $from, $to);
                break;
                case 'hourly':
                    $results = $this->HourlyLocationFromTo($location, $from, $to);
                break;
                default:
                 return response()->json([
                        'message' => 'Invalid interval',
                    ], 400);
                break;
            }
            return $results;
        } elseif (strtolower($station) == 'typ1') {
            switch ($interval) {
                case 'daily':
                    $results = $this->LocationFromTo($location, $from, $to);
                break;
                case 'monthly':
                    $results = $this->MonthlyLocationFromTo($location, $from, $to);
                break;
                case 'hourly':
                    $results = $this->HourlyLocationFromTo($location, $from, $to);
                break;
                default:
                 return response()->json([
                        'message' => 'Invalid interval',
                    ], 400);
                break;
            }
            return $results;
        } else {
            return response()->json([
                   'message' => 'Invalid station',
               ], 400);
        }
    }
    public  function OtherStationToNull ($station, $location, $interval, $from) {
        if (strtolower($station) == 'mcs') {
            switch (strtolower($interval)) {
                case 'daily':
                    $results = $this->LocationFrom($location, $from);
                break;
                case 'monthly':
                    $results = $this->MonthlyLocationFrom($location, $from);
                break;
                case 'hourly':
                    $results = $this->HourlyLocationFrom($location, $from);
                break;
                default:
                     return response()->json([
                            'message' => 'Invalid interval',
                        ], 400);
                break;
            }
            return $results;
        } elseif (strtolower($station) == 'typ1') {
            switch ($interval) {
                case 'daily':
                    $results = $this->LocationFrom($location, $from);
                break;
                case 'monthly':
                    $results = $this->MonthlyLocationFrom($location, $from);
                break;
                case 'hourly':
                    $results = $this->HourlyLocationFrom($location, $from);
                break;
                default:
                     return response()->json([
                            'message' => 'Invalid interval',
                        ], 400);
                break;
            }
            return $results;
        } else {
            return response()->json([
                   'message' => 'Invalid station',
               ], 400);
        }
    }
    public  function OtherStationTo ($station, $location, $interval, $from, $to) {
        if (strtolower($station) == 'mcs') {
            switch (strtolower($interval)) {
                case 'daily':
                    $results = $this->LocationFromTo($location, $from, $to);
                break;
                case 'monthly':
                    $results = $this->MonthlyLocationFromTo($location, $from, $to);
                break;
                case 'hourly':
                    $results = $this->HourlyLocationFromTo($location, $from, $to);
                break;
                default:
                 return response()->json([
                        'message' => 'Invalid interval',
                    ], 400);
                break;
            }
        } elseif (strtolower($station) == 'typ1') {
            switch ($interval) {
                case 'daily':
                    $results = $this->LocationFromTo($location, $from, $to);
                break;
                case 'monthly':
                    $results = $this->MonthlyLocationFromTo($location, $from, $to);
                break;
                case 'hourly':
                    $results = $this->HourlyLocationFromTo($location, $from, $to);
                break;
                default:
                 return response()->json([
                        'message' => 'Invalid interval',
                    ], 400);
                break;
            }
            return $results;
        } else {
            return response()->json([
                   'message' => 'Invalid station',
               ], 400);
        }
    }
    public  function toNotNullNamibia ($location, $interval, $from, $to){
        switch (strtolower($interval)) {
            case 'daily':
                $results = $this->LocationFromTo($location, $from, $to);
            break;
            case 'monthly':
                $results = $this->MonthlyLocationFromTo($location, $from, $to);
            break;
            case 'hourly':
                $results = $this->HourlyLocationFromTo($location, $from, $to);
            break;
            default:
                return response()->json([
                       'message' => 'Invalid interval',
                   ], 400);
            break;
        }
        return $results;
    }
    public function toNullNamibia ($location, $interval, $from) {
        switch (strtolower($interval)) {
            case 'daily':
                $results = $this->LocationFrom($location, $from);
            break;
            case 'monthly':
                $results = DB::select( DB::raw("SELECT Monat as date, Humidity, WindSpeedAvg, WindDirectionAvg, StationName, Latitude, Longitude, country
                                                FROM MCS_MonthlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Monat >= :fromx"),
                                                array('fromx' => $from));
            break;
            case 'hourly':
                $results = DB::select( DB::raw("SELECT Datum as date,Hour, Humidity, BaromPress, MaxWindSpeed, StationName, Latitude, Longitude, Country
                                                FROM MCS_HourlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Datum >= :fromx"),
                                                array('fromx' => $from));
            break;
            default:
                return response()->json([
                       'message' => 'Invalid interval',
                   ], 400);
            break;
        }
        return $results;
    }
    public function toNotNullOther ($location, $interval, $from, $to) {
        switch (strtolower($interval)) {
            case 'daily':
                $results = $this->LocationFromTo($location, $from, $to);
            break;
            case 'monthly':
                $results = DB::select( DB::raw("SELECT Monat as date, Monat as date, Fog1, Humidity, BaromPress, WindSpeedAvg, WindDirectionAvg, DewPoint, StationName, Latitude, Longitude, country
                                                FROM Typ1_MonthlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Monat >= :fromx
                                                AND Monat <= :to
                                                AND Country = :location"),
                                                array('fromx' => $from,
                                                'location' => $location,
                                                'to' => $to));
            break;
            case 'hourly':
                $results = DB::select( DB::raw("SELECT Datum as date,Datum as date, Hour, Minute, Timezone, Fog1, Humidity, WindDirection, WindSpeed,
                                                BaromPress, WetBulb, DewPoint_AVG, StationName, Latitude, Longitude, Country
                                                FROM Typ1_HourlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Datum >= :fromx
                                                AND Datum <= :to
                                                AND Country = :location"),
                                                array('fromx' => $from,
                                                'location' => $location,
                                                'to' => $to));
            break;
            default:
                return response()->json([
                       'message' => 'Invalid interval',
                   ], 400);
            break;
        }
        return $results;
    }
    public function toNullOther ($location, $interval, $from) {
        switch (strtolower($interval)) {
            case 'daily':
                $results = $this->LocationFrom($location, $from);
            break;
            case 'monthly':
                $results = DB::select( DB::raw("SELECT Monat as date, Monat as date, Fog1, Humidity, BaromPress, WindSpeedAvg, WindDirectionAvg, DewPoint, StationName, Latitude, Longitude, country
                                                FROM Typ1_MonthlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Monat >= :fromx
                                                AND Country = :location"),
                                                array('fromx' => $from,
                                                'location' => $location));

            break;
            case 'hourly':
                $results = DB::select( DB::raw("SELECT Datum as date,Datum as date, Hour, Minute, Timezone, Fog1, Humidity, WindDirection, WindSpeed,
                                                BaromPress, WetBulb, DewPoint_AVG, StationName, Latitude, Longitude, Country
                                                FROM Typ1_HourlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Datum >= :fromx
                                                AND Country = :location"),
                                                array('fromx' => $from,
                                                'location' => $location));
            break;
            default:
                return response()->json([
                       'message' => 'Invalid interval',
                   ], 400);
            break;
        }
        return $results;
    }
    public static function LocationFromTo ($location, $from, $to) {
        if (strtolower($location) == 'namibia') {
            $results = DB::select( DB::raw("SELECT Datum as date, Humidity, WindSpeedAvg, WindDirectionAvg, StationName, Latitude, Longitude, country
                                        FROM MCS_DailyData
                                        RIGHT JOIN All_WeatherStations
                                        ON fk_Logger_ID = LoggerSerial
                                        WHERE DATUM >= :fromx
                                        AND DATUM <= :to"),
                                        array('fromx' => $from,
                                        'to' => $to));
        } elseif (strtolower($location) == 'angola' || strtolower($location) == 'botswana' || strtolower($location) == 'south africa' || strtolower($location) == 'zambia'){
            $results = DB::select( DB::raw("SELECT Datum as date, Fog1, Humidity, BaromPress, WindSpeedAvg, WindDirectionAvg, DewPoint, StationName, Latitude, Longitude, country
                                            FROM Typ1_DailyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE DATUM >= :fromx
                                            AND DATUM <= :to
                                            AND Country = :location"),
                                            array('fromx' => $from,
                                            'location' => $location,
                                            'to' => $to));
        } else {
            return response()->json([
                'message' => 'Invalid country'
            ], 400);
        }
        return $results;
    }
    public static function LocationFrom ($location, $from) {
        if (strtolower($location) == 'namibia') {
            $results = DB::select( DB::raw("SELECT Datum as date, Humidity, WindSpeedAvg, WindDirectionAvg, StationName, Latitude, Longitude, country
                                        FROM MCS_DailyData
                                        RIGHT JOIN All_WeatherStations
                                        ON fk_Logger_ID = LoggerSerial
                                        WHERE DATUM >= :fromx"),
                                        array('fromx' => $from));
        } elseif (strtolower($location) == 'angola' || strtolower($location) == 'botswana' || strtolower($location) == 'south africa' || strtolower($location) == 'zambia'){
            $results = DB::select( DB::raw("SELECT Datum as date, Fog1, Humidity, BaromPress, WindSpeedAvg, WindDirectionAvg, DewPoint, StationName, Latitude, Longitude, country
                                            FROM Typ1_DailyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE DATUM >= :fromx
                                            AND Country = :location"),
                                            array('fromx' => $from,
                                            'location' => $location));
        } else {
            return response()->json([
                'message' => 'Invalid country'
            ], 400);
        }
        return $results;
    }
    public static function MonthlyLocationFromTo($location, $from, $to){
        if (strtolower($location) == 'namibia') {
            $results = DB::select( DB::raw("SELECT Monat as date, Humidity, WindSpeedAvg, WindDirectionAvg, StationName, Latitude, Longitude, country
                                            FROM MCS_MonthlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Monat >= :fromx
                                            AND Monat <= :to"),
                                            array('fromx' => $from,
                                            'to' => $to));
        } elseif (strtolower($location) == 'south africa' || strtolower($location) == 'angola' || strtolower($location) == 'zambia' || strtolower($location) == 'botswana'){
            $results = DB::select( DB::raw("SELECT Monat as date, Monat as date, Fog1, Humidity, BaromPress, WindSpeedAvg, WindDirectionAvg, DewPoint, StationName, Latitude, Longitude, country
                                            FROM Typ1_MonthlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Monat >= :fromx
                                            AND Monat <= :to
                                            AND Country = :location"),
                                            array('fromx' => $from,
                                            'location' => $location,
                                            'to' => $to));
        } else {
            return response()->json([
                'message' => 'Invalid country'
            ], 400);
        }
        return $results;
    }
    public static function MonthlyLocationFrom($location, $from){
        if (strtolower($location) == 'namibia') {
            $results = DB::select( DB::raw("SELECT Monat as date, Humidity, WindSpeedAvg, WindDirectionAvg, StationName, Latitude, Longitude, country
                                            FROM MCS_MonthlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Monat >= :fromx"),
                                            array('fromx' => $from));
        } elseif (strtolower($location) == 'south africa' || strtolower($location) == 'angola' || strtolower($location) == 'zambia' || strtolower($location) == 'botswana'){
            $results = DB::select( DB::raw("SELECT Monat as date, Monat as date, Fog1, Humidity, BaromPress, WindSpeedAvg, WindDirectionAvg, DewPoint, StationName, Latitude, Longitude, country
                                            FROM Typ1_MonthlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Monat >= :fromx
                                            AND Country = :location"),
                                            array('fromx' => $from,
                                            'location' => $location));
        } else {
            return response()->json([
                'message' => 'Invalid country'
            ], 400);
        }
        return $results;
    }
    public static function HourlyLocationFromTo($location, $from, $to){
        if (strtolower($location) == 'namibia') {
            $results = DB::select( DB::raw("SELECT Datum as date,Hour, Humidity, BaromPress, MaxWindSpeed, StationName, Latitude, Longitude, Country
                                            FROM MCS_HourlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Datum >= :fromx
                                            AND Datum <= :to"),
                                            array('fromx' => $from,
                                            'to' => $to));
        } elseif (strtolower($location) == 'south africa' || strtolower($location) == 'angola' || strtolower($location) == 'zambia' || strtolower($location) == 'botswana'){
            $results =  DB::select( DB::raw("SELECT Datum as date,Datum as date, Hour, Minute, Timezone, Fog1, Humidity, WindDirection, WindSpeed,
                                            BaromPress, WetBulb, DewPoint_AVG, StationName, Latitude, Longitude, Country
                                            FROM Typ1_HourlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Datum >= :fromx
                                            AND Datum <= :to
                                            AND Country = :location"),
                                            array('fromx' => $from,
                                            'location' => $location,
                                            'to' => $to));
        } else {
            return response()->json([
                'message' => 'Invalid country'
            ], 400);
        }
        return $results;
    }
    public static function HourlyLocationFrom($location, $from){
        if (strtolower($location) == 'namibia') {
            $results = DB::select( DB::raw("SELECT Datum as date,Hour, Humidity, BaromPress, MaxWindSpeed, StationName, Latitude, Longitude, Country
                                            FROM MCS_HourlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Datum >= :fromx"),
                                            array('fromx' => $from));
        } elseif (strtolower($location) == 'south africa' || strtolower($location) == 'angola' || strtolower($location) == 'zambia' || strtolower($location) == 'botswana'){
            $results = DB::select( DB::raw("SELECT Datum as date,Datum as date, Hour, Minute, Timezone, Fog1, Humidity, WindDirection, WindSpeed,
                                            BaromPress, WetBulb, DewPoint_AVG, StationName, Latitude, Longitude, Country
                                            FROM Typ1_HourlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Datum = :fromx
                                            AND Country = :location"),
                                            array('fromx' => $from,
                                            'location' => $location));
        } else {
            return response()->json([
                'message' => 'Invalid country'
            ], 400);
        }
        return $results;
    }
}
