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

    /** Lista de atletas de la categoría del coach */
    public function atletas()
    {
        $user = Auth::user();
        $category = $user->category;

        $atletas = $category
            ? Athlete::with('category')->where('category_id', $category->id)->orderBy('apellido_paterno')->get()
            : collect();

        return view('coach.atletas', compact('category', 'atletas'));
    }

    /** Planificaciones del coach */
    public function planificaciones()
    {
        $user = Auth::user();
        $category = $user->category;

        $allTrainings = Training::with(['category', 'coach'])->latest()->get();

        $planificacionesPropias = collect();
        $planificacionesOtras = collect();

        if ($category) {
            $planificacionesPropias = $allTrainings->where('category_id', $category->id)->values();
            $planificacionesOtras = $allTrainings->where('category_id', '!=', $category->id)->values();
        } else {
            $planificacionesOtras = $allTrainings->values();
        }

        return view('coach.planificaciones', compact('category', 'planificacionesPropias', 'planificacionesOtras'));
    }
}
