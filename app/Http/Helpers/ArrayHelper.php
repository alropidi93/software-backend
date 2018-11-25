<?php

namespace App\Http\Helpers;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ArrayHelper
{

  public static function checkIfArrayIsEmpty( $obj){
    if ( empty($obj)){
      return true;
    }
    return false;
  }

  public static function isArray($possibleArray){
    return gettype($possibleArray)=='array';
  }

  public static function isIntNumericalArray($array){
    $test = implode('',$array);
    $tmp = (int) $test;
    if($tmp == $test)
        return true;
    else
        return false;
  }

  


}
