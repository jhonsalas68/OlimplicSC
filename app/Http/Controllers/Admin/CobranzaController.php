<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CobranzaController extends Controller
{
    /** Pantalla principal: buscar atleta por CI */
    public function index()
    {
        return view('admin.cobranza.index');
    }

    /** Buscar atleta por CI vía AJAX o POST */
    public function buscar(Request $request)
    {
        $request->validate(['ci' => 'required|string']);

        $athlete = Athlete::with('category')
            ->where('ci', $request->ci)
            ->first();

        if (! $athlete) {
            return back()->withErrors(['ci' => 'No se encontró ningún atleta con ese C.I.'])->withInput();
        }

        return view('admin.cobranza.cobrar', compact('athlete'));
    }

    /** Búsqueda AJAX: devuelve JSON con atletas que coincidan por nombre o CI */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $atletas = Athlete::with('category')
            ->where(function ($query) use ($q) {
                $query->where('ci', 'ilike', "%{$q}%")
                      ->orWhere('nombre', 'ilike', "%{$q}%")
                      ->orWhere('apellido_paterno', 'ilike', "%{$q}%")
                      ->orWhere('apellido_materno', 'ilike', "%{$q}%")
                      ->orWhereRaw("CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno,'')) ILIKE ?", ["%{$q}%"]);
            })
            ->limit(8)
            ->get()
            ->map(function (Athlete $a) {
                $ultimoPago = $a->payments()->latest()->first();
                return [
                    'ci'            => $a->ci,
                    'nombre_completo' => trim("{$a->nombre} {$a->apellido_paterno} {$a->apellido_materno}"),
                    'iniciales'     => strtoupper(substr($a->nombre, 0, 1) . substr($a->apellido_paterno ?? '', 0, 1)),
                    'foto'          => $a->foto,
                    'categoria'     => $a->category->nombre ?? '—',
                    'ultimo_pago'   => $ultimoPago
                        ? \Carbon\Carbon::parse($ultimoPago->created_at)->format('M Y')
                        : null,
                ];
            });

        return response()->json($atletas);
    }

    /** Registrar el cobro y redirigir a la nota de venta */
    public function cobrar(Request $request)
    {
        $validated = $request->validate([
            'athlete_id'        => 'required|exists:athletes,id',
            'concepto'          => 'required|in:mensualidad,articulo_deportivo',
            'descripcion'       => 'nullable|string|max:255',
            'mes_correspondiente' => 'required|string|max:50',
            'monto'             => 'required|numeric|min:0.01',
        ]);

        $payment = Payment::create([
            'athlete_id'          => $validated['athlete_id'],
            'concepto'            => $validated['concepto'],
            'descripcion'         => $validated['descripcion'] ?? null,
            'mes_correspondiente' => $validated['mes_correspondiente'],
            'monto'               => $validated['monto'],
            'estado_pago'         => 'pagado',
            'cobrado_por'         => Auth::id(),
        ]);

        return redirect()->route('cobranza.nota', $payment->id);
    }

    /** Mostrar nota de venta en pantalla (para imprimir) */
    public function nota(Payment $payment)
    {
        $payment->load('athlete.category', 'cobrador');
        return view('admin.cobranza.nota', compact('payment'));
    }
}
