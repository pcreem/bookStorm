<?php declare(strict_types=1);

namespace App\Middleware;

class ParseDatetime
{    
    public function parseUserDatetime(string $rawDatetime){
        // $rawDatetime = "12/11/2018 02:33 AM";

        if (strlen($rawDatetime) > 0){
            $time = substr($rawDatetime,11);
            $date = substr($rawDatetime,0,10);
            $year = substr($date,-4);
            $month = substr($date,-10,2);
            $day = substr($date,-7,2);            
            $amOrPm = substr($time,-2);
            $hour = substr($time,0,2);
            $minute = substr($time,3,2);
            if ($amOrPm === 'AM' && $hour === '12'){ $hour = '00';}
            if ($amOrPm === 'PM' && $hour !== '12'){ $hour = strval(intval($hour)+12);}
            $time = $hour . ':' . $minute;
            $datetime = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute;
            
            return $datetime;
        }

        return null;        
    }

    private function splitDayTime(string $rawDatetime): array{
        $pattern = "/[0-9]/";
        preg_match($pattern, $rawDatetime, $matches, PREG_OFFSET_CAPTURE);
        $numStart = $matches[0][1];
        
        $officeDay = substr($rawDatetime, 0, $numStart-1);
        $officeDay = preg_match("/[,-]/",$officeDay) ? preg_split("/[,-]/",$officeDay) : $officeDay;    
        $officeTime = substr($rawDatetime, $numStart);
        $officeTime = explode("-", $officeTime);    
        $officeSet = [$officeDay, $officeTime];
        
        return $officeSet;
    }
    
    private function turnTo24hour(string $hour, string $amOrPm): string{
        if ($amOrPm === 'am' && $hour === '12'){ return '00';}
        if ($amOrPm === 'pm' && $hour !== '12'){ return strval(intval($hour)+12);}
        return $hour;
    }
    
    private function changeTimeFormat(array $officeTime): array{
        $officeTime24Formated = [];
        foreach($officeTime as $str){
            $str = trim($str);
            $amOrPm = substr($str,-2);
            $time = substr($str,0,strlen($str)-3);
            if (preg_match("/:/", $time)){
                $hour = substr($time,0,-3);
                $minute = substr($time,-2,2);
                $hour = $this->turnTo24hour($hour,$amOrPm);
                $time = $hour . ':' . $minute;
            }else{
                $hour = $this->turnTo24hour($time,$amOrPm);
                $time = $hour . ':' . '00';
            }
        
            array_push($officeTime24Formated,$time);
        }
    
        return $officeTime24Formated;
    }


    public function parseStoreDatetime(string $rawDatetime): array
    {
        // $rawDatetime="Mon, Fri 2:30 pm - 8 pm / Tues 11 am - 2 pm / Weds 1:15 pm - 3:15 am / Thurs 10 am - 3:15 am / Sat 5 am - 11:30 am / Sun 10:45 am - 5 pm";
        
        if (strlen($rawDatetime) > 0){
            $rawDatetimeArray=explode("/",$rawDatetime);
            $parsedDatetimeSet = [];

            foreach($rawDatetimeArray as $val){
                $val = trim($val);
                $officeSet = $this->splitDayTime($val);
                $officeDay = $officeSet[0];
                $officeTime = $this->changeTimeFormat($officeSet[1]);
                $dayType = gettype($officeDay);
                $opentime = $officeTime[0];
                $closetime = $officeTime[1];

                if ($dayType === 'array'){
                    foreach ($officeDay as $day){
                        $day = trim($day);
                        $parsedDatetime = [$day,$opentime,$closetime];            
                        array_push($parsedDatetimeSet, $parsedDatetime);
                    }
                }else{
                    $day = trim($officeDay);
                    $parsedDatetime = [$day,$opentime,$closetime];            
                    array_push($parsedDatetimeSet, $parsedDatetime);
                }
            }

            return $parsedDatetimeSet;
        }

        return null;
        
    }

}

?>