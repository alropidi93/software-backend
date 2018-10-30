<?php

namespace App\Http\Helpers;
use Illuminate\Support\Collection;




class Algorithm{

  public static function quitNullValuesFromCollection($collection){

 
    $collectionFiltered = $collection->filter(function($value, $key) {
       return  $value != null;
    });
    return $collectionFiltered->values();
  }

  public static function quitNullValuesFromArray($array){
    $arrayFiltered = array_filter($array,function($var){
      return !is_null($var);
    });
    return $arrayFiltered;
  }

  public static function codeGeneration(){
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';
    $random_string_length = 7;
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $random_string_length; $i++) {
          $code .= $characters[mt_rand(0, $max)];
    }
    return $code;
    
  }

  public static function getRndIntegerNumber($limit)
  {

    return rand(0,$limit);

  }


}
