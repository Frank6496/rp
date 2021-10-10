<?php

class dateformulas{
	
  	
  	function firstDayOf($period, DateTime $date = null)
	{
      $period = strtolower($period);
      $validPeriods = array('year', 'quarter', 'month', 'week');

      if ( ! in_array($period, $validPeriods))
          throw new InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));

      $newDate = ($date === null) ? new DateTime() : clone $date;

      switch ($period) {
          case 'year':
              $newDate->modify('first day of january ' . $newDate->format('Y'));
              break;
          case 'quarter':
              $month = $newDate->format('n') ;

              if ($month < 4) {
                  $newDate->modify('first day of january ' . $newDate->format('Y'));
              } elseif ($month > 3 && $month < 7) {
                  $newDate->modify('first day of april ' . $newDate->format('Y'));
              } elseif ($month > 6 && $month < 10) {
                  $newDate->modify('first day of july ' . $newDate->format('Y'));
              } elseif ($month > 9) {
                  $newDate->modify('first day of october ' . $newDate->format('Y'));
              }
              break;
          case 'month':
              $newDate->modify('first day of this month');
              break;
          case 'week':
              $newDate->modify(($newDate->format('w') === '0') ? 'monday last week' : 'monday this week');
              break;
      }

      return $newDate;
	}
  
  	function lastDayOf($period, DateTime $date = null)
	{
    $period = strtolower($period);
    $validPeriods = array('year', 'quarter', 'month', 'week');
 
    if ( ! in_array($period, $validPeriods))
        throw new InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));
 
    $newDate = ($date === null) ? new DateTime() : clone $date;
 
    switch ($period)
    {
        case 'year':
            $newDate->modify('last day of december ' . $newDate->format('Y'));
            break;
        case 'quarter':
            $month = $newDate->format('n') ;
 
            if ($month < 4) {
                $newDate->modify('last day of march ' . $newDate->format('Y'));
            } elseif ($month > 3 && $month < 7) {
                $newDate->modify('last day of june ' . $newDate->format('Y'));
            } elseif ($month > 6 && $month < 10) {
                $newDate->modify('last day of september ' . $newDate->format('Y'));
            } elseif ($month > 9) {
                $newDate->modify('last day of december ' . $newDate->format('Y'));
            }
            break;
        case 'month':
            $newDate->modify('last day of this month');
            break;
        case 'week':
            $newDate->modify(($newDate->format('w') === '0') ? 'now' : 'sunday this week');
            break;
    }
 
    return $newDate;
	}

  private function getDate($param){
  			switch($param){
              case 'CW': return $this->lastDayOf('week', new DateTime());
              case 'CM': return $this->lastDayOf('month', new DateTime());
              case 'CQ': return $this->lastDayOf('quarter', new DateTime());
              case 'CY': return $this->lastDayOf('year', new DateTime());
              default : return new DateTime();
            }
  }
  
  function calcdate($param){
  		$param_array = explode('|',$param);
    	$date = $this->getDate($param_array[0]);
    	for($i=1;$i<count($param_array);$i++){
        	$date->modify($param_array[$i]);
        
        }
    	return $date->format('Y-m-d H:i:s');
  }
  
  function test_calcdate($args){
  		echo $this->calcdate($args[0]);
  }

}


?>