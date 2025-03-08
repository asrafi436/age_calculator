<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AgeCalculatorController extends Controller
{
    // Show the age calculator form
    public function showForm()
    {
        return view('dashboard');
    }

    // Handle age calculation logic
    public function calculateAge(Request $request)
    {
        // Validate the birthdate input
        $request->validate([
            'birthdate' => ['required', 'date', 'before_or_equal:today'], // Prevents future dates
        ]);

        // Parse the birthdate using Carbon
        $birthdate = Carbon::parse($request->birthdate)->startOfDay();
        $now = Carbon::now()->startOfDay();

        // Calculate the difference
        $diff = $birthdate->diff($now);
        $hours = $now->diffInHours($birthdate) % 24; // Remainder of hours

        // Generate the age message
        $ageMessage = "Your age is: {$diff->y} years, {$diff->m} months, {$diff->d} days, and {$hours} hours.";

        // Return the view with the calculated age message
        return view('dashboard', compact('ageMessage'));
    }
}
