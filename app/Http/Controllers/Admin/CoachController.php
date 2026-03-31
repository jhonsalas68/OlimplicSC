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
    public function atletas()
    {
        $user = Auth::user();
        $myCategory = $user->category;
        
        $allAtletas = Athlete::with(['category', 'latestPayment'])
            ->orderBy('category_id')
            ->orderBy('apellido_paterno')
            ->get();

        $atletasPropios = collect();
        $atletasOtros = collect();

        if ($myCategory) {
            $atletasPropios = $allAtletas->where('category_id', $myCategory->id)->values();
            $atletasOtros = $allAtletas->where('category_id', '!=', $myCategory->id)->groupBy(fn($a) => $a->category->nombre ?? 'Sin Categoría');
        } else {
            $atletasOtros = $allAtletas->groupBy(fn($a) => $a->category->nombre ?? 'Sin Categoría');
        }

        return view('coach.atletas', compact('myCategory', 'atletasPropios', 'atletasOtros'));
    }

    /** Planificaciones agrupadas por categoria */
    public function planificaciones()
    {
        $user = Auth::user();
        $myCategory = $user->category;

        $allTrainings = Training::with(['category', 'coach'])->latest()->get();

        $planificacionesPropias = collect();
        $planificacionesOtras = collect();

        if ($myCategory) {
            $planificacionesPropias = $allTrainings->where('category_id', $myCategory->id)->values();
            $planificacionesOtras = $allTrainings->where('category_id', '!=', $myCategory->id)
                ->groupBy(fn($t) => $t->category->nombre ?? 'Otras Categorías');
        } else {
            $planificacionesOtras = $allTrainings->groupBy(fn($t) => $t->category->nombre ?? 'Otras Categorías');
        }

        return view('coach.planificaciones', compact('myCategory', 'planificacionesPropias', 'planificacionesOtras'));
    }
}
