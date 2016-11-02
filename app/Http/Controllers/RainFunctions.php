<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
class RainFunctions extends Controller
{
    //All small/simple functions are contained here
    //checks supplied date to determine if it is a number and is less than 31
    public static function checkMonth ($month) {
        if ($month <= 12 && $month > 0) {
            return true;
        }
    }
    //check if year is properly formed
    public static function checkYear ($year) {
        if(preg_match("/^(19|20)\d{2}$/", $year))
        {
            return true;
        }
    }
    //check date format
    public static function checkDate($date) {
        if(preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches))
         {
          if(checkdate($matches[2], $matches[3], $matches[1]))
           {
            return true;
           }
       } elseif (preg_match("/^(\d{4})-(\d{2})$/", $date, $matches)){
           return true;
       }
    }
    //check if from date is not after to date
    public static function dateComparison ($fromDate, $toDate) {
        if(strtotime($fromDate) <= strtotime($toDate)){
            return true;
        }
    }

    //get records with interval
    public static function getInterval($location, $fromDate, $toDate, $interval) {
        if(strtolower($location) == 'namibia'){
            if(strtolower($interval) == 'daily'){
                $results['Rain_By_Interval'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                FROM MCS_DailyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Datum >= :fromDate
                                                AND Datum <= :toDate
                                                AND country = :location
                                                ORDER BY Datum"),
                                                array( 'location' => $location,
                                                        'fromDate' => $fromDate,
                                                        'toDate' => $toDate ));
            }else if(strtolower($interval) == 'monthly'){
                $results['Rain_By_Interval'] = DB::select( DB::raw("SELECT Monat as date, rain, stationname, latitude, longitude, country
                                                FROM MCS_MonthlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Monat >= :fromDate
                                                AND Monat <= :toDate
                                                AND  country = :location
                                                ORDER BY Monat"),
                                                array( 'location' => $location,
                                                        'fromDate' => $fromDate,
                                                        'toDate' => $toDate ));
            } else if(strtolower($interval) == 'hourly') {
                $results['Rain_By_Interval'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                FROM MCS_HourlyData
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
                        'message' => 'Invalid interval',
                    ], 400);
            }
            if($results['Rain_By_Interval'] == []){
                return response()->json([
                        'message' => 'No records found',
                    ], 404);
            } else {
                return json_encode($results);
            }
        } else {
            if(strtolower($interval) == 'daily'){
                $results['Rain_By_Interval'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname,latitude, longitude, country
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
            }else if(strtolower($interval) == 'monthly'){
                $results['Rain_By_Interval'] = DB::select( DB::raw("SELECT Monat as date, rain, stationname, latitude, longitude, country
                                                FROM Typ1_MonthlyData
                                                RIGHT JOIN All_WeatherStations
                                                ON fk_Logger_ID = LoggerSerial
                                                WHERE Monat >= :fromDate
                                                AND Monat <= :toDate
                                                AND  country = :location
                                                ORDER BY Monat"),
                                                array( 'location' => $location,
                                                        'fromDate' => $fromDate,
                                                        'toDate' => $toDate ));
            } else if(strtolower($interval) == 'hourly') {
                $results['Rain_By_Interval'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                FROM Typ1_HourlyData
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
                        'message' => 'Invalid interval',
                    ], 400);
            }
            if($results['Rain_By_Interval'] == []){
                return response()->json([
                        'message' => 'No records found',
                    ], 404);
            } else {
                return json_encode($results);
            }
        }

    }

    //get records by station type
    public static function getRainByStation($location, $stationtype, $interval, $fromDate, $toDate) {

        if(strtolower($interval) == 'daily'){
            switch (strtolower($stationtype)) {
                case 'mcs':
                    if(strtolower($location) == 'namibia')
                        {
                            if (isset($toDate)) {
                            $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Datum AS date, rain, stationname, latitude, longitude, country
                                                                                FROM MCS_DailyData
															                    RIGHT JOIN All_WeatherStations
												                                ON LoggerSerial = fk_Logger_ID
													                            WHERE Datum >= :dateFrom
													                            AND Datum <= :dateTo"),
                                                        array(
                                                                'dateFrom' => $fromDate,
                                                                'dateTo' => $toDate));

                        } else {
                            $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Datum AS date, rain, stationname, latitude, longitude, country
                                                                                FROM MCS_DailyData
                                                                                RIGHT JOIN All_WeatherStations
                                                                                ON LoggerSerial = fk_Logger_ID
                                                                                WHERE Datum = :fromDate"),
                                                        array('fromDate' => $fromDate ));
                        }
                    } else {
                        return response()->json([
                                'message' => 'MCS Station only available in Namibia',
                            ], 400);
                    }
                    break;
                case 'typ1':
                    if (isset($toDate)) {
                        $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                        FROM Typ1_DailyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON LoggerSerial = fk_Logger_ID
                                                        WHERE Datum >= :dateFrom
                                                        AND Datum <= :dateTo
                                                        AND fk_Logger_ID IN (SELECT LoggerSerial FROM All_WeatherStations WHERE country = :location)
                                                        "),
                                                        array( 'location' => $location,
                                                                'dateFrom' => $fromDate,
                                                                'dateTo' => $toDate ));
                    } else {
                        $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Datum AS date, rain, stationname, latitude, longitude, country
                                                        FROM Typ1_DailyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON LoggerSerial = fk_Logger_ID
                                                        WHERE Datum = :dateFrom
                                                        AND fk_Logger_ID IN (SELECT LoggerSerial FROM All_WeatherStations WHERE country = :location)
                                                        "),
                                                        array( 'location' => $location,
                                                                'dateFrom' => $fromDate));
                    }
                    break;
                default:
                    return response()->json([
                            'message' => 'Invalid station type',
                        ], 400);
                    break;
            }
        } else if(strtolower($interval) == 'monthly'){
            switch (strtolower($stationtype)) {
                case 'mcs':
                        if (isset($toDate)) {
                            $fromDate = date('Y-m', strtotime($fromDate));

                            $toDate = date('Y-m', strtotime($toDate));
                            $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Monat as month, rain, stationname, latitude, longitude, country
                                                                                FROM MCS_MonthlyData
                                                                                RIGHT JOIN All_WeatherStations
                                                                                ON fk_Logger_ID = LoggerSerial
                                                                                WHERE Monat >= :dateFrom
                                                                                AND Monat <= :dateTo
                                                                                AND fk_Logger_ID IN (SELECT LoggerSerial FROM All_WeatherStations WHERE country = :location)
                                                                                "),
                                                        array( 'location' => $location,
                                                                'dateFrom' => $fromDate,
                                                                'dateTo' => $toDate ));
                        } else {
                            $fromDate = date('Y-m', strtotime($fromDate));
                            $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Monat as month, rain, stationname, latitude, longitude, country
                                                                                FROM MCS_MonthlyData
                                                                                RIGHT JOIN All_WeatherStations
                                                                                ON fk_Logger_ID = LoggerSerial
                                                        WHERE Monat = :dateFrom
                                                        AND fk_Logger_ID IN (SELECT LoggerSerial FROM All_WeatherStations WHERE country = :location)
                                                        "),
                                                        array( 'location' => $location,
                                                                'dateFrom' => $fromDate));
                        }
                    break;
                case 'typ1':
                        if (isset($toDate)) {
                            $fromDate = date('Y-m', strtotime($fromDate));
                            $toDate = date('Y-m', strtotime($toDate));
                            $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Monat as date, rain, stationname, latitude, longitude, country
                                                        FROM Typ1_MonthlyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON fk_Logger_ID = LoggerSerial
                                                        WHERE Monat >= :dateFrom
                                                        AND Monat <= :dateTo
                                                        AND fk_Logger_ID IN (SELECT LoggerSerial FROM All_WeatherStations WHERE country = :location)
                                                        "),
                                                        array( 'location' => $location,
                                                                'dateFrom' => $fromDate,
                                                                'dateTo' => $toDate ));
                        } else {
                            $fromDate = date('Y-m', strtotime($fromDate));
                            $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Monat as date, rain, stationname, latitude, longitude, country
                                                            FROM Typ1_MonthlyData
                                                            RIGHT JOIN All_WeatherStations
                                                            ON fk_Logger_ID = LoggerSerial
                                                            WHERE Monat = :dateFrom
                                                            AND country = :location
                                                            "),
                                                            array( 'location' => $location,
                                                                    'dateFrom' => $fromDate));
                        }
                    break;
                default:
                    return response()->json([
                            'message' => 'Invalid station type',
                        ], 400);
                    break;
            }
        } else if(strtolower($interval) == 'hourly'){
            switch (strtolower($stationtype)) {
                case 'mcs':
                        if (isset($toDate)) {
                            $results['Rain_By_Station'] = $results = DB::select( DB::raw("SELECT Datum as date, hour, rain, stationname, latitude, longitude, country
                                                            FROM MCS_HourlyData
                                                            RIGHT JOIN All_WeatherStations
                                                            ON fk_Logger_ID = LoggerSerial
                                                            WHERE Datum >= :dateFrom
                                                            AND Datum <= :dateTo
                                                            AND country = :location"
                                                        ),
                                                            array( 'location' => $location,
                                                                    'dateFrom' => $fromDate,
                                                                    'dateTo' => $toDate ));
                        } else {
                            $results['Rain_By_Station'] = $results = DB::select( DB::raw("SELECT Datum as date, hour, rain, stationname, latitude, longitude, country
                                                            FROM MCS_HourlyData
                                                            RIGHT JOIN All_WeatherStations
                                                            ON fk_Logger_ID = LoggerSerial
                                                            WHERE Datum = :dateFrom
                                                            AND country = :location"
                                                        ),
                                                            array( 'location' => $location,
                                                                    'dateFrom' => $fromDate));
                        }
                    break;
                case 'typ1':
                        if (isset($toDate)) {
                            if (strtolower($location) == 'angola') {
                                $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Datum as date, rain, hour, minute, stationname, country
                                                                                    FROM AO_HourlyData
                                                                                    RIGHT JOIN All_WeatherStations
                                                                                    ON LoggerSerial = fk_Logger_ID
                                                                                    WHERE Datum >= :dateFrom
                                                                                    AND Datum <= :dateTo
                                                                                    AND country = :location
                                                                "),
                                                                array( 'location' => $location,
                                                                        'dateFrom' => $fromDate,
                                                                        'dateTo' => $toDate ));
                            } else {
                                $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Datum as date, rain,hour, minute, stationname, latitude, longitude, country
                                                                FROM Typ1_HourlyData
                                                                RIGHT JOIN All_WeatherStations
                                                                ON LoggerSerial = fk_Logger_ID
                                                                WHERE Datum >= :dateFrom
                                                                AND Datum <= :dateTo
                                                                AND country = :location
                                                                "),
                                                                array( 'location' => $location,
                                                                        'dateFrom' => $fromDate,
                                                                        'dateTo' => $toDate ));
                            }
                        } else {
                            if (strtolower($location) == 'angola'){
                                $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Datum as date, rain, hour, minute, stationname,latitude, longitude, country
                                                                                    FROM AO_HourlyData
                                                                                    RIGHT JOIN All_WeatherStations
                                                                                    ON LoggerSerial = fk_Logger_ID
                                                                                    WHERE Datum >= :dateFrom
                                                                                    AND country = :location
                                                                "),
                                                                array( 'location' => $location,
                                                                        'dateFrom' => $fromDate ));
                            } else {
                                $results['Rain_By_Station'] = DB::select( DB::raw("SELECT Datum as date, rain, stationname,latitude, longitude, country
                                                                FROM Typ1_HourlyData
                                                                RIGHT JOIN All_WeatherStations
                                                                ON LoggerSerial = fk_Logger_ID
                                                                WHERE Datum = :dateFrom
                                                                AND  country = :location"),
                                                                array( 'location' => $location,
                                                                        'dateFrom' => $fromDate));}
                        }
                    break;
                default:
                    return response()->json([
                            'message' => 'Invalid station type',
                        ], 400);
                    break;
            }
        } else {
            return response()->json([
                    'message' => 'Invalid interval',
                ], 400);
        }
        if($results['Rain_By_Station'] == []){
            return response()->json([
                    'message' => 'No records found',
                ], 404);
        } else {
            return json_encode($results);
        }
    }

    public static function getOther($location, $year, $month){
        if (isset($month)) {
            $results = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                        FROM Typ1_DailyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON fk_Logger_ID = LoggerSerial
                                                        WHERE YEAR(DATUM) = :year
                                                        AND MONTH(DATUM) = :month
                                                        AND Country = :location"),
                                                        array(  'month' => $month,
                                                                'year' => $year,
                                                                'location' => $location
                                                            ));
        } else {
            $results = DB::select( DB::raw("SELECT Datum as date, rain, stationname, latitude, longitude, country
                                                        FROM Typ1_DailyData
                                                        RIGHT JOIN All_WeatherStations
                                                        ON fk_Logger_ID = LoggerSerial
                                                        WHERE YEAR(DATUM) = :year
                                                        AND Country = :location"),
                                                        array('year' => $year,
                                                            'location' => $location));
        }
        return $results;
    }
}
