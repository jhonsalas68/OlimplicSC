<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CobrosController extends Controller
{
    /** Panel principal de cobros */
    public function index()
    {
        return view('admin.cobros.index');
    }

    /** Búsqueda AJAX por nombre o CI */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        $atletas = Athlete::with(['category', 'latestPayment'])
            ->where(function ($query) use ($q) {
                $keywords = explode(' ', $q);
                foreach ($keywords as $word) {
                    $word = trim($word);
                    if ($word !== '') {
                        $query->where(function ($query2) use ($word) {
                            $query2->where('ci', 'ilike', "%{$word}%")
                                   ->orWhere('nombre', 'ilike', "%{$word}%")
                                   ->orWhere('apellido_paterno', 'ilike', "%{$word}%")
                                   ->orWhere('apellido_materno', 'ilike', "%{$word}%");
                        });
                    }
                }
            })
            ->limit(8)->get()
            ->map(fn(Athlete $a) => [
                'id'             => $a->id,
                'ci'             => $a->ci,
                'nombre_completo'=> trim("{$a->nombre} {$a->apellido_paterno} {$a->apellido_materno}"),
                'iniciales'      => strtoupper(substr($a->nombre,0,1).substr($a->apellido_paterno??'',0,1)),
                'foto'           => $a->foto ? (str_starts_with($a->foto, 'http') ? str_replace('/upload/', '/upload/c_fill,w_100,h_100,q_auto,f_auto/', $a->foto) : $a->foto) : null,
                'categoria'      => $a->category->nombre ?? '—',
                'ultimo_pago'    => $a->latestPayment?->created_at?->format('M Y'),
            ]);

        return response()->json($atletas);
    }

    /** Devuelve datos del atleta para el panel de cobro (AJAX) */
    public function getAtleta(Athlete $athlete)
    {
        $athlete->loadMissing(['category', 'latestPayment']);
        $ultimoPago = $athlete->latestPayment;
        return response()->json([
            'id'             => $athlete->id,
            'nombre_completo'=> trim("{$athlete->nombre} {$athlete->apellido_paterno} {$athlete->apellido_materno}"),
            'ci'             => $athlete->ci,
            'categoria'      => $athlete->category->nombre ?? '—',
            'foto'           => $athlete->foto ? (str_starts_with($athlete->foto, 'http') ? str_replace('/upload/', '/upload/c_fill,w_150,h_150,q_auto,f_auto/', $athlete->foto) : $athlete->foto) : null,
            'ultimo_pago'    => $ultimoPago
                ? ['mes' => $ultimoPago->mes_correspondiente, 'monto' => $ultimoPago->monto, 'fecha' => $ultimoPago->created_at->format('d/m/Y')]
                : null,
        ]);
    }

    /** Procesar el cobro */
    public function cobrar(Request $request)
    {
        $validated = $request->validate([
            'athlete_id'          => 'required|exists:athletes,id',
            'concepto'            => 'required|in:mensualidad,articulo_deportivo',
            'mes_correspondiente' => 'required_if:concepto,mensualidad|nullable|string|max:7',
            'descripcion'         => 'nullable|string|max:255',
            'monto'               => 'required|numeric|min:0.01',
            'metodo_pago'         => 'required|in:efectivo,qr,tarjeta',
        ]);

        $payment = Payment::create([
            'athlete_id'          => $validated['athlete_id'],
            'concepto'            => $validated['concepto'],
            'mes_correspondiente' => $validated['mes_correspondiente'] ?? now()->format('Y-m'),
            'descripcion'         => $validated['descripcion'] ?? null,
            'monto'               => $validated['monto'],
            'metodo_pago'         => $validated['metodo_pago'],
            'estado_pago'         => 'pagado',
            'cobrado_por'         => Auth::id(),
        ]);

        $atleta = Athlete::find($validated['athlete_id']);
        \App\Services\ActivityLogger::log(
            'venta_realizada', 
            "Cobro realizado a {$atleta->nombre} {$atleta->apellido_paterno} por un monto de Bs. {$validated['monto']}.",
            $payment,
            ['atleta_id' => $atleta->id, 'monto' => $validated['monto']]
        );

        return redirect()->route('cobros.nota', $payment->id);
    }

    /** Nota de venta */
    public function nota(Payment $payment)
    {
        $payment->load('athlete.category', 'cobrador');
        return view('admin.cobros.nota', compact('payment'));
    }
}
