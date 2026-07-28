<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_default_locale_is_indonesian(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertEquals('id', App::getLocale());
    }

    public function test_can_switch_language_to_english(): void
    {
        $response = $this->get('/lang/en');
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');

        $this->withSession(['locale' => 'en'])->get('/')
             ->assertStatus(200);
        
        $this->assertEquals('en', App::getLocale());
    }

    public function test_can_switch_language_back_to_indonesian(): void
    {
        $response = $this->get('/lang/id');
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'id');

        $this->withSession(['locale' => 'id'])->get('/')
             ->assertStatus(200);

        $this->assertEquals('id', App::getLocale());
    }
}
