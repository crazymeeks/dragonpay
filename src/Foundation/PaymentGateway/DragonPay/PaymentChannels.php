<?php

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay;

class PaymentChannels
{

    const EVERYDAY = 'XXXXXXX';
    // Monday - Friday only
    const WEEKDAYS   = '0XXXXX0';
    // Sunday and Friday Only
    const WEEKENDS_ONLY = 'X00000X';
    const SUNDAY_TO_FRIDAY = 'XXXXXX0';
    const MONDAY_TO_SATURDAY = '0XXXXXX';




    /**
     * Check payment channel is available everyday
     *
     * @param string $dayOfWeek
     * 
     * @return bool
     */
    public function everyDay($dayOfWeek)
    {
        return self::EVERYDAY === $dayOfWeek;
    }

    /**
     * Check payment channel is available every weekdays(Monday-Friday)
     *
     * @param string $dayOfWeek
     * 
     * @return bool
     */
    public function weekDays($daysOfWeek)
    {
        return self::WEEKDAYS === $daysOfWeek;
    }

     /**
     * Check payment channel is available every weekends(Sun & Sat)
     *
     * @param string $dayOfWeek
     * 
     * @return bool
     */
    public function weekEnds($daysOfWeek)
    {
        return self::WEEKENDS_ONLY === $daysOfWeek;
    }
    
    /**
     * Check payment channel is available every weekends(Sun-Friday)
     *
     * @param string $dayOfWeek
     * 
     * @return bool
     */
    public function sundayToFriday($daysOfWeek)
    {
        return self::SUNDAY_TO_FRIDAY === $daysOfWeek;
    }

    /**
     * Check payment channel is available every Mo-Sat
     *
     * @param string $dayOfWeek
     * 
     * @return bool
     */
    public function mondayToSaturday($daysOfWeek)
    {
        return self::MONDAY_TO_SATURDAY === $daysOfWeek;
    }
}