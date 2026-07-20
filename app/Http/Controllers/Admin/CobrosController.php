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
            'athlete_id'      => 'required|exists:athletes,id',
            'metodo_pago'     => 'required|in:efectivo,qr',
            'whatsapp_number' => 'nullable|string|max:20',
            'items_json'      => 'required|string',
        ]);

        $items = json_decode($validated['items_json'], true);
        if (!is_array($items) || empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Debes agregar al menos un ítem al cobro.');
        }

        $atleta = Athlete::findOrFail($validated['athlete_id']);
        $paymentGroupId = (string) \Illuminate\Support\Str::uuid();
        $totalMonto = 0;
        $primerPayment = null;

        foreach ($items as $item) {
            $monto = (float) ($item['monto'] ?? 0);
            if ($monto <= 0) continue;

            $payment = Payment::create([
                'athlete_id'          => $atleta->id,
                'concepto'            => $item['concepto'],
                'mes_correspondiente' => $item['mes_correspondiente'] ?? now()->format('Y-m'),
                'descripcion'         => $item['descripcion'] ?? null,
                'monto'               => $monto,
                'metodo_pago'         => $validated['metodo_pago'],
                'whatsapp_number'     => $validated['whatsapp_number'] ?? null,
                'estado_pago'         => 'pagado',
                'cobrado_por'         => Auth::id(),
                'payment_group_id'    => $paymentGroupId,
            ]);

            if (!$primerPayment) {
                $primerPayment = $payment;
            }

            $totalMonto += $monto;

            // Si es una mensualidad, actualizamos automáticamente las fechas de habilitación del atleta
            if ($item['concepto'] === 'mensualidad') {
                $mesCorrespondiente = $item['mes_correspondiente'] ?? now()->format('Y-m');
                try {
                    $fechaExpiracion = \Carbon\Carbon::createFromFormat('Y-m', substr($mesCorrespondiente, 0, 7))->endOfMonth()->format('Y-m-d');
                    $atleta->update([
                        'habilitado_booleano' => true,
                        'fecha_pago_habilitacion' => now()->format('Y-m-d'),
                        'fecha_vencimiento_habilitacion' => $fechaExpiracion,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error al calcular fecha de vencimiento de habilitación en venta múltiple: " . $e->getMessage());
                }
            }
        }

        if (!$primerPayment) {
            return redirect()->back()->withInput()->with('error', 'Error al procesar los ítems del cobro.');
        }

        \App\Services\ActivityLogger::log(
            'venta_realizada', 
            "Cobro múltiple realizado a {$atleta->nombre} {$atleta->apellido_paterno} por un monto total de Bs. " . number_format($totalMonto, 2) . ".",
            $primerPayment,
            ['atleta_id' => $atleta->id, 'monto' => $totalMonto, 'group_id' => $paymentGroupId]
        );

        return redirect()->route('cobros.nota', $paymentGroupId);
    }

    /** Nota de venta */
    public function nota($id)
    {
        $payments = Payment::where('payment_group_id', $id)->get();

        if ($payments->isEmpty()) {
            // Retrocompatibilidad: buscar por ID o external_id
            $payment = Payment::where('id', $id)
                ->orWhere('external_id', $id)
                ->firstOrFail();
            $payments = collect([$payment]);
        }

        $payments->load('athlete.category', 'cobrador');
        return view('admin.cobros.nota', compact('payments'));
    }

    /** Nota de venta pública (sin auth) */
    public function notaPublica($id)
    {
        $payments = Payment::where('payment_group_id', $id)
            ->orWhere('external_id', $id)
            ->get();

        if ($payments->isEmpty()) {
            // Retrocompatibilidad por ID primario
            $payment = Payment::where('id', $id)->firstOrFail();
            $payments = collect([$payment]);
        }

        $payments->load('athlete.category', 'cobrador');
        
        $esPublico = true;
        return view('admin.cobros.nota', compact('payments', 'esPublico'));
    }

    /** Descargar PDF público */
    public function downloadPublicPdf($id)
    {
        $payments = Payment::where('payment_group_id', $id)
            ->orWhere('external_id', $id)
            ->get();

        if ($payments->isEmpty()) {
            $payment = Payment::where('id', $id)->firstOrFail();
            $payments = collect([$payment]);
        }

        $payments->load('athlete.category', 'cobrador');
        
        $esPublico = true;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.cobros.pdf_nota', compact('payments', 'esPublico'))
                  ->setPaper('letter', 'portrait');
                  
        return $pdf->download('nota_venta_' . $id . '.pdf');
    }
}
