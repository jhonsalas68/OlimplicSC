<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\AthleteExport;
use App\Imports\AthleteImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\FileStorageHelper;

class AthleteController extends Controller
{
    use FileStorageHelper;

    public function index(Request $request)
    {
        $query = Athlete::with(['category', 'latestPayment']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%$search%")
                  ->orWhere('apellido_paterno', 'LIKE', "%$search%")
                  ->orWhere('ci', 'LIKE', "%$search%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('genero')) {
            $query->where('genero', $request->genero);
        }

        if ($request->filled('deuda')) {
            if ($request->deuda === 'al_dia') {
                $query->alDia();
            } else {
                $query->debe();
            }
        }

        $athletes = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.athletes.index', compact('athletes', 'categories'));
    }

    public function create()
    {
        return view('admin.athletes.create');
    }

    public function store(Request $request)
    {
        // 1. Validación manual del C.I. con redirección directa (especial para Railway)
        if (Athlete::where('ci', $request->ci)->exists()) {
            return redirect()->route('athletes.create')->with('error', 'Este número de C.I. ya está registrado en otro atleta.');
        }

        $validated = $request->validate([
            'nombre'                  => 'required|string|max:255',
            'apellido_paterno'        => 'required|string|max:255',
            'apellido_materno'        => 'nullable|string|max:255',
            'ci'                      => 'required|string|max:20',
            'fecha_nacimiento'        => 'required|date',
            'genero'                  => 'nullable|in:Masculino,Femenino,Otro',
            'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'alergias'                => 'nullable|string',
            'habilitado_booleano'     => 'nullable|boolean',
            'tiene_seguro'            => 'nullable|boolean',
            'seguro_compania'         => 'nullable|string|max:255',
            'seguro_contacto'         => 'nullable|string|max:255',
            'nombre_padre'            => 'nullable|string|max:255',
            'apellido_paterno_padre'  => 'nullable|string|max:255',
            'apellido_materno_padre'  => 'nullable|string|max:255',
            'telefono_padre'          => 'nullable|string|max:20',
            'relacion_contacto'       => 'nullable|string|max:50',
            'contacto_nombre'         => 'nullable|string|max:255',
            'contacto_telefono'       => 'nullable|string|max:20',
            'contacto_relacion'       => 'nullable|string|max:50',
        ], [
            'ci.required' => 'El número de C.I. es obligatorio.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
        ]);

        try {
            if ($request->hasFile('foto')) {
                $path = Storage::disk('r2')->putFile('athletes', $request->file('foto'));
                $validated['foto'] = Storage::disk('r2')->url($path);
            }

            $validated['habilitado_booleano'] = $request->has('habilitado_booleano');
            $validated['tiene_seguro']        = $request->has('tiene_seguro');

            $athlete = Athlete::create($validated);

            \App\Services\ActivityLogger::log(
                'inscripcion_atleta', 
                "Nuevo atleta inscrito: {$athlete->nombre} {$athlete->apellido_paterno}.",
                $athlete
            );

            return redirect()->route('athletes.index')->with('success', 'Atleta registrado correctamente.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en AthleteController@store: ' . $e->getMessage());
            return redirect()->route('athletes.create')->with('error', 'Ocurrió un error inesperado al registrar al atleta.');
        }
    }

    public function show(Athlete $athlete)
    {
        $pagos = $athlete->payments()->latest()->take(5)->get();
        $alDia = $athlete->isAlDia();
        return view('admin.athletes.show', compact('athlete', 'pagos', 'alDia'));
    }

    public function edit(Athlete $athlete)
    {
        $esMenor = $athlete->edadActual() < 18;
        return view('admin.athletes.edit', compact('athlete', 'esMenor'));
    }

    public function update(Request $request, Athlete $athlete)
    {
        // 1. Validación manual del C.I. para evitar colapsos
        if (Athlete::where('ci', $request->ci)->where('id', '!=', $athlete->id)->exists()) {
            return redirect()->route('athletes.edit', $athlete)->with('error', 'Este número de C.I. ya está registrado en otro perfil.');
        }

        $validated = $request->validate([
            'nombre'                  => 'required|string|max:255',
            'apellido_paterno'        => 'required|string|max:255',
            'apellido_materno'        => 'nullable|string|max:255',
            'ci'                      => 'required|string|max:20',
            'fecha_nacimiento'        => 'required|date',
            'genero'                  => 'nullable|in:Masculino,Femenino,Otro',
            'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'alergias'                => 'nullable|string',
            'habilitado_booleano'     => 'nullable|boolean',
            'tiene_seguro'            => 'nullable|boolean',
            'seguro_compania'         => 'nullable|string|max:255',
            'seguro_contacto'         => 'nullable|string|max:255',
            'nombre_padre'            => 'nullable|string|max:255',
            'apellido_paterno_padre'  => 'nullable|string|max:255',
            'apellido_materno_padre'  => 'nullable|string|max:255',
            'telefono_padre'          => 'nullable|string|max:20',
            'relacion_contacto'       => 'nullable|string|max:50',
            'contacto_nombre'         => 'nullable|string|max:255',
            'contacto_telefono'       => 'nullable|string|max:20',
            'contacto_relacion'       => 'nullable|string|max:50',
        ], [
            'ci.required' => 'El número de C.I. es obligatorio.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
        ]);

        try {
            if ($request->hasFile('foto')) {
                if ($athlete->foto) {
                    $this->deleteFile($athlete->foto);
                }
                $path = Storage::disk('r2')->putFile('athletes', $request->file('foto'));
                $validated['foto'] = Storage::disk('r2')->url($path);
            }

            $validated['habilitado_booleano'] = $request->has('habilitado_booleano');
            $validated['tiene_seguro']        = $request->has('tiene_seguro');

            $athlete->update($validated);

            \App\Services\ActivityLogger::log(
                'edicion_atleta', 
                "Datos del atleta actualizados: {$athlete->nombre} {$athlete->apellido_paterno}.",
                $athlete
            );

            return redirect()->route('athletes.index')->with('success', 'Atleta actualizado correctamente.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en AthleteController@update: ' . $e->getMessage());
            return redirect()->route('athletes.edit', $athlete)->with('error', 'Ocurrió un error al actualizar el perfil.');
        }
    }

    public function destroy(Athlete $athlete)
    {
        if ($athlete->foto) {
            $this->deleteFile($athlete->foto);
        }
        \App\Services\ActivityLogger::log(
            'eliminacion_atleta', 
            "Atleta eliminado del sistema: {$athlete->nombre} {$athlete->apellido_paterno}.",
            null,
            ['nombre' => "{$athlete->nombre} {$athlete->apellido_paterno}", 'ci' => $athlete->ci]
        );

        $athlete->delete();
        return redirect()->route('athletes.index')->with('success', 'Atleta eliminado.');
    }

    public function toggleHabilitado(Athlete $athlete)
    {
        if (auth()->user()->hasRole('Coach')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $athlete->update(['habilitado_booleano' => !$athlete->habilitado_booleano]);
        
        $estado = $athlete->habilitado_booleano ? 'Habilitado' : 'Deshabilitado';
        \App\Services\ActivityLogger::log(
            'cambio_estado_atleta', 
            "Estado del atleta {$athlete->nombre} {$athlete->apellido_paterno} cambiado a: {$estado}.",
            $athlete,
            ['nuevo_estado' => $estado]
        );

        return response()->json(['habilitado' => $athlete->habilitado_booleano]);
    }

    public function exportSelected(Request $request)
    {
        $ids = json_decode($request->ids);
        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'No se seleccionaron alumnos para la exportación.');
        }

        $athletes = Athlete::whereIn('id', $ids)->with('category')->get();
        
        if ($athletes->isEmpty()) {
            return back()->with('error', 'No se encontraron registros de los atletas seleccionados.');
        }

        $pdf = Pdf::loadView('admin.athletes.export_pdf', compact('athletes'));
        return $pdf->download('lista_convocados_olimpic.pdf');
    }

    public function export()
    {
        return Excel::download(new AthleteExport, 'atletas_olimpic.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        Excel::import(new AthleteImport, $request->file('file'));
        
        return back()->with('success', 'Atletas importados correctamente.');
    }

    public function downloadPdf(Athlete $athlete)
    {
        $pdf = Pdf::loadView('admin.athletes.pdf', compact('athlete'));
        return $pdf->download("atleta_{$athlete->ci}.pdf");
    }
}
