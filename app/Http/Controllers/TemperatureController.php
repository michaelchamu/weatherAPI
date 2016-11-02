<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
class TemperatureController extends Controller
{

    function temperatureByDates($location, $from, $to = null){
        if(strtolower($location) == 'namibia'){
            if (isset($to)) {
                if (RainFunctions::checkDate($to) && RainFunctions::checkDate($from) && RainFunctions::dateComparison($from, $to)){
                    $results['Temperature_By_Dates'] = DB::select( DB::raw("
                                                    SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                    FROM MCS_DailyData
                                                    RIGHT JOIN All_WeatherStations
                                                    ON fk_Logger_ID = LoggerSerial
                                                    WHERE datum >= :fromDate
                                                    AND datum <= :toDate
                                                      "),
                                            array('fromDate' => $from,
                                                    'toDate' => $to ));
                } else {
                    return response()->json([
                        'message' => 'Invalid date format',
                    ], 400);
                }
            } else {
                if (RainFunctions::checkDate($from)) {
                    $results['Temperature_By_Dates'] = DB::select( DB::raw("
                                                    SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                    FROM MCS_DailyData
                                                    RIGHT JOIN All_WeatherStations
                                                    ON fk_Logger_ID = LoggerSerial
                                                    WHERE datum = :fromDate
                                                      "),
                                            array('fromDate' => $from));
                } else {
                    return response()->json([
                        'message' => 'Invalid date format',
                    ], 400);
                }
            }
        } elseif (strtolower($location) == 'zambia' || strtolower($location) == 'botswana' || strtolower($location) == 'south africa' || strtolower($location) == 'angola'){
                    if (isset($to)) {
                        if (RainFunctions::checkDate($to) && RainFunctions::checkDate($from) && RainFunctions::dateComparison($from, $to)) {
                                $results['Temperature_By_Dates'] = DB::select( DB::raw("
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
                            } else {
                                return response()->json([
                                    'message' => 'Invalid date format',
                                ], 400);
                            }
                    } else {
                        if (RainFunctions::checkDate($from)){
                            $results['Temperature_By_Dates'] = DB::select( DB::raw("
                                                    SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                    FROM Typ1_DailyData
                                                    RIGHT JOIN All_WeatherStations
                                                    ON fk_Logger_ID = LoggerSerial
                                                    WHERE datum = :fromDate
                                                    AND country = :location
                                                      "),
                                            array( 'location' => $location,
                                                    'fromDate' => $from));
                        } else {
                            return response()->json([
                                'message' => 'Invalid date format',
                            ], 400);
                        }
                    }
        } else {
            return response()->json([
                'message' => 'Invalid country',
            ], 404);
        }
        if ($results['Temperature_By_Dates'] == []) {
            return response()->json([
                'message' => 'No records found',
            ], 404);
        } else {
                return json_encode($results);
        }
    }

    function temperatureByStation($location, $stationtype, $interval, $from, $to = null) {
        if(strtolower($location) == 'namibia') {
            if (isset($to)){
                if (RainFunctions::checkDate($from) && RainFunctions::checkDate($to) && RainFunctions::dateComparison($from, $to)) {
                    $results['Temperature_By_Station'] = TemperatureFunctions::NamibiaStation($location, $stationtype, $interval, $from, $to);
                } else {
                    return response()->json([
                        'message' => 'Invalid date format',
                    ], 400);
                }
            } else {
                if (RainFunctions::checkDate($from)) {
                    $results['Temperature_By_Station'] = TemperatureFunctions::NamibiaStation($location, $stationtype, $interval, $from, $to);
                } else {
                    return response()->json([
                        'message' => 'Invalid date format',
                    ], 400);
                }
            }
        } elseif (strtolower($location) == 'zambia' || strtolower($location) == 'botswana' || strtolower($location) == 'angola' || strtolower($location) == 'south africa'){
            if (isset($to)){
                if (RainFunctions::checkDate($from) && RainFunctions::checkDate($to) && RainFunctions::dateComparison($from, $to)) {
                    $results['Temperature_By_Station'] = TemperatureFunctions::NamibiaStation($location, $stationtype, $interval, $from, $to);
                } else {
                    return response()->json([
                        'message' => 'Invalid date format',
                    ], 400);
                }
            } else {
                if (RainFunctions::checkDate($from)) {
                    $results['Temperature_By_Station'] = TemperatureFunctions::NamibiaStation($location, $stationtype, $interval, $from, $to);
                } else {
                    return response()->json([
                        'message' => 'Invalid date format',
                    ], 400);
                }
            }
        } else {
            return response()->json([
                'message' => 'Invalid country',
            ], 400);
        }
        if ($results['Temperature_By_Station'] == []) {
            return response()->json([
                'message' => 'No records found',
            ], 404);
        } else {
                return json_encode($results);
        }
    }

    function temperatureByInterval($location, $from, $to, $interval) {
        if (RainFunctions::checkDate($from) && RainFunctions::checkDate($to) && RainFunctions::dateComparison($from, $to)) {
            if(strtolower($location) == 'namibia') {
                switch (strtolower($interval)) {
                    case 'daily':
                        $results['Temperature_By_DateRange'] = DB::select( DB::raw("
                                                            SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                            FROM MCS_DailyData
                                                            RIGHT JOIN All_WeatherStations
                                                            ON fk_Logger_ID = LoggerSerial
                                                            WHERE datum >= :fromDate
                                                            AND datum <= :toDate
                                                                          "),
                                                                array('fromDate' => $from,
                                                                        'toDate' => $to ));
                    break;
                    case 'monthly':
                        $results['Temperature_By_DateRange'] = DB::select( DB::raw("
                                            SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                            FROM MCS_MonthlyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE datum >= :fromDate
                                            AND datum <= :toDate
                                                          "),
                                                array('fromDate' => $from,
                                                        'toDate' => $to ));
                    break;
                    case 'hourly':
                        $results['Temperature_By_DateRange'] = DB::select( DB::raw("
                                                                SELECT datum as date, temp_ambient, soiltempave, latitude, longitude, stationname, country
                                                                FROM MCS_HourlyData
                                                                RIGHT JOIN All_WeatherStations
                                                                ON fk_Logger_ID = LoggerSerial
                                                                WHERE datum >= :fromDate
                                                                AND datum <= :toDate
                                                                              "),
                                                                    array('fromDate' => $from,
                                                                            'toDate' => $to ));
                    break;
                    default:
                    return response()->json([
                        'message' => 'Invalid interval',
                    ], 400);
                    break;
                }
                if ($results['Temperature_By_DateRange'] == []) {
                    return response()->json([
                        'message' => 'No records found',
                    ], 404);
                } else {
                    return json_encode($results);
                }
            } elseif (strtolower($location) == 'zambia' || strtolower($location) == 'botswana' || strtolower($location) == 'angola' || strtolower($location) == 'south africa'){
                switch (strtolower($interval)) {
                    case 'monthly':
                        $results['Temperature_By_DateRange'] = DB::select( DB::raw("
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
                    break;
                    case 'daily':
                        $results['Temperature_By_DateRange'] = DB::select( DB::raw("
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
                                                            dd($results);
                    break;
                    case 'hourly':
                        $results['Temperature_By_DateRange'] = DB::select( DB::raw("
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
                    break;
                    default:
                    return response()->json([
                        'message' => 'Invalid interval',
                    ], 400);
                    break;
                }
                if ($results['Temperature_By_DateRange'] == []) {
                    return response()->json([
                        'message' => 'No records found',
                    ], 404);
                } else {
                    return json_encode($results);
                }
            } else {
                return response()->json([
                    'message' => 'Invalid country',
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Invalid date format',
            ], 400);
        }
    }

    function temperatureYearMonth($location, $year = null, $month=null) {

            if(strtolower($location) == 'zambia' || strtolower($location) == 'botswana' || strtolower($location) == 'angola' || strtolower($location) == 'south africa') {
                if ($year == null && $month == null) {
                    $month = date('m', strtotime('-1 months'));
                    $year = date('Y');

                    $results['Temperature_By_YearMonth'] = DB::select( DB::raw("
                                            SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                            FROM Typ1_DailyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE YEAR(datum) = :year
                                            AND MONTH(datum) = :month
                                            AND country = :location
                                              "),
                                    array( 'location' => $location,
                                            'month' => $month,
                                            'year' => $year ));

                } elseif ($month == null && isset($year)) {
                    if (RainFunctions::checkYear($year)) {
                        $results['Temperature_By_YearMonth'] = DB::select( DB::raw("
                                                SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                FROM Typ1_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE YEAR(datum) = :year
                                                AND country = :location
                                                  "),
                                        array( 'location' => $location,
                                                'year' => $year ));
                    } else {
                        return response()->json([
                            'message' => 'Invalid year',
                        ], 400);
                    }
                } elseif (isset($year) && isset($month)) {
                        if (RainFunctions::checkYear($year) && RainFunctions::checkMonth($month)) {
                            $results['Temperature_By_YearMonth'] = DB::select( DB::raw("
                                                    SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                    FROM Typ1_DailyData
                                                    RIGHT JOIN All_WeatherStations
                                                    ON fk_Logger_ID = LoggerSerial
                                                    WHERE YEAR(datum) = :year
                                                    AND MONTH(datum) = :month
                                                    AND country = :location
                                                      "),
                                            array( 'location' => $location,
                                                    'month' => $month,
                                                    'year' => $year ));

                        } else {
                            return response()->json([
                                'message' => 'Invalid year or month',
                            ], 400);
                    }
                }
            } elseif (strtolower($location) == 'namibia' ) {
            if ($year == null && $month == null){

                $month = date('m', strtotime('-1 months'));
                $year = date('Y');

                $results['Temperature_By_YearMonth'] = DB::select( DB::raw("
                                        SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                        FROM MCS_DailyData
                                        RIGHT JOIN All_WeatherStations
                                        ON fk_Logger_ID = LoggerSerial
                                        WHERE YEAR(datum) = :year
                                        AND datum = :month
                                        AND country = :location
                                          "),
                                array( 'location' => $location,
                                        'month' => $month,
                                        'year' => $year ));
            } elseif ($month == null && isset($year)) {
                if (RainFunctions::checkYear($year)) {
                    $results['Temperature_By_YearMonth'] = DB::select( DB::raw("
                                            SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                            FROM MCS_DailyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE YEAR(datum) = :year
                                            AND country = :location
                                              "),
                                    array( 'location' => $location,
                                            'year' => $year ));
                } else {
                    return response()->json([
                        'message' => 'Invalid year',
                    ], 400);
                }
            } elseif (isset($year) && isset($month)) {
                    if (RainFunctions::checkYear($year) && RainFunctions::checkMonth($month)) {
                            $results['Temperature_By_YearMonth'] = DB::select( DB::raw("
                                                    SELECT datum as date, ambienttempmin, ambienttempmax, ambienttempavg, latitude, longitude, stationname, country
                                                    FROM MCS_DailyData
                                                    RIGHT JOIN All_WeatherStations
                                                    ON fk_Logger_ID = LoggerSerial
                                                    WHERE YEAR(datum) = :year
                                                    AND MONTH(datum) = :month
                                                    AND country = :location
                                                      "),
                                            array( 'location' => $location,
                                                    'month' => $month,
                                                    'year' => $year ));
                        } else {
                            return response()->json([
                                'message' => 'Invalid year or month',
                            ], 400);
                        }

                    } else {
                            return response()->json([
                                'message' => 'Invalid country',
                            ], 400);
                    }
                }
                    if ($results['Temperature_By_YearMonth'] == []) {
                        return response()->json([
                            'message' => 'No records found',
                        ], 404);
                    } else {
                        return json_encode($results);
                    }
        }
}
