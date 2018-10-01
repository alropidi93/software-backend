<?php

namespace App\Http\Helpers;
use Illuminate\Support\Collection;
use Carbon\Carbon;



class ArrayHelper
{

  public static function checkIfArrayIsEmpty( $obj){
    if ( empty($obj)){
      return true;
    }
    return false;
  }

  


}
