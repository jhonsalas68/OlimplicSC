<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AthleteSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_cobros_search_can_find_athlete_by_partial_full_name()
    {
        Category::factory()->create(['id' => 1]); // Asegurar que hay al menos una categoría

        // Crear atleta
        $atleta = Athlete::factory()->create([
            'nombre'           => 'Juan',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Gomez',
            'ci'               => '1234567',
        ]);

        // Iniciar sesión como Admin o un rol con acceso, o saltar el middleware asumiendo que el User es auth.
        // Si hay roles, podemos hacer login.
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Simulamos la consulta: Buscar solo por nombre
        $response1 = $this->getJson(route('cobros.search', ['q' => 'Juan']));
        $response1->assertJsonFragment([
            'ci' => '1234567',
            'nombre_completo' => 'Juan Perez Gomez'
        ]);

        // Simulamos la consulta: Buscar por nombre completo con espacios (lo que fallaba antes)
        $response2 = $this->getJson(route('cobros.search', ['q' => 'Juan Perez Gomez']));
        $response2->assertJsonFragment([
            'ci' => '1234567',
            'nombre_completo' => 'Juan Perez Gomez'
        ]);

        // Simulamos la consulta: Buscar saltándose un apellido
        $response3 = $this->getJson(route('cobros.search', ['q' => 'Juan Gomez']));
        $response3->assertJsonFragment([
            'ci' => '1234567',
            'nombre_completo' => 'Juan Perez Gomez'
        ]);
    }
}
