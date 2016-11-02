<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use DB;
class RainController extends Controller
{
    //get rain for location by year(optional), month(optional)
    public function rainYearMonth ($location, $year = null, $month = null) {
        if (strtolower($location) == 'namibia') {
            if (!isset($year) && !isset($month)) { //if both year and month are not specified
                $year = date('Y');
                $month = date('m', strtotime("-1 month"));

                $results['Rain_by_Year'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname,latitude, longitude, country
                                                                FROM MCS_DailyData
                                                                RIGHT JOIN All_WeatherStations
                                                                ON fk_Logger_ID = LoggerSerial
                                                                WHERE YEAR(DATUM) = :year
                                                                AND country = :location
                                                                ORDER BY Datum"),
                                array('location' => $location,
                                        'year' => $year));

            }  elseif (isset($year) && !isset($month)) { //if year is specified and month nor specified

                if(RainFunctions::checkYear($year)) {
                    $results['Rain_by_Year'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                                    FROM MCS_DailyData
                                                                    RIGHT JOIN All_WeatherStations
                                                                    ON fk_Logger_ID = LoggerSerial
                                                                    WHERE YEAR(DATUM) = :year
                                                                    AND  country = :location
                                                                    ORDER BY Datum"),
                                                    array( 'location' => $location,
                                                            'year' => $year));
                } else {
                    return response()->json([
                            'message' => 'Invalid year',
                        ], 400);
                }
            } elseif (isset($year) && isset($month)) {

                if (RainFunctions::checkYear($year) && RainFunctions::checkMonth($month)) {
                    $results['Rain_by_Year'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                    FROM MCS_DailyData
                                                    RIGHT JOIN All_WeatherStations
                                                    ON fk_Logger_ID = LoggerSerial
                                                    WHERE YEAR(DATUM) = :year
                                                    AND MONTH(DATUM) = :month
                                                    AND country = :location
                                                    ORDER BY Datum"),
                                                    array( 'location' => $location,
                                                            'month' => $month,
                                                            'year' => $year));
                }
            }

            if ($results['Rain_by_Year'] == []) {
                return response()->json([
                        'message' => 'Records not found',
                        ], 404);
                } else {
                return json_encode($results);
            }
        } else {
            if(strtolower($location) == 'angola'  || strtolower($location) == 'zambia' || strtolower($location) == 'south africa' || strtolower($location) == 'botswana') {
                if (!isset($year) && !isset($month)){
                    $year = date('Y');
                    $month = date('m', strtotime('-1 month'));
                    $results['Rain_by_Year'] = RainFunctions::getOther($location, $year, $month);
                } elseif (isset($year) && !isset($month)){
                    if (RainFunctions::checkYear($year)) {
                        $results['Rain_by_Year'] = RainFunctions::getOther($location, $year, $month);
                    } else {
                        return response()->json([
                            'message' => 'Invalid year',
                        ], 400);
                    }
                } elseif (isset($year) && isset($month)){
                    if (RainFunctions::checkYear($year) && RainFunctions::checkMonth($month)){
                        $results['Rain_by_Year'] = RainFunctions::getOther($location, $year, $month);
                    } else {
                        return response()->json([
                            'message' => 'Invalid year or month',
                        ], 400);
                    }
                }

                if ($results['Rain_by_Year'] == []){
                    return response()->json([
                        'message' => 'No records found',
                    ], 404);
                } else {
                    return json_encode($results);
                }

            } else {
                return response()->json([
                    'message' => 'Invalid or missing country',
                ], 400);
            }
        }
    }
    //get rain for location by exact date or two date intervals, toDate is optional
    public function rainByDate($location, $fromDate, $toDate = null) {
        if (strtolower($location) == 'namibia')  {
            if (isset($toDate)) {
                if(RainFunctions::checkDate($fromDate) && RainFunctions::checkDate($toDate)) {
                    if (RainFunctions::dateComparison($fromDate, $toDate)) {
                        $results['Rain_By_Date'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                FROM MCS_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Datum >= :fromDate
                                                AND Datum <= :toDate
                                                AND  country = :location
                                                ORDER BY Datum"),
                                                array( 'location' => $location,
                                                        'fromDate' => $fromDate,
                                                        'toDate' => $toDate ));
                    } else {
                        return response()->json([
                                'message' => 'From date greater than to date',
                            ], 400);
                    }
                } else {
                    return response()->json([
                            'message' => 'Invalid date format',
                        ], 400);
                }
            } else {
                if(RainFunctions::checkDate($fromDate)) {
                    $results['Rain_By_Date'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                            FROM MCS_DailyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Datum = :fromDate
                                            AND country = :location
                                            ORDER BY Datum"),
                                            array( 'location' => $location,
                                                    'fromDate' => $fromDate
                                            ));
                } else {
                    return response()->json([
                            'message' => 'Invalid date format',
                        ], 400);
                }
            }
            if ($results['Rain_By_Date'] == []) {
                return response()->json([
                        'message' => 'Records not found',
                        ], 404);
                } else {
                return json_encode($results);
            }
        } elseif (strtolower($location) == 'zambia' || strtolower($location) == 'angola' || strtolower($location) == 'south africa' || strtolower($location) == 'botswana'){
            if (isset($toDate)) {
                if(RainFunctions::checkDate($fromDate) && RainFunctions::checkDate($toDate)) {
                    if (RainFunctions::dateComparison($fromDate, $toDate)) {
                        $results['Rain_By_Date'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                FROM Typ1_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Datum >= :fromDate
                                                AND Datum <= :toDate
                                                AND country = :location
                                                ORDER BY Datum"),
                                                array( 'location' => $location,
                                                        'fromDate' => $fromDate,
                                                        'toDate' => $toDate ));
                    } else {
                        return response()->json([
                                'message' => 'From date greater than to date',
                            ], 400);
                    }
                } else {
                    return response()->json([
                            'message' => 'Invalid date format',
                        ], 400);
                }
            } else {
                if(RainFunctions::checkDate($fromDate)) {
                    $results['Rain_By_Date'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                            FROM Typ1_DailyData
                                            RIGHT JOIN All_WeatherStations
                                            ON fk_Logger_ID = LoggerSerial
                                            WHERE Datum = :fromDate
                                            AND country = :location
                                            ORDER BY Datum"),
                                            array( 'location' => $location,
                                                    'fromDate' => $fromDate
                                            ));
                } else {
                    return response()->json([
                            'message' => 'Invalid date format',
                        ], 400);
                }
            }
            if ($results['Rain_By_Date'] == []) {
                return response()->json([
                        'message' => 'Records not found',
                        ], 404);
                } else {
                return json_encode($results);
            }
        } else {
            return response()->json([
                    'message' => 'Invalid or missing country',
                ], 400);
        }
    }
    //get rain for location by mandatory period/range choose whether to get monthly/daily Records
    public function rainByInterval($location, $fromDate, $toDate, $interval) {
        if (strtolower($location) == 'namibia' || strtolower($location) == 'zambia' || strtolower($location) == 'angola' || strtolower($location) == 'south africa' || strtolower($location) == 'botswana') {

            if (RainFunctions::checkDate($fromDate) && RainFunctions::checkDate($toDate) && RainFunctions::dateComparison($fromDate, $toDate)) {
                    $results = RainFunctions::getInterval($location, $fromDate, $toDate, $interval);
            } else {
                return response()->json([
                        'message' => 'Invalid date',
                    ], 400);
            }
        } else {
            return response()->json([
                    'message' => 'Invalid or missing country',
                ], 400);
        }
        return $results;
    }
    //get rain for location by stationtype with specific range and specify monthly/daily
    public function rainByStationType($location, $stationtype, $interval, $fromDate, $toDate = null) {

        if (strcmp(strtolower($location), 'namibia') || strcmp(strtolower($location), 'zambia') || strcmp(strtolower($location), 'angola') || strcmp(strtolower($location), 'south africa') || strcmp(strtolower($location), 'botswana')) {

            if($toDate != null) {
                if (RainFunctions::checkDate($fromDate) && RainFunctions::checkDate($toDate) && RainFunctions::dateComparison($fromDate, $toDate)) {

                    $results = RainFunctions::getRainByStation($location, $stationtype, $interval, $fromDate, $toDate);
                    return $results;
                } else {
                    return response()->json([
                            'message' => 'Invalid date format',
                        ], 400);
                }
            } else {

                    $results = RainFunctions::getRainByStation($location, $stationtype, $interval, $fromDate, $toDate);
                    return $results;
            }
        }else{
            return response()->json([
                    'message' => 'Invalid or missing country',
                ], 400);
        }
    }


}
