<?php

$str = "12/11/2018 02:33 AM";
$time = substr($str,11);
$date = substr($str,0,10);
$year = substr($date,-4);
$month = substr($date,-7,2);
$day = substr($date,-10,2);
$amOrPm = substr($time,-2);
$hour = substr($time,0,2);
$minute = substr($time,3,2);
if ($amOrPm === 'AM' && $hour === '12'){ $hour = '00';}
if ($amOrPm === 'PM' && $hour !== '12'){ $hour = strval(intval($hour)+12);}
$time = $hour . ':' . $minute;
$datetime = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute;
echo $datetime;

?>