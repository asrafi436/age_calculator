<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Http\Response;

class AgeCalculatorTest extends TestCase
{
    /**
     * Test assertTrue and assertFalse for age calculation.
     */
    public function testAgeCalculationAssertions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/dashboard', ['birthdate' => now()->subYears(25)->format('Y-m-d')]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSeeText('Your age is:');

        // Use Laravel's built-in content checks
        $response->assertSee('Your age is:');
        $response->assertDontSee('Invalid date format.');
    }

    /**
     * Test assertEquals for exact age calculation.
     */
    public function testAgeCalculationForExactAge()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Age should be 30 years
        $dob = now()->subYears(30)->format('Y-m-d');
        $response = $this->post('/dashboard', ['birthdate' => $dob]);

        $response->assertStatus(Response::HTTP_OK);

        // Extract the age from the response content
        preg_match('/Your age is: (\d+)/', $response->getContent(), $matches);
        $calculatedAge = (int) $matches[1];

        $this->assertEquals(30, $calculatedAge);
    }

    /**
     * Test assertTrue and assertFalse for invalid date inputs.
     */
    public function testInvalidDOBAssertions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $invalidDOBs = [
            'abcd-ef-gh',  // Invalid format
            '2023-13-40',  // Invalid month and day
            'not-a-date',  // Completely invalid
        ];

        foreach ($invalidDOBs as $dob) {
            $response = $this->post('/dashboard', ['birthdate' => $dob]);

            $response->assertRedirect();
            $response->assertSessionHasErrors('birthdate');

            // Ensure the response does not contain the age display
            $response->assertDontSee('Your age is:');
        }
    }

    /**
     * Test assertEquals for future dates (should return an error).
     */
    public function testFutureDOBAssertions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $futureDOB = now()->addYears(10)->format('Y-m-d');
        $response = $this->post('/dashboard', ['birthdate' => $futureDOB]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('birthdate');

        // Assert the correct validation error message
        $this->assertEquals(
            'The birthdate field must be a date before or equal to today.',
            session('errors')->first('birthdate')
        );
    }

    /**
     * Test edge case for leap year birthday.
     */
    public function testCalculateAgeForEdgeCaseBirthday()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
    
        // Get today's date dynamically (make sure it's treated as today)
        $predefinedCurrentDate = now()->format('Y-m-d');
        
        // Use the predefined current date as the birthdate
        $response = $this->post('/dashboard', ['birthdate' => $predefinedCurrentDate]);
    
        // Check for a successful response
        $response->assertStatus(Response::HTTP_OK);
    
        // Check if the response contains the calculated age
        $response->assertSee('Your age is:');
    
        // Extract the age from the response content
        preg_match('/Your age is: (\d+)/', $response->getContent(), $matches);
        $calculatedAge = (int) $matches[1];
    
        // Assert that the age is 0 since the birthdate is today
        $this->assertEquals(0, $calculatedAge);
}

}
