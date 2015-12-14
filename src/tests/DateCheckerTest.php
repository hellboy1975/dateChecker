<?php
class DateCheckerTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * This test should cause an exception (specifically InvalidArgumentException) to be throw, as the From value would occur after the To
     */
    public function testTimeDifferenceInvalidToException() 
    {
        $this->setExpectedException('InvalidArgumentException');

        $data['timeFrom'] = new DateTime('2015-01-01 00:00:00 ');
        $data['timeTo'] = new DateTime('2015-01-01 00:00:00 ');
        $data['timeFromZone'] = "America/Los_Angeles";
        $data['timeToZone'] = "Australia/Adelaide";

        $dc = new DateChecker($data);

        // Act
        $result = $dc->timeDifference('seconds');
    }

    /**
     * @dataProvider providerTestTimeDifference
     */
    public function testTimeDifferenceValues($type, $from, $to, $fromZone, $toZone, $expected)
    {
        // Arrange
        $data['timeFrom'] = new DateTime( $from );
        $data['timeTo'] = new DateTime( $to );
        $data['timeFromZone'] = $fromZone;
        $data['timeToZone'] = $toZone;

        $dc = new DateChecker($data);

        // Act
        $result = $dc->timeDifference($type);

        // Assert
        $this->assertEquals($expected, $result);
    }


    public function providerTestTimeDifference()
    {
        return array(
            'Seconds in a day' => array('seconds', 'now', '+1 day', "Australia/Adelaide", "Australia/Adelaide", '86400 seconds'),
            'Minutes in a day' => array('minutes', 'now', '+1 day', "Australia/Adelaide", "Australia/Adelaide", '1440 minutes'),
            'Hours in a day' => array('hours', 'now', '+1 day', "Australia/Adelaide", "Australia/Adelaide", '24 hours'),
            'Days in a week' => array('days', 'now', '+1 week', "Australia/Adelaide", "Australia/Adelaide", '7 days'),
            'Weeks in 2015' => array('weeks', '2015-01-01 00:00:00 ', '2016-01-01 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '52.142857142857 weeks'),
            'Days in a year (2015)' => array('days', '2015-01-01 00:00:00 ', '2016-01-01 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '365 days'),
            'Months in a decade' => array('months', '2000-01-01 00:00:00 ', '2010-01-01 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '120 months'),
            'Years in a decade' => array('years', '2000-01-01 00:00:00 ', '2010-01-01 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '10 years'),

            // the number of days and weeks in a leap year is different from a normal year!
            'Weeks in leap-year' => array('weeks', '2000-01-01 00:00:00 ', '2001-01-01 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '52.285714285714 weeks'),
            'Days in leap-year 2000' => array('days', '2000-01-01 00:00:00 ', '2001-01-01 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '366 days'),

            // weekdays tests
            'Weekdays in a week' => array('weekdays', 'now', '+1 week', "Australia/Adelaide", "Australia/Adelaide", '5 weekdays'),
            'Weekdays in December' => array('weekdays', 'Dec 1 00:00:01 UTC 2015', 'Dec 31 23:59:59 UTC 2015', "Australia/Adelaide", "Australia/Adelaide", '23 weekdays'),

            // complete weeks - a year has 51 "complete weeks" (beginning Sunday) with the other 1.14 weeks typically occuring at the start and finish of each year
            'Complete Weeks in a 2015' => array('compWeeks', '2015-01-01 00:00:00 ', '2016-01-01 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '51 complete weeks (starting Sunday)'),
            // starts on a Sunday, so will result in 52 weeks
            'Complete Weeks in a 2006' => array('compWeeks', '2006-01-01 00:00:00 ', '2007-01-01 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '52 complete weeks (starting Sunday)'),
            // a very short week should return 0 days correctly, and not -1
            'Complete Weeks - short week' => array('compWeeks', '2015-12-14 00:00:00 ', '2015-12-16 00:00:00 ', "Australia/Adelaide", "Australia/Adelaide", '0 complete weeks (starting Sunday)'),

            // misc tests
            /* for a week day to register it must be 24 hours since the time associated with the from date, hence the first test below registers 9 days, while the next gives 10. */
            'Weekdays over a partial day period' => array('weekdays', '12/07/2015 01:00 AM', '12/18/2015 12:00 AM', "Australia/Adelaide", "Australia/Adelaide", '9 weekdays'),
            'Weekdays over a full day period' => array('weekdays', '12/07/2015 01:00 AM', '12/18/2015 01:01 AM', "Australia/Adelaide", "Australia/Adelaide", '10 weekdays'),

            
        );
    }
}
