<?php

namespace App\Console\Commands;

use App\Models\Athlete;
use App\Models\Category;
use Illuminate\Console\Command;

class RecalcularCategorias extends Command
{
    protected $signature   = 'atletas:recalcular-categorias';
    protected $description = 'Reasigna la categoría de cada atleta según su edad actual';

    public function handle(): void
    {
        $actualizados = 0;

        Athlete::with('category')->each(function (Athlete $athlete) use (&$actualizados) {
            $nuevaCategoria = Category::resolverPorEdad($athlete->edadActual());

            if ($athlete->category_id !== $nuevaCategoria->id) {
                $anterior = $athlete->category->nombre ?? '—';
                $athlete->update(['category_id' => $nuevaCategoria->id]);
                $this->line("  {$athlete->nombre} {$athlete->apellido_paterno}: {$anterior} → {$nuevaCategoria->nombre}");
                $actualizados++;
            }
        });

        $this->info("Recalculo completado. {$actualizados} atleta(s) cambiaron de categoría.");
    }
}
