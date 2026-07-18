<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Category;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CobrosMultiplesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_process_multi_concept_checkout(): void
    {
        // 1. Crear categoría manualmente
        $category = Category::create([
            'nombre' => 'Sub 15',
            'descripcion' => 'Categoría Sub 15',
            'edad_min' => 10,
            'edad_max' => 15,
        ]);

        // 2. Crear atleta manualmente
        $athlete = Athlete::create([
            'nombre' => 'Carlos',
            'apellido_paterno' => 'Miranda',
            'apellido_materno' => 'Suarez',
            'ci' => '7654321',
            'fecha_nacimiento' => '2010-05-15',
            'category_id' => $category->id,
            'genero' => 'masculino',
            'habilitado_booleano' => false,
        ]);

        // 3. Crear usuario admin
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
        $admin->assignRole('Admin');

        // Autenticar al usuario
        $this->actingAs($admin);

        // 4. Definir ítems en JSON
        $items = [
            [
                'concepto' => 'mensualidad',
                'mes_correspondiente' => '2026-07',
                'descripcion' => 'Julio mensualidad',
                'monto' => 350.00
            ],
            [
                'concepto' => 'articulo_deportivo',
                'mes_correspondiente' => null,
                'descripcion' => 'Polera de entrenamiento',
                'monto' => 120.00
            ]
        ];

        // 5. Enviar petición POST a cobros.cobrar
        $response = $this->post(route('cobros.cobrar'), [
            'athlete_id' => $athlete->id,
            'metodo_pago' => 'qr',
            'whatsapp_number' => '70010020',
            'items_json' => json_encode($items),
        ]);

        if (session('errors')) {
            dump(session('errors')->getMessages());
        }
        if (session('error')) {
            dump(session('error'));
        }

        // 6. Verificar que la redirección apunte a la nota de venta del grupo
        $response->assertStatus(302);

        // Recuperar pagos de la BD
        $payments = Payment::where('athlete_id', $athlete->id)->get();

        $this->assertCount(2, $payments);

        // Ambos pagos deben compartir el mismo payment_group_id
        $this->assertNotNull($payments[0]->payment_group_id);
        $this->assertEquals($payments[0]->payment_group_id, $payments[1]->payment_group_id);

        // Verificar montos
        $this->assertEquals(350.00, $payments->where('concepto', 'mensualidad')->first()->monto);
        $this->assertEquals(120.00, $payments->where('concepto', 'articulo_deportivo')->first()->monto);

        // 7. Verificar que el estatus deportivo del atleta se actualizó automáticamente
        $athlete->refresh();
        $this->assertTrue((bool)$athlete->habilitado_booleano);
        $this->assertEquals(now()->format('Y-m-d'), $athlete->fecha_pago_habilitacion->format('Y-m-d'));
        $this->assertEquals('2026-07-31', $athlete->fecha_vencimiento_habilitacion->format('Y-m-d'));
    }
}
