<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentExport;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with(['athlete.category', 'cobrador']);

        // Filtro por mes (default: mes actual)
        $mes = $request->get('mes', now()->format('Y-m'));
        if ($mes) {
            $query->where(function($q) use ($mes) {
                $q->where('mes_correspondiente', 'ilike', $mes . '%')
                  ->orWhereBetween('created_at', [
                      \Carbon\Carbon::createFromFormat('Y-m', $mes)->startOfMonth(),
                      \Carbon\Carbon::createFromFormat('Y-m', $mes)->endOfMonth(),
                  ]);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('athlete', fn($q) =>
                $q->where(function($sub) use ($search) {
                    $sub->where('nombre', 'ilike', "%$search%")
                        ->orWhere('apellido_paterno', 'ilike', "%$search%")
                        ->orWhere('apellido_materno', 'ilike', "%$search%")
                        ->orWhere('ci', 'ilike', "%$search%")
                        ->orWhereRaw("CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno,'')) ILIKE ?", ["%$search%"]);
                })
            );
        }

        if ($request->filled('metodo')) {
            $query->where('metodo_pago', $request->metodo);
        }

        if ($request->filled('concepto')) {
            $query->where('concepto', $request->concepto);
        }

        $payments = $query->latest()->paginate(15);
        return view('admin.payments.index', compact('payments'));
    }

    public function exportPdf(Request $request)
    {
        $query = Payment::with('athlete', 'cobrador');
        $this->applyFilters($query, $request);
        $pagos   = $query->latest()->get();
        $filtros = $this->filtrosLabel($request);

        $pdf = Pdf::loadView('admin.superadmin.pdf.pagos', compact('pagos', 'filtros'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('pagos_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Payment::with('athlete', 'cobrador');
        $this->applyFilters($query, $request);
        $pagos = $query->latest()->get();

        return Excel::download(
            new PaymentExport($pagos),
            'pagos_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function applyFilters($query, Request $request): void
    {
        $mes = $request->get('mes', now()->format('Y-m'));
        if ($mes) {
            $start = \Carbon\Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
            $end   = \Carbon\Carbon::createFromFormat('Y-m', $mes)->endOfMonth();
            $query->where(fn($q) =>
                $q->where('mes_correspondiente', 'ilike', $mes . '%')
                  ->orWhereBetween('created_at', [$start, $end])
            );
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('athlete', fn($q) =>
                $q->where(function($sub) use ($s) {
                    $sub->where('nombre', 'ilike', "%$s%")
                        ->orWhere('apellido_paterno', 'ilike', "%$s%")
                        ->orWhere('apellido_materno', 'ilike', "%$s%")
                        ->orWhere('ci', 'ilike', "%$s%")
                        ->orWhereRaw("CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno,'')) ILIKE ?", ["%$s%"]);
                })
            );
        }
        if ($request->filled('metodo'))  $query->where('metodo_pago', $request->metodo);
        if ($request->filled('concepto')) $query->where('concepto', $request->concepto);
    }

    private function filtrosLabel(Request $request): string
    {
        $parts = [];
        if ($request->filled('mes')) {
            $parts[] = \Carbon\Carbon::createFromFormat('Y-m', $request->mes)->translatedFormat('F Y');
        }
        if ($request->filled('metodo'))   $parts[] = ucfirst($request->metodo);
        if ($request->filled('concepto')) $parts[] = $request->concepto === 'mensualidad' ? 'Mensualidad' : 'Artículo Deportivo';
        return implode(' · ', $parts) ?: 'Todos los registros';
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Pago eliminado.');
    }
}
