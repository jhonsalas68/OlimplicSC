<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #1e293b; }
    .header { background: #1e3a8a; color: white; padding: 10px 14px; margin-bottom: 12px; }
    .header h1 { font-size: 14px; font-weight: bold; }
    .header p { font-size: 8px; opacity: 0.8; margin-top: 2px; }
    .header-meta { display: flex; justify-content: space-between; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1e3a8a; color: white; }
    thead th { padding: 6px 5px; text-align: left; font-size: 8px; font-weight: bold; }
    tbody tr:nth-child(even) { background: #f0f4ff; }
    tbody tr:nth-child(odd) { background: #ffffff; }
    tbody td { padding: 5px; border-bottom: 1px solid #e2e8f0; font-size: 8px; }
    .total-row { background: #1e3a8a !important; color: white; font-weight: bold; }
    .footer { margin-top: 12px; text-align: center; font-size: 7px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; }
</style>
</head>
<body>
<div class="header">
    <div class="header-meta">
        <div>
            <h1>Club OlimpicSC — Reporte de Pagos</h1>
            <p>{{ $filtros ?? 'Todos los registros' }} | Total: {{ $pagos->count() }} registros | Bs. {{ number_format($pagos->sum('monto'), 2) }}</p>
        </div>
        <div style="text-align:right; font-size:8px;">
            <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Atleta</th>
            <th>CI</th>
            <th>Concepto</th>
            <th>Descripción</th>
            <th>Mes</th>
            <th>Monto (Bs.)</th>
            <th>Método</th>
            <th>Cobrado Por</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagos as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $p->athlete->nombre ?? '—' }} {{ $p->athlete->apellido_paterno ?? '' }}</td>
            <td>{{ $p->athlete->ci ?? '—' }}</td>
            <td>{{ $p->concepto === 'mensualidad' ? 'Mensualidad' : 'Artículo' }}</td>
            <td>{{ $p->descripcion ?? '—' }}</td>
            <td>{{ $p->mes_correspondiente ?? '—' }}</td>
            <td>{{ number_format($p->monto, 2) }}</td>
            <td>{{ strtoupper($p->metodo_pago ?? '—') }}</td>
            <td>{{ $p->cobrador->name ?? '—' }}</td>
            <td>{{ $p->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="6" style="text-align:right; padding-right:8px;">TOTAL COBRADO:</td>
            <td>Bs. {{ number_format($pagos->sum('monto'), 2) }}</td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>

<div class="footer">
    Club OlimpicSC · Santa Cruz, Bolivia · +591 69039107 · Reporte generado automáticamente
</div>
</body>
</html>
