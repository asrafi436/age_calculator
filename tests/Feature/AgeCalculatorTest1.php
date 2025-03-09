<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AgeCalculatorTest extends TestCase
{
    use RefreshDatabase; // Refresh database to reset data for each test

    /** @test */
    public function it_displays_the_age_calculator_form()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Age Calculator');
    }

    /** @test */
    public function it_calculates_age_correctly()
    {
        $user = \App\Models\User::factory()->create();
        $birthdate = Carbon::now()->subYears(20)->toDateString(); // 20 years ago

        $response = $this->actingAs($user)->post('/dashboard', [
            'birthdate' => $birthdate,
        ]);

        $response->assertStatus(200);
        $response->assertSee("Your age is: 20 years");
    }

    /** @test */
    public function it_requires_a_valid_birthdate()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard', [
            'birthdate' => '',
        ]);

        $response->assertSessionHasErrors('birthdate');
    }

    /** @test */
    public function it_handles_birthdays_on_leap_years()
    {
        $user = \App\Models\User::factory()->create();

        // Leap year birthday (Feb 29, 2000)
        $birthdate = Carbon::create(2000, 2, 29)->toDateString();
        $response = $this->actingAs($user)->post('/dashboard', [
            'birthdate' => $birthdate,
        ]);

        $expectedAge = Carbon::now()->diffInYears(Carbon::create(2000, 2, 29));

        $response->assertStatus(200);
        $response->assertSee("Your age is: $expectedAge years");
    }

    /** @test */
    public function it_handles_birthdays_on_today()
    {
        $user = \App\Models\User::factory()->create();

        $birthdate = Carbon::now()->toDateString(); // Today's date

        $response = $this->actingAs($user)->post('/dashboard', [
            'birthdate' => $birthdate,
        ]);

        $response->assertStatus(200);
        $response->assertSee("Your age is: 0 years");
    }

    /** @test */
    public function it_does_not_accept_future_dates()
    {
        $user = \App\Models\User::factory()->create();

        $futureDate = Carbon::now()->addYears(5)->toDateString(); // 5 years in the future

        $response = $this->actingAs($user)->post('/dashboard', [
            'birthdate' => $futureDate,
        ]);

        $response->assertSessionHasErrors('birthdate'); // Should trigger validation error
    }

    /** @test */
    public function it_does_not_accept_non_date_inputs()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard', [
            'birthdate' => 'not-a-date',
        ]);

        $response->assertSessionHasErrors('birthdate');
    }
}
