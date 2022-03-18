<?php
class UtilHelper
{
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

    public static function getExtensionFile($fileName)
    {
        // $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        //return $ext;
        $file = new SplFileInfo($fileName);
        $extension  = $file->getExtension();
        return $extension;
    }


    /**
     * Hack da função readfile. Útil para ler arquivo muito grande
     */
    public static function readfile_chunked($filename, $retbytes=true)
    {
        $chunksize = 1*(1024*1024); // how many bytes per chunk
        $buffer = '';
        $cnt =0;
        // $handle = fopen($filename, 'rb');
        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            if ($retbytes) {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status) {
            return $cnt; // return num. bytes delivered like readfile() does.
        }
        return $status;
    }
}
