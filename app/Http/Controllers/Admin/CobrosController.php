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

        $mesActual = now()->format('Y-m');
        $atletas = Athlete::with(['category', 'latestPayment'])
            ->withExists(['payments as pagado_mes_actual' => function ($q) use ($mesActual) {
                $q->where('concepto', 'mensualidad')
                  ->where('mes_correspondiente', $mesActual)
                  ->where('estado_pago', 'pagado');
            }])
            ->where(function ($query) use ($q) {
                $keywords = explode(' ', $q);
                foreach ($keywords as $word) {
                    $word = trim($word);
                    if ($word !== '') {
                        $query->where(function ($query2) use ($word) {
                        $query2->where('ci', 'like', "%{$word}%")
                               ->orWhere('nombre', 'like', "%{$word}%")
                               ->orWhere('apellido_paterno', 'like', "%{$word}%")
                               ->orWhere('apellido_materno', 'like', "%{$word}%");
                        });
                    }
                }
            })
            ->limit(8)->get()
            ->map(fn(Athlete $a) => [
                'id'                => $a->id,
                'ci'                => $a->ci,
                'nombre_completo'   => trim("{$a->nombre} {$a->apellido_paterno} {$a->apellido_materno}"),
                'iniciales'         => strtoupper(substr($a->nombre,0,1).substr($a->apellido_paterno??'',0,1)),
                'foto'              => $a->foto,
                'categoria'         => $a->category->nombre ?? '—',
                'ultimo_pago'       => $a->latestPayment?->mes_correspondiente ?? $a->latestPayment?->created_at?->format('M Y'),
                'pagado_mes_actual' => $a->pagado_mes_actual,
            ]);

        return response()->json($atletas);
    }

    /** Devuelve datos del atleta para el panel de cobro (AJAX) */
    public function getAtleta(Athlete $athlete)
    {
        $mesActual = now()->format('Y-m');
        $athlete->loadMissing(['category', 'latestPayment']);
        $alDia = $athlete->isAlDia();
        $ultimoPago = $athlete->latestPayment;
        
        return response()->json([
            'id'                => $athlete->id,
            'nombre_completo'   => trim("{$athlete->nombre} {$athlete->apellido_paterno} {$athlete->apellido_materno}"),
            'ci'                => $athlete->ci,
            'categoria'         => $athlete->category->nombre ?? '—',
            'foto'              => $athlete->foto,
            'pagado_mes_actual' => $alDia,
            'ultimo_pago'       => $ultimoPago
                ? [
                    'mes'    => $ultimoPago->mes_correspondiente, 
                    'monto'  => $ultimoPago->monto, 
                    'fecha'  => $ultimoPago->created_at->format('d/m/Y'),
                    'tipo'   => $ultimoPago->concepto
                  ]
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
            'metodo_pago'         => 'required|in:efectivo,qr',
            'whatsapp_number'     => 'nullable|string|max:20',
        ]);

        $payment = Payment::create([
            'athlete_id'          => $validated['athlete_id'],
            'concepto'            => $validated['concepto'],
            'mes_correspondiente' => $validated['mes_correspondiente'] ?? now()->format('Y-m'),
            'descripcion'         => $validated['descripcion'] ?? null,
            'monto'               => $validated['monto'],
            'metodo_pago'         => $validated['metodo_pago'],
            'whatsapp_number'     => $validated['whatsapp_number'] ?? null,
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

    /** Nota de venta pública (sin auth) */
    public function notaPublica($external_id)
    {
        $payment = Payment::where('external_id', $external_id)->firstOrFail();
        $payment->load('athlete.category', 'cobrador');
        
        // Pasamos una variable para ocultar botones administrativos en la vista pública si fuera necesario
        $esPublico = true;
        return view('admin.cobros.nota', compact('payment', 'esPublico'));
    }

    /** Descargar PDF público */
    public function downloadPublicPdf($external_id)
    {
        $payment = Payment::where('external_id', $external_id)->firstOrFail();
        $payment->load('athlete.category', 'cobrador');
        
        $esPublico = true;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.cobros.nota', compact('payment', 'esPublico'))
                  ->setPaper([0, 0, 396, 612], 'portrait');
                  
        return $pdf->stream('nota_venta_' . $payment->id . '.pdf');
    }
}
