<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoachController extends Controller
{
    /** Dashboard del Coach: sus planificaciones y atletas de su categoría */
    public function dashboard()
    {
        $user = Auth::user();
        $category = $user->category;

        $planificaciones = $category
            ? Training::with(['category', 'coach'])->where('category_id', $category->id)->latest()->get()
            : collect();

        $atletas = $category
            ? Athlete::with('category')->where('category_id', $category->id)->orderBy('apellido_paterno')->get()
            : collect();

        return view('coach.dashboard', compact('user', 'category', 'planificaciones', 'atletas'));
    }

    /** Lista de atletas agrupados por categoria, la del coach primero */
    public function atletas(Request $request)
    {
        $user = Auth::user();
        $myCategory = $user->category;
        $verTodas = $request->has('ver_todas');
        
        // Optimización: Si no se pide ver todas, solo cargamos los de nuestra categoría
        $query = Athlete::with(['category', 'latestPayment']);
        
        if (!$verTodas && $myCategory) {
            $query->where('category_id', $myCategory->id);
        }

        $allAtletas = $query->orderBy('category_id')
            ->orderBy('apellido_paterno')
            ->get();

        $atletasPropios = collect();
        $atletasOtros = collect();

        if ($myCategory) {
            $atletasPropios = $allAtletas->where('category_id', $myCategory->id)->values();
            if ($verTodas) {
                $atletasOtros = $allAtletas->where('category_id', '!=', $myCategory->id)
                    ->groupBy(fn($a) => $a->category?->nombre ?? 'Sin Categoría');
            }
        } elseif ($verTodas) {
            $atletasOtros = $allAtletas->groupBy(fn($a) => $a->category?->nombre ?? 'Sin Categoría');
        }

        return view('coach.atletas', compact('user', 'myCategory', 'atletasPropios', 'atletasOtros', 'verTodas'));
    }

    /** Planificaciones agrupadas por categoria */
    public function planificaciones(Request $request)
    {
        $user = Auth::user();
        $myCategory = $user->category;
        $verTodas = $request->has('ver_todas');

        $query = Training::with(['category', 'coach']);
        
        if (!$verTodas && $myCategory) {
            $query->where('category_id', $myCategory->id);
        }

        $allTrainings = $query->latest()->get();

        $planificacionesPropias = collect();
        $planificacionesOtras = collect();

        if ($myCategory) {
            $planificacionesPropias = $allTrainings->where('category_id', $myCategory->id)->values();
            if ($verTodas) {
                $planificacionesOtras = $allTrainings->where('category_id', '!=', $myCategory->id)
                    ->groupBy(fn($t) => $t->category?->nombre ?? 'Otras Categorías');
            }
        } elseif ($verTodas) {
            $planificacionesOtras = $allTrainings->groupBy(fn($t) => $t->category?->nombre ?? 'Otras Categorías');
        }

        return view('coach.planificaciones', compact('user', 'myCategory', 'planificacionesPropias', 'planificacionesOtras', 'verTodas'));
    }
}
