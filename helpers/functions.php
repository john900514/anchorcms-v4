<?php
use Illuminate\Support\Facades\Schema;

if (! function_exists('backpack_users_have_email')) {
    /**
     * Check if the email column is present on the user table.
     *
     * @return string
     */
    function backpack_users_have_email()
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();

        $has = Schema::connection($user->getConn())->hasColumn($user->getTable(), 'email');

        return $has;
    }
}

if (! function_exists('getDateByFloatValue')) {

    /**
     * EXCEL time to read prototype float, now converted into the standard time format formatted
     * Return time is UTC time (Coordinated Universal Time, plus 8 hours is Beijing time)
     * @param float | int $dateValue Excel float values
     * @param int $calendar_type device type default Windows 1900.Windows 1904.MAC
     * @return int timestamp
     */
    function getDateByFloatValue($dateValue = 0, $calendar_type = 1900)
    {
        // Excel date in storage is the numeric type, was calculated from January 1, 1900 to the present value
        if (1900 == $calendar_type) {// WINDOWS EXCEL in date from the base date January 1, 1900 of
            $myBaseDate = 25569; // php from 1970-01-01 25569 is the number of days to the phase difference 1900-01-01
        if ($dateValue < 60) {
            --$myBaseDate;
        }
         } Else {// MAC is a leap year date in EXCEL time difference from the base date (25569-24107 = 4 * 365 + 2) January 1, 1904, wherein two days is?
            $myBaseDate = 24107;
        }

         // perform the conversion
        if ($dateValue >= 1) {
            $utcDays = $dateValue - $myBaseDate;
            $returnValue = round($utcDays * 86400);
            if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
                $returnValue = (integer)$returnValue;
            }
        } else {
            // function for floating point rounding
            $hours = round($dateValue * 24);
            $mins = round($dateValue * 1440) - round($hours * 60);
            $secs = round($dateValue * 86400) - round($hours * 3600) - round($mins * 60);
            $returnValue = (integer)gmmktime($hours, $mins, $secs);
        }

         return $returnValue; // Returns the timestamp
    }
}
