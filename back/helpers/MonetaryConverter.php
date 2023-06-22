<?php

class MonetaryConverter {

    public static function centsToDolar($cents)
    {
        $dolar = $cents / 100;
        return number_format($dolar, 2, '.', ',');
    }

    public static function dolarToCents($dolar)
    {
        $cents = str_replace('.', ',', $dolar);
        $cents = floatval($cents) * 100;
        return intval($cents);
    }

}