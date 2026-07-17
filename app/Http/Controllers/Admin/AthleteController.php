<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Exports\AthleteExport;
use App\Imports\AthleteImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\FileStorageHelper;

class AthleteController extends Controller
{
    use FileStorageHelper;

    private function getFilteredAthletesQuery(Request $request)
    {
        $mesActual = now()->format('Y-m');
        $query = Athlete::with('category')
            ->withExists(['payments as pagado_mes_actual' => function ($q) use ($mesActual) {
                $q->where('concepto', 'mensualidad')
                  ->where('mes_correspondiente', $mesActual)
                  ->where('estado_pago', 'pagado');
            }]);

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

        if ($request->has('tiene_seguro') && $request->tiene_seguro !== null && $request->tiene_seguro !== '') {
            if ($request->tiene_seguro == '0') {
                $query->where(function($q) {
                    $q->where('tiene_seguro', false)
                      ->orWhereNull('tiene_seguro');
                });
            } else {
                $query->where('tiene_seguro', true);
            }
        }

        if ($request->has('estado') && $request->estado !== null && $request->estado !== '') {
            if ($request->estado == '0') {
                $query->where(function($q) {
                    $q->where('habilitado_booleano', false)
                      ->orWhereNull('habilitado_booleano');
                });
            } else {
                $query->where('habilitado_booleano', true);
            }
        }

        if ($request->filled('deuda')) {
            if ($request->deuda === 'al_dia') {
                $query->alDia();
            } else {
                $query->debe();
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->getFilteredAthletesQuery($request);

            $selectedCategory = null;
            if ($request->filled('category_id')) {
                $selectedCategory = Category::find($request->category_id);
            }

            $athletes = $query->latest()->paginate(15)->withQueryString();
            $categories = Category::all();

            // Agrupación por categorías para el dashboard inicial
            $athletesByCategory = [];
            $hasFilters = $request->filled('search') || $request->filled('category_id') || $request->filled('genero') || $request->filled('deuda') || ($request->has('tiene_seguro') && $request->tiene_seguro !== null && $request->tiene_seguro !== '') || ($request->has('estado') && $request->estado !== null && $request->estado !== '');
            if (!$hasFilters) {
                foreach ($categories as $cat) {
                    $catAtletas = Athlete::where('category_id', $cat->id)->take(3)->get();
                    if ($catAtletas->isNotEmpty()) {
                        $athletesByCategory[] = [
                            'category' => $cat,
                            'athletes' => $catAtletas,
                            'total' => Athlete::where('category_id', $cat->id)->count()
                        ];
                    }
                }
            }

            return view('admin.athletes.index', compact('athletes', 'categories', 'athletesByCategory', 'selectedCategory', 'hasFilters'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en AthleteController@index: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return view('admin.athletes.index', [
                'athletes' => collect([]),
                'categories' => Category::all(),
                'athletesByCategory' => [],
                'selectedCategory' => null,
                'hasFilters' => false
            ])->with('error', 'Error al cargar los atletas. Por favor, intenta nuevamente.');
        }
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
            'genero'                  => 'nullable|in:Masculino,Femenino',
            'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'ci_anverso'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'ci_reverso'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'tiene_carnet_atleta'     => 'nullable|boolean',
            'carnet_atleta_anverso'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'carnet_atleta_reverso'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'alergias'                => 'nullable|string',
            'habilitado_booleano'     => 'nullable|boolean',
            'fecha_habilitacion'      => 'nullable|date',
            'fecha_pago_habilitacion' => 'nullable|date',
            'fecha_vencimiento_habilitacion' => 'nullable|date',
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
            $ci = $request->ci;
            $folder = "athletes/{$ci}";

            // Subida de foto de perfil
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = "perfil_" . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                $validated['foto'] = Storage::disk('r2')->url($path);
            }

            // Subida de CI Anverso
            if ($request->hasFile('ci_anverso')) {
                $file = $request->file('ci_anverso');
                $filename = "ci_anverso_" . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                $validated['ci_anverso'] = Storage::disk('r2')->url($path);
            }

            // Subida de CI Reverso
            if ($request->hasFile('ci_reverso')) {
                $file = $request->file('ci_reverso');
                $filename = "ci_reverso_" . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                $validated['ci_reverso'] = Storage::disk('r2')->url($path);
            }

            $validated['tiene_carnet_atleta'] = $request->has('tiene_carnet_atleta');

            // Subida de Carnet de Atleta (solo si tiene_carnet_atleta es verdadero)
            if ($validated['tiene_carnet_atleta']) {
                if ($request->hasFile('carnet_atleta_anverso')) {
                    $file = $request->file('carnet_atleta_anverso');
                    $filename = "carnet_atleta_anverso_" . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                    $validated['carnet_atleta_anverso'] = Storage::disk('r2')->url($path);
                }
                if ($request->hasFile('carnet_atleta_reverso')) {
                    $file = $request->file('carnet_atleta_reverso');
                    $filename = "carnet_atleta_reverso_" . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                    $validated['carnet_atleta_reverso'] = Storage::disk('r2')->url($path);
                }
            } else {
                $validated['carnet_atleta_anverso'] = null;
                $validated['carnet_atleta_reverso'] = null;
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
            return redirect()->back()->withInput()->with('error', 'Error al registrar: ' . $e->getMessage());
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
            'genero'                  => 'nullable|in:Masculino,Femenino',
            'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'ci_anverso'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'ci_reverso'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'tiene_carnet_atleta'     => 'nullable|boolean',
            'carnet_atleta_anverso'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'carnet_atleta_reverso'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'alergias'                => 'nullable|string',
            'habilitado_booleano'     => 'nullable|boolean',
            'fecha_habilitacion'      => 'nullable|date',
            'fecha_pago_habilitacion' => 'nullable|date',
            'fecha_vencimiento_habilitacion' => 'nullable|date',
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
            $ci = $request->ci;
            $folder = "athletes/{$ci}";

            // 1. Manejo del cambio de C.I. (mover carpeta en R2 si cambia)
            if ($request->ci !== $athlete->ci) {
                $oldCi = $athlete->ci;
                $oldFolder = "athletes/{$oldCi}";
                
                try {
                    $files = Storage::disk('r2')->files($oldFolder);
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            $newPath = str_replace($oldFolder, $folder, $file);
                            Storage::disk('r2')->move($file, $newPath);
                        }
                        
                        // Reemplazar la base de la URL en los campos existentes para apuntar al nuevo path
                        $oldBaseUrl = Storage::disk('r2')->url($oldFolder);
                        $newBaseUrl = Storage::disk('r2')->url($folder);
                        
                        foreach (['foto', 'ci_anverso', 'ci_reverso', 'carnet_atleta_anverso', 'carnet_atleta_reverso'] as $field) {
                            if ($athlete->$field) {
                                $athlete->$field = str_replace($oldBaseUrl, $newBaseUrl, $athlete->$field);
                            }
                        }
                        // Guardamos temporalmente en el modelo los paths actualizados
                        $athlete->save();
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("No se pudo renombrar la carpeta en R2 al cambiar el CI: " . $e->getMessage());
                }
            }

            // 2. Subida y reemplazo de foto de perfil
            if ($request->hasFile('foto')) {
                if ($athlete->foto) {
                    $this->deleteFile($athlete->foto);
                }
                $file = $request->file('foto');
                $filename = "perfil_" . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                $validated['foto'] = Storage::disk('r2')->url($path);
            }

            // 3. Subida y reemplazo de CI Anverso
            if ($request->hasFile('ci_anverso')) {
                if ($athlete->ci_anverso) {
                    $this->deleteFile($athlete->ci_anverso);
                }
                $file = $request->file('ci_anverso');
                $filename = "ci_anverso_" . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                $validated['ci_anverso'] = Storage::disk('r2')->url($path);
            }

            // 4. Subida y reemplazo de CI Reverso
            if ($request->hasFile('ci_reverso')) {
                if ($athlete->ci_reverso) {
                    $this->deleteFile($athlete->ci_reverso);
                }
                $file = $request->file('ci_reverso');
                $filename = "ci_reverso_" . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                $validated['ci_reverso'] = Storage::disk('r2')->url($path);
            }

            $validated['tiene_carnet_atleta'] = $request->has('tiene_carnet_atleta');

            // 5. Subida y reemplazo de Carnet de Atleta
            if ($validated['tiene_carnet_atleta']) {
                if ($request->hasFile('carnet_atleta_anverso')) {
                    if ($athlete->carnet_atleta_anverso) {
                        $this->deleteFile($athlete->carnet_atleta_anverso);
                    }
                    $file = $request->file('carnet_atleta_anverso');
                    $filename = "carnet_atleta_anverso_" . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                    $validated['carnet_atleta_anverso'] = Storage::disk('r2')->url($path);
                }
                if ($request->hasFile('carnet_atleta_reverso')) {
                    if ($athlete->carnet_atleta_reverso) {
                        $this->deleteFile($athlete->carnet_atleta_reverso);
                    }
                    $file = $request->file('carnet_atleta_reverso');
                    $filename = "carnet_atleta_reverso_" . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                    $validated['carnet_atleta_reverso'] = Storage::disk('r2')->url($path);
                }
            } else {
                // Si ya no tiene carnet de atleta, eliminar las fotos existentes de R2 y BD
                if ($athlete->carnet_atleta_anverso) {
                    $this->deleteFile($athlete->carnet_atleta_anverso);
                }
                if ($athlete->carnet_atleta_reverso) {
                    $this->deleteFile($athlete->carnet_atleta_reverso);
                }
                $validated['carnet_atleta_anverso'] = null;
                $validated['carnet_atleta_reverso'] = null;
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
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Athlete $athlete)
    {
        // Eliminar todas las imágenes del atleta de Cloudflare R2
        foreach (['foto', 'ci_anverso', 'ci_reverso', 'carnet_atleta_anverso', 'carnet_atleta_reverso'] as $field) {
            if ($athlete->$field) {
                $this->deleteFile($athlete->$field);
            }
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
        if (Auth::user()->hasRole('Coach')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        $nuevoEstado = !$athlete->habilitado_booleano;
        $athlete->update([
            'habilitado_booleano' => $nuevoEstado,
            'fecha_habilitacion' => $nuevoEstado ? ($athlete->fecha_habilitacion ?: now()) : null
        ]);
        
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

    public function export(Request $request)
    {
        $query = $this->getFilteredAthletesQuery($request);
        $athletes = $query->get();
        return Excel::download(new AthleteExport($athletes), 'atletas_olimpic.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = $this->getFilteredAthletesQuery($request);
        $athletes = $query->get();
        $pdf = Pdf::loadView('admin.athletes.export_data_pdf', compact('athletes'));
        return $pdf->download('lista_atletas_olimpic.pdf');
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
