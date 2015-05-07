<?php

class Format {

    public static function DateFromTo( $mysql_date, $toFormat='d.m.Y H:i', $fromFormat='Y-m-d H:i:s' )
    {
        $date = \DateTime::createFromFormat($fromFormat, $mysql_date);
        $res = date( $toFormat, $date->getTimestamp() );

        return $res;
    }
    /*
     * $mysql_date from mysql -> Y-m-d H:i:s
     * return array( d.m.Y, H:i)
     * */
    public static function SqlDateTo( $mysql_date )
    {
        $date_time = explode(" ", $mysql_date);
        $date = explode("-", $date_time[0]);

        return array(
            'date' => $date[2] . '.' . $date[1] . '.' . $date[0] ,
            'time' => $date_time[1]
        );
    }

    /*
     * 2014-12-31 16:00 +120 min
     *
     * return 16:00-18:00
     * */
    public static function TimePeriod( $mysql_date, $min )
    {
        //date_create_from_format
        $date1 = \DateTime::createFromFormat('Y-m-d H:i:s', $mysql_date);
        $time_1 = date('H:i', $date1->getTimestamp());

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $mysql_date);
        $timestamp = $date->getTimestamp() + $min * 60;
        $time_2 = date('H:i', $timestamp);

        return $time_1 . ' - ' .$time_2;
    }

}