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
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Traits\CloudinaryHelper;

class AthleteController extends Controller
{
    use CloudinaryHelper;
    public function export()
    {
        return Excel::download(new AthleteExport, 'atletas_olimpicsc_' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportSelected(Request $request)
    {
        $ids = json_decode($request->ids, true);
        if (!$ids) return back()->with('error', 'No se seleccionaron atletas.');

        $athletes = Athlete::whereIn('id', $ids)->with('category')->get();
        
        $pdf = Pdf::loadView('admin.athletes.export_pdf', compact('athletes'));
        return $pdf->download('convocados_' . date('Ymd_His') . '.pdf');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            Excel::import(new AthleteImport, $request->file('file'));
            return redirect()->route('athletes.index')->with('success', 'Atletas importados correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
    public function index(Request $request)
    {
        if (!$request->has('category_id') && !$request->has('search')) {
            $categories = Category::withCount('athletes')->orderBy('edad_min')->get();
            return view('admin.athletes.categories', compact('categories'));
        }

        $query = Athlete::query();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
            $selectedCategory = Category::find($request->category_id);
        } else {
            $selectedCategory = null;
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $keywords = explode(' ', $s);
                foreach ($keywords as $word) {
                    $word = trim($word);
                    if ($word !== '') {
                        $q->where(function ($query2) use ($word) {
                            $query2->where('nombre', 'ilike', "%{$word}%")
                                   ->orWhere('apellido_paterno', 'ilike', "%{$word}%")
                                   ->orWhere('apellido_materno', 'ilike', "%{$word}%")
                                   ->orWhere('ci', 'ilike', "%{$word}%")
                                   ->orWhere('id_alfanumerico_unico', 'ilike', "%{$word}%");
                        });
                    }
                }
            });
        }

        // Filtro por estado de pago (Mensualidad)
        if ($request->filled('deuda')) {
            if ($request->deuda === 'al_dia') {
                $query->alDia();
            } elseif ($request->deuda === 'deudores') {
                $query->debe();
            }
        }

        $mesActual = now()->format('Y-m');
        $athletes = $query->with('category')
            ->withExists(['payments as pagado_mes_actual' => function ($q) use ($mesActual) {
                $q->where('concepto', 'mensualidad')
                  ->where('mes_correspondiente', $mesActual)
                  ->where('estado_pago', 'pagado');
            }])
            ->latest()
            ->paginate(10);
        return view('admin.athletes.index', compact('athletes', 'selectedCategory'));
    }

    public function create()
    {
        return view('admin.athletes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'                  => 'required|string|max:255',
            'apellido_paterno'        => 'required|string|max:255',
            'apellido_materno'        => 'nullable|string|max:255',
            'ci'                      => 'required|string|max:20|unique:athletes,ci',
            'fecha_nacimiento'        => 'required|date',
            'genero'                  => 'nullable|in:Masculino,Femenino,Otro',
            'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'alergias'                => 'nullable|string',
            'habilitado_booleano'     => 'nullable|boolean',
            // Seguro
            'tiene_seguro'            => 'nullable|boolean',
            'seguro_compania'         => 'nullable|string|max:255',
            'seguro_contacto'         => 'nullable|string|max:255',
            // Contacto menor
            'nombre_padre'            => 'nullable|string|max:255',
            'apellido_paterno_padre'  => 'nullable|string|max:255',
            'apellido_materno_padre'  => 'nullable|string|max:255',
            'telefono_padre'          => 'nullable|string|max:20',
            'relacion_contacto'       => 'nullable|string|max:50',
            // Contacto mayor
            'contacto_nombre'         => 'nullable|string|max:255',
            'contacto_telefono'       => 'nullable|string|max:20',
            'contacto_relacion'       => 'nullable|string|max:50',
        ]);

        try {
            if ($request->hasFile('foto')) {
                \Illuminate\Support\Facades\Log::info('Subiendo foto a Cloudinary...');
                $response = Cloudinary::uploadApi()->upload($request->file('foto')->getRealPath(), [
                    'folder' => 'athletes'
                ]);
                $validated['foto'] = $response['secure_url'];
                \Illuminate\Support\Facades\Log::info('Foto subida: ' . $validated['foto']);
            }

            $validated['habilitado_booleano'] = $request->has('habilitado_booleano');
            $validated['tiene_seguro']        = $request->has('tiene_seguro');

            \Illuminate\Support\Facades\Log::info('Creando atleta en BD...', $validated);
            $athlete = Athlete::create($validated);
            \Illuminate\Support\Facades\Log::info('Atleta creado ID: ' . $athlete->id);

            \App\Services\ActivityLogger::log(
                'inscripcion_atleta', 
                "Nuevo atleta inscrito: {$athlete->nombre} {$athlete->apellido_paterno}.",
                $athlete
            );

            return redirect()->route('athletes.index')->with('success', 'Atleta registrado correctamente.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en AthleteController@store: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return back()->withInput()->with('error', 'Error al registrar: ' . $e->getMessage());
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
        $validated = $request->validate([
            'nombre'                  => 'required|string|max:255',
            'apellido_paterno'        => 'required|string|max:255',
            'apellido_materno'        => 'nullable|string|max:255',
            'ci'                      => 'required|string|max:20|unique:athletes,ci,' . $athlete->id,
            'fecha_nacimiento'        => 'required|date',
            'genero'                  => 'nullable|in:Masculino,Femenino,Otro',
            'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'alergias'                => 'nullable|string',
            'habilitado_booleano'     => 'nullable|boolean',
            // Seguro
            'tiene_seguro'            => 'nullable|boolean',
            'seguro_compania'         => 'nullable|string|max:255',
            'seguro_contacto'         => 'nullable|string|max:255',
            // Contacto menor
            'nombre_padre'            => 'nullable|string|max:255',
            'apellido_paterno_padre'  => 'nullable|string|max:255',
            'apellido_materno_padre'  => 'nullable|string|max:255',
            'telefono_padre'          => 'nullable|string|max:20',
            'relacion_contacto'       => 'nullable|string|max:50',
            // Contacto mayor
            'contacto_nombre'         => 'nullable|string|max:255',
            'contacto_telefono'       => 'nullable|string|max:20',
            'contacto_relacion'       => 'nullable|string|max:50',
        ]);

        try {
            if ($request->hasFile('foto')) {
                if ($athlete->foto) {
                    $this->deleteFromCloudinary($athlete->foto);
                }
                $response = Cloudinary::uploadApi()->upload($request->file('foto')->getRealPath(), [
                    'folder' => 'athletes'
                ]);
                $validated['foto'] = $response['secure_url'];
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
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Athlete $athlete)
    {
        if ($athlete->foto) {
            $this->deleteFromCloudinary($athlete->foto);
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
}
