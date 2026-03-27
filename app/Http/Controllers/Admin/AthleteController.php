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

class AthleteController extends Controller
{
    public function export()
    {
        return Excel::download(new AthleteExport, 'atletas_olimpicsc_' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
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
        $query = Athlete::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'ilike', "%{$s}%")
                  ->orWhere('apellido_paterno', 'ilike', "%{$s}%")
                  ->orWhere('apellido_materno', 'ilike', "%{$s}%")
                  ->orWhere('ci', 'ilike', "%{$s}%")
                  ->orWhere('id_alfanumerico_unico', 'ilike', "%{$s}%")
                  ->orWhereRaw("CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno,'')) ILIKE ?", ["%{$s}%"]);
            });
        }

        $athletes = $query->with('category')->latest()->paginate(10);
        return view('admin.athletes.index', compact('athletes'));
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
            'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('athletes/fotos', 'public');
        }

        $validated['habilitado_booleano'] = $request->has('habilitado_booleano');
        $validated['tiene_seguro']        = $request->has('tiene_seguro');

        $athlete = Athlete::create($validated);

        return redirect()->route('athletes.index')->with('success', 'Atleta registrado correctamente.');
    }

    public function show(Athlete $athlete)
    {
        $pagos = $athlete->payments()->latest()->take(5)->get();
        return view('admin.athletes.show', compact('athlete', 'pagos'));
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
            'foto'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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

        if ($request->hasFile('foto')) {
            if ($athlete->foto) Storage::disk('public')->delete($athlete->foto);
            $validated['foto'] = $request->file('foto')->store('athletes/fotos', 'public');
        }

        $validated['habilitado_booleano'] = $request->has('habilitado_booleano');
        $validated['tiene_seguro']        = $request->has('tiene_seguro');

        $athlete->update($validated);

        return redirect()->route('athletes.index')->with('success', 'Atleta actualizado correctamente.');
    }

    public function destroy(Athlete $athlete)
    {
        if ($athlete->foto) {
            Storage::disk('public')->delete($athlete->foto);
        }
        $athlete->delete();
        return redirect()->route('athletes.index')->with('success', 'Atleta eliminado.');
    }

    public function toggleHabilitado(Athlete $athlete)
    {
        $athlete->update(['habilitado_booleano' => !$athlete->habilitado_booleano]);
        return response()->json(['habilitado' => $athlete->habilitado_booleano]);
    }
}
