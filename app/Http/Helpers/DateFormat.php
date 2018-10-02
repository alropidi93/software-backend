<?php

namespace App\Http\Helpers;
use Illuminate\Support\Collection;
use Carbon\Carbon;



class DateFormat
{

  public static function spanishDateToEnglishDate( $date ){
    // $date es un string en el siguiente formato dd/mm/aaaa
    return date('Y-m-d',strtotime($date));
  }

  /*Convert a string representation of a date ("yyyymmdd") to a datetime type using Carbon Library */
  public static function stringDateToDateTimeType( $dateString ){

    $dateTemp = $dateString;
    $year = intdiv($dateTemp,10000);
    $month = intdiv($dateTemp%1000,100);
    $day = $dateTemp%100; 
    return Carbon::createFromDate($year, $month, $day);
  }

  public static function datetimeStringToDate_dmY($datetimeString){
    if ($datetimeString==null){
      return null;
    }
    return Carbon::parse($datetimeString)->format("d/m/Y");

  }

}
