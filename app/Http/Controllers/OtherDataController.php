<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\RainFunctions;

use DB;
class OtherDataController extends Controller
{
    //
    public function otherDataByDates ($location, $from, $to = null) {
        if (!is_null($to)){
            if (RainFunctions::checkDate($from) && RainFunctions::checkDate($to) && RainFunctions::dateComparison($from, $to)){
                $results['other_By_Dates'] = OtherDataFunctions::LocationFromTo($location, $from, $to);
            } else {
                return response()->json([
                    'message' => 'Invalid date format',
                ], 400);
            }
        } else {
            if (RainFunctions::checkDate($from)){
                $results['other_By_Dates'] = OtherDataFunctions::LocationFrom($location, $from);
            } else {
                return response()->json([
                    'message' => 'Invalid date format',
                ], 400);
            }
        }

        if ($results['other_By_Dates'] == []){
            return results()->json([
                'message' => 'No records found'
            ], 404);
        } else {
            return json_encode($results);
        }
    }
    public function otherDataByStation ($location, $stationtype, $interval, $from, $to = null) {
        if (!is_null($to)) {

            if (RainFunctions::checkDate($from) && RainFunctions::checkDate($to)) {
                if (RainFunctions::dateComparison($from, $to)) {
                    $results['other_By_Station'] = OtherDataFunctions::GetResultsStationType($location, $stationtype, $interval, $from, $to);
                } else {
                    return response()->json([
                        'message' => 'From date must come before to date',
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'Invalid date format',
                ], 400);
            }
        } else {
            if (RainFunctions::checkDate($from)) {
                    $results['other_By_Station'] = OtherDataFunctions::GetResultsStationType($location, $stationtype, $interval, $from, $to);
            } else {
                return response()->json([
                    'message' => 'Invalid date format',
                ], 400);
            }
        }
        if ($results['other_By_Station'] == []) {
            return response()->json([
                'message' => 'Records not found',
            ], 404);
        } else {
            return json_encode($results);
        }
    }
    public function otherDataByInterval ($location, $from, $to, $interval) {
        if(RainFunctions::checkDate($from) && RainFunctions::checkDate($to) && RainFunctions::dateComparison($from, $to)){
            $results['other_By_DateRange'] = OtherDataFunctions::IntervalsNoStations($location, $interval, $from, $to);
        } else {
            return response()->json([
                'message' => 'Invalid date format',
            ], 400);
        }
        if ($results['other_By_DateRange'] == []) {
            return response()->json([
                'message' => 'Records not found',
            ], 404);
        } else {
        return json_encode($results);
        }
    }
    public function otherDataYearMonth ($location, $year=null, $month=null) {
        if(is_null($year) && is_null($month)) {
            $year = date('Y');
            $month = date('m', strtotime("-1 month"));
            $results['other_By_YearMonth'] = OtherDataFunctions::GetResultsYearMonth ($location, $year, $month);

        } elseif (isset($year) && is_null($month)) {
            if (RainFunctions::checkYear($year)) {
                $results['other_By_YearMonth'] = OtherDataFunctions::GetResultsYearMonth ($location, $year, $month);

            } else {
                return response()->json([
                    'message' => 'Invalid year',
                ], 400);
            }
        } elseif (isset($year) && isset($month)) {
            if (RainFunctions::checkMonth($month) && RainFunctions::checkYear($year)) {
                    $results['other_By_YearMonth'] = OtherDataFunctions::GetResultsYearMonth ($location, $year, $month);
            } else {
                return response()->json([
                    'message' => 'Invalid year or month',
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Year or month missing from request',
            ], 400);
        }
        if ($results['other_By_YearMonth'] == []) {
            return response()->json([
                'message' => 'Records not found',
            ], 404);
        } else {
        return json_encode($results);
        }
    }
}
