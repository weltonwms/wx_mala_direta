<?php
class UtilHelper {

    public static function convertCsvToArray($fileCsv)
    {
        $array = array_map('str_getcsv', file($fileCsv));
        array_walk($array, function (&$a) use ($array) {
            $a = array_combine($array[0], $a);
        });
        array_shift($array); # remove column header
        return $array;
    }

    public function convertCsvToJson($fileCsv)
    {
        $array = array_map('str_getcsv', file($fileCsv));
        array_walk($array, function (&$a) use ($array) {
            $a = array_combine($array[0], $a);
        });
        array_shift($array); # remove column header
        $json = json_encode($array);
        return $json;
    }


    public static function getCabecalhoCsv($fileCsv)
    {
       // echo "<pre>"; print_r($fileCsv); exit();
        $firstRow=file($fileCsv)[0];
        $cabecalhoCsv=str_getcsv($firstRow);
        return $cabecalhoCsv;

    }

    public static function dateBr($dateUs)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateUs);
        return $date->format('d.m.Y H:i');
    }

    public static function teste(){
        return "testando o teste";
    }
}