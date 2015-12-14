<?php

class DateChecker
{

	private $_from_DT;
	private $_to_DT;

	private $_format = 'Y-m-d H:i:s';

	public static $DIFF_TYPES = array('seconds', 'minutes', 'hours', 'days', 'weeks', 'weekdays', 'compWeeks', 'months', 'years');
 
	public function __construct($args) {

		// the timeFrom and timeTo args are already DateTime values thanks to Silex, but they are in UTC.
		// We should convert them to the timezone the user has selected 

		// check that timeFrom and timeTo are valid DateTime objects
		if (!($args['timeFrom'] instanceof DateTime)) {
		  	throw new InvalidArgumentException('timeFrom was not a valid DateTime instance. Input was: ' . $args['timeFrom']);
		} 
		if (!($args['timeTo'] instanceof DateTime)) {
		  	throw new InvalidArgumentException('timeFrom was not a valid DateTime instance. Input was: ' . $args['timeFrom']);
		} 

		// Make sure the time zones are valid too
		$zones = DateTimeZone::listIdentifiers();

		if (! in_array( $args['timeFromZone'], $zones ) ) {
			throw new InvalidArgumentException('fromZone should be a valid DateTimeZone. Input was: ' . $args['timeFromZone']);	
		}

		if (! in_array( $args['timeToZone'], $zones ) ) {
			throw new InvalidArgumentException('toZone should be a valid DateTimeZone. Input was: ' . $args['timeToZone']);	
		}
		 
		// $this->_from_DT = $args['timeFrom']->setTimezone(new DateTimeZone( $args['timeFromZone'] ) );
		// $this->_to_DT = $args['timeTo']->setTimezone(new DateTimeZone( $args['timeToZone'] ) );

		$this->_from_DT = new DateTime( $args['timeFrom']->format('Y-m-d H:i:s'), new DateTimeZone( $args['timeFromZone'] ));
		$this->_to_DT = new DateTime( $args['timeTo']->format('Y-m-d H:i:s'), new DateTimeZone( $args['timeToZone'] ));

	}
	/**
	 * This function checks that the From DateTime is really before the To DateTime, as the on page validation does not take into account timezones.
	 * @return boolean True if the From DateTim is indeed before the To
	 */
	private function _checkFromBeforeTo() {
		// the DateTime comparison operator is pretty smart, and takes into account time zones!
		return ( $this->_from_DT > $this->_to_DT);
	}

	/**
	 * Returns a string containing the requested time difference
	 * @param  string $diffType A string containing a value from self::$DIFF_TYPES
	 * @return string           The difference displayed as a string
	 */
	public function timeDifference($diffType = 'days') {

		if (! in_array( $diffType, self::$DIFF_TYPES ) ) {
			throw new InvalidArgumentException('diffType is not a valid type. Input was: ' . $diffType);	
		}

		if ($this->_checkFromBeforeTo() ) {
			throw new InvalidArgumentException('timeTo must be after timeFrom');		
		}

		// grab the number of seconds between our two DateTime variables
		$seconds = $this->_to_DT->getTimestamp() - $this->_from_DT->getTimestamp();

		// We'll also use the results of the diff function to determine some of the trickier queries
		$difference = $this->_from_DT->diff($this->_to_DT);


		if ($diffType == 'seconds') {
			if ($seconds == 1) $return = "$seconds second";
			else $return = "$seconds seconds";
		}

		else if ($diffType == 'minutes') {
			$minutes = $seconds / 60;
			if ($minutes == 1) $return = "$minutes minute";
			else $return = "$minutes minutes";
		}
		else if ($diffType == 'hours') {
			$hours = $seconds / 3600;
			if ($hours == 1) $return = "$hours hour";
			else $return = "$hours hours";
		}
		else if ($diffType == 'days') {
			$days = $seconds / 86400;
			if ($days == 1) $return = "$days day";
			else $return = "$days days";
		}
		else if ($diffType == 'weeks') {
			$weeks = $seconds / 604800;
			if ($weeks == 1) $return = "$weeks week";
			else $return = "$weeks weeks";
		}
		else if ($diffType == 'weekdays') {
			$weekdays = $this->_numberOfWeekDays();
			if ($weekdays == 1) $return = $weekdays . ' week day';
			else $return = $weekdays . ' weekdays';
		}
		else if ($diffType == 'compWeeks') {
			$weeks = $this->_numberOfCompleteWeeks();
			if ($weeks == 1) $return = $weeks . ' complete week (starting Sunday)';
			else $return = $weeks . ' complete weeks (starting Sunday)';
		}
		else if ($diffType == 'months') {

			$months = $difference->m + ($difference->y*12) ;
			if ($months == 1) $return = "$months month";
			else $return = "$months months";
		}
		else if ($diffType == 'years') {
			if ($difference->y == 1) $return = $difference->format("%y year");
			else $return = $difference->format("%y years");
		}

		return $return;
		

	}

	/**
	 * Parses each day within the DateInterval, and increments the counter if it's a weekday
	 * @return integer The number of Week Days
	 */
	private function _numberOfWeekDays() {
		$workingDays = [1, 2, 3, 4, 5]; // 6 is Saturday and 7 is Sunday

	    $to = $this->_to_DT;
	    // $to->modify('+1 day');
	    $interval = new DateInterval('P1D');
	    $periods = new DatePeriod($this->_from_DT, $interval, $to);

	    $days = 0;
	    // loop through each day, and if the day is Mon-Fri then we'll add it to our total
	    foreach ($periods as $period) {
	        if (!in_array($period->format('N'), $workingDays)) continue;
	        $days++;
	    }
	    return $days;
	}

	/**
	 * Calculates the number of complete weeks within the date range.  Assumes Sunday is the first day of a complete week.
	 * @return integer The number of weeks.
	 */
	private function _numberOfCompleteWeeks() {

		// find the next or previous Sunday (unless it's already one!)
		if ($this->_from_DT->format('N') != 7) $fromSunday = $this->_from_DT->modify('next sunday');
		else $fromSunday = $this->_from_DT;

		if ($this->_to_DT->format('N') != 7) $toSunday = $this->_to_DT->modify('previous sunday');
		$toSunday = $this->_to_DT;

		// it's possible that the modify function can make the toSunday occur before the fromSunday value, so where this is the case the correct answer is 0 
		if ($toSunday < $fromSunday) return 0;
		
		// $seconds = $this->_to_DT->getTimestamp() - $this->_from_DT->getTimestamp();
		$seconds = $toSunday->getTimestamp() - $fromSunday->getTimestamp();
		return intval($seconds / 604800);

	}
}