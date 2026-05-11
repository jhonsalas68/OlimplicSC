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
        $userCategories = $user->categories;
        $myCategoryIds = $userCategories->pluck('id')->toArray();
        $myCategories = $userCategories; // Para la vista

        $planificaciones = !empty($myCategoryIds)
            ? Training::with(['category', 'coach'])->whereIn('category_id', $myCategoryIds)->latest()->get()
            : collect();

        $mesActual = now()->format('Y-m');
        $atletas = !empty($myCategoryIds)
            ? Athlete::with('category')
                ->withExists(['payments as pagado_mes_actual' => function ($q) use ($mesActual) {
                    $q->where('concepto', 'mensualidad')
                      ->where('mes_correspondiente', $mesActual)
                      ->where('estado_pago', 'pagado');
                }])
                ->whereIn('category_id', $myCategoryIds)
                ->orderBy('apellido_paterno')
                ->get()
            : collect();

        return view('coach.dashboard', compact('user', 'myCategories', 'planificaciones', 'atletas'));
    }

    /** Lista de atletas agrupados por categoria */
    public function atletas(Request $request)
    {
        $user = Auth::user();
        $userCategories = $user->categories;
        $myCategoryIds = $userCategories->pluck('id')->toArray();
        $myCategories = $userCategories;

        $mesActual = now()->format('Y-m');

        // 1. MIS ATLETAS (Sección Superior)
        $queryPropios = Athlete::with(['category'])
            ->withExists(['payments as pagado_mes_actual' => function ($q) use ($mesActual) {
                $q->where('concepto', 'mensualidad')
                  ->where('mes_correspondiente', $mesActual)
                  ->where('estado_pago', 'pagado');
            }]);
        
        if ($request->filled('category_id')) {
            if (in_array($request->category_id, $myCategoryIds)) {
                $queryPropios->where('category_id', $request->category_id);
            } else {
                $queryPropios->whereIn('category_id', $myCategoryIds);
            }
        } else {
            $queryPropios->whereIn('category_id', $myCategoryIds);
        }

        if ($request->filled('genero')) {
            $queryPropios->where('genero', $request->genero);
        }

        if ($request->filled('deuda')) {
            if ($request->deuda === 'al_dia') {
                $queryPropios->alDia();
            } elseif ($request->deuda === 'deudores') {
                $queryPropios->debe();
            }
        }

        $atletasPropios = $queryPropios->orderBy('apellido_paterno')->get();

        // 2. BUSCADOR GENERAL (Todas las categorías - Sección Inferior)
        $queryGeneral = Athlete::with(['category'])
            ->withExists(['payments as pagado_mes_actual' => function ($q) use ($mesActual) {
                $q->where('concepto', 'mensualidad')
                  ->where('mes_correspondiente', $mesActual)
                  ->where('estado_pago', 'pagado');
            }]);

        if ($request->filled('search')) {
            $s = $request->search;
            $queryGeneral->where(function($q) use ($s) {
                $q->where('nombre', 'like', "%$s%")
                  ->orWhere('apellido_paterno', 'like', "%$s%")
                  ->orWhere('ci', 'like', "%$s%");
            });
        }

        if ($request->filled('genero_gen')) {
            $queryGeneral->where('genero', $request->genero_gen);
        }

        if ($request->filled('deuda_gen')) {
            if ($request->deuda_gen === 'al_dia') {
                $queryGeneral->alDia();
            } elseif ($request->deuda_gen === 'deudores') {
                $queryGeneral->debe();
            }
        }

        // Si no hay búsqueda ni filtros, mostramos los últimos 10 de todo el club o nada? 
        // Vamos a mostrar los 15 más recientes si no hay filtros para que no esté vacío.
        $atletasGeneral = ($request->filled('search') || $request->filled('genero_gen') || $request->filled('deuda_gen'))
            ? $queryGeneral->orderBy('apellido_paterno')->paginate(20)->withQueryString()
            : $queryGeneral->latest()->limit(10)->get();

        $categories = $userCategories->map(function($cat) {
            return [
                'category' => $cat,
                'is_mine' => true,
                'count' => $cat->athletes()->count(),
            ];
        });

        return view('coach.atletas', compact('user', 'myCategories', 'atletasPropios', 'atletasGeneral', 'categories'));
    }

    /** Planificaciones de sus categorías */
    public function planificaciones(Request $request)
    {
        $user = Auth::user();
        $userCategories = $user->categories;
        $myCategoryIds = $userCategories->pluck('id')->toArray();
        $myCategories = $userCategories;

        $query = Training::with(['category', 'coach']);
        
        // Solo las suyas
        $query->whereIn('category_id', $myCategoryIds);

        $planificacionesPropias = $query->latest()->get();

        return view('coach.planificaciones', compact('user', 'myCategories', 'planificacionesPropias'));
    }
}
