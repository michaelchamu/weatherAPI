<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
class TemperatureFunctions extends Controller
{
    //
    public static function NamibiaStation ($location, $stationtype, $interval, $from, $to) {
        switch (strtolower($stationtype)) {
            case 'mcs':
                if (strtolower($interval) == 'monthly') {
                    if ($to == null){
                        $results = DB::select( DB::raw("
                                                        SELECT monat as date, ambienttempmin, ambienttempmax, ambienttempavg, soiltempavg, latitude, longitude, stationname, country
                                                        FROM MCS_MonthlyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON fk_Logger_ID = LoggerSerial
                                                        WHERE Monat = :fromDate
                                                          "),
                                                array('fromDate' => $from));
                    } else {
                        $results = DB::select( DB::raw("
                                                        SELECT monat as date, ambienttempmin, ambienttempmax, ambienttempavg, soiltempavg, latitude, longitude, stationname, country
                                                        FROM MCS_MonthlyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON fk_Logger_ID = LoggerSerial
                                                        WHERE Monat >= :fromDate
                                                        AND Monat <= :toDate
                                                          "),
                                                array('fromDate' => $from,
                                                        'toDate' => $to ));
                    }
                } elseif (strtolower($interval) == 'daily') {
                    if ($to == null){
                        $results = DB::select( DB::raw("
                                                        SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                        FROM MCS_DailyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON fk_Logger_ID = LoggerSerial
                                                        WHERE datum = :fromDate
                                                          "),
                                                array('fromDate' => $from ));
                    } else {
                        $results = DB::select( DB::raw("
                                            SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                            FROM MCS_DailyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE datum >= :fromDate
                                            AND datum <= :toDate
                                                          "),
                                                array('fromDate' => $from,
                                                        'toDate' => $to ));
                    }
                } elseif (strtolower($interval) == 'hourly') {
                    if ($to == null){
                        $results = DB::select( DB::raw("
                                            SELECT datum as date, temp_ambient, soiltempave, latitude, longitude, stationname, country
                                            FROM MCS_HourlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE datum = :fromDate
                                                          "),
                                                array('fromDate' => $from ));
                    } else {
                        $results = DB::select( DB::raw("
                                                        SELECT datum as date, temp_ambient, soiltempave, latitude, longitude, stationname, country
                                                        FROM MCS_HourlyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON fk_Logger_ID = LoggerSerial
                                                        WHERE datum >= :fromDate
                                                        AND datum <= :toDate
                                                                      "),
                                                            array('fromDate' => $from,
                                                                    'toDate' => $to ));
                    }
                } else {
                    return response()->json([
                        'message' => 'Invalid interval specified',
                    ], 400);
                }
            break;
            case 'typ1':
                if (strtolower($interval) == 'monthly') {
                    if ($to == null){
                        $results = DB::select( DB::raw("
                                                SELECT Monat as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                FROM Typ1_MonthlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Monat = :fromDate
                                                AND country = :location
                                                  "),
                                        array( 'location' => $location,
                                                'fromDate' => $from));
                    } else {
                        $results = DB::select( DB::raw("
                                                SELECT Monat as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                FROM Typ1_MonthlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Monat >= :fromDate
                                                AND Monat <= :toDate
                                                AND country = :location
                                                  "),
                                        array( 'location' => $location,
                                                'fromDate' => $from,
                                                'toDate' => $to ));
                    }
                } elseif (strtolower($interval) == 'daily') {
                    if ($to == null){
                        $results = DB::select( DB::raw("
                                                SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                FROM Typ1_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE datum = :fromDate
                                                AND country = :location
                                                  "),
                                        array( 'location' => $location,
                                                'fromDate' => $from ));
                    } else {
                        $results = DB::select( DB::raw("
                                                SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                FROM Typ1_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE datum >= :fromDate
                                                AND datum <= :toDate
                                                AND country = :location
                                                  "),
                                        array( 'location' => $location,
                                                'fromDate' => $from,
                                                'toDate' => $to ));
                    }
                } elseif (strtolower($interval) == 'hourly') {
                    if ($to == null){
                        $results = DB::select( DB::raw("
                                                SELECT datum as date, hour, minute, Temp_ambient, soiltempave, temp_ground, latitude, longitude, stationname, country
                                                FROM Typ1_HourlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE datum = :fromDate
                                                AND country = :location
                                                  "),
                                        array( 'location' => $location,
                                                'fromDate' => $from));
                    } else {
                        $results = DB::select( DB::raw("
                                                SELECT datum as date, hour, minute, Temp_ambient, soiltempave, temp_ground, latitude, longitude, stationname, country
                                                FROM Typ1_HourlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE datum >= :fromDate
                                                AND datum <= :toDate
                                                AND country = :location
                                                  "),
                                        array( 'location' => $location,
                                                'fromDate' => $from,
                                                'toDate' => $to ));
                    }
                } else {
                    return response()->json([
                        'message' => 'Invalid interval specified',
                    ], 400);
                }
            break;

            default:
                return response()->json([
                    'message' => 'Invalid interval specified',
                ], 400);
            break;
        }
        return $results;
    }

}
