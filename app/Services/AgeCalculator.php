<?php

namespace App\Services;

use Carbon\Carbon;

class AgeCalculator
{
    public function calculateAge($dob)
    {
        // Check for empty input
        if (empty($dob)) {
            return "Invalid date format.";
        }

        try {
            $dob = Carbon::parse($dob);

            // Check for future date
            if ($dob->isFuture()) {
                return "Invalid date format.";
            }

            // Calculate the age difference
            $now = Carbon::now();
            $age = $dob->diff($now);

            // Format the result
            return "Your age is: {$age->y} years, {$age->m} months, {$age->d} days, and {$age->h} hours.";
        } catch (\Exception $e) {
            return "Invalid date format.";
        }
    }
}
