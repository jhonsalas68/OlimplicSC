@php
    $primerPayment = $payments->first();
    $idGrupo = $primerPayment->payment_group_id ?? $primerPayment->external_id;
    $totalMonto = $payments->sum('monto');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nota de Venta #{{ str_pad($primerPayment->id, 5, "0", STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1e293b; padding: 20px; line-height: 1.4; }
        
        .header-table { width: 100%; background: #0b2d69; color: white; padding: 12px 15px; margin-bottom: 15px; border-radius: 6px; }
        .header-title { font-size: 16px; font-weight: bold; }
        .header-sub { font-size: 9px; color: #93c5fd; margin-top: 2px; }
        .header-right { text-align: right; }
        .nota-num { font-size: 16px; font-weight: bold; color: #ffffff; }
        .badge-pagado { background: #22c55e; color: white; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }

        .section-title { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-top: 15px; margin-bottom: 10px; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { width: 50%; padding: 4px 0; vertical-align: top; }
        .info-label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .info-value { font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 1px; }

        .concepto-box { background: #c61c2c; color: white; padding: 12px 15px; border-radius: 6px; margin-bottom: 15px; }
        .concepto-table { width: 100%; border-collapse: collapse; }
        .concepto-title { font-size: 14px; font-weight: bold; }
        .concepto-sub { font-size: 9px; color: #fca5a5; margin-top: 2px; }
        .monto-title { font-size: 20px; font-weight: bold; text-align: right; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table th { background: #f1f5f9; padding: 6px 8px; text-align: left; font-size: 8px; color: #475569; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; }
        .items-table td { padding: 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }

        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="header-title">Club Olympic Santa Cruz</div>
                <div class="header-sub">Comprobante Oficial de Pago</div>
            </td>
            <td class="header-right">
                <div style="font-size: 8px; color: #93c5fd; text-transform: uppercase;">Nota de Venta</div>
                <div class="nota-num">#{{ str_pad($primerPayment->id, 5, "0", STR_PAD_LEFT) }}</div>
                <div style="font-size: 8px; margin-top: 2px;">{{ $primerPayment->created_at->format('d/m/Y H:i') }}</div>
                <div style="margin-top: 4px;"><span class="badge-pagado">PAGADO</span></div>
            </td>
        </tr>
    </table>

    <div class="section-title">Datos del Atleta</div>
    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">Nombre del Atleta</div>
                <div class="info-value">{{ $primerPayment->athlete->nombre ?? 'N/A' }} {{ $primerPayment->athlete->apellido_paterno ?? '' }} {{ $primerPayment->athlete->apellido_materno ?? '' }}</div>
            </td>
            <td>
                <div class="info-label">Cédula de Identidad</div>
                <div class="info-value">{{ $primerPayment->athlete->ci ?? 'N/A' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="info-label">Categoría</div>
                <div class="info-value">{{ $primerPayment->athlete->category->nombre ?? 'Sin Categoría' }}</div>
            </td>
            <td>
                <div class="info-label">Teléfono de Contacto</div>
                <div class="info-value">{{ $primerPayment->athlete->contacto_telefono ?? $primerPayment->athlete->telefono_padre ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <div class="concepto-box">
        <table class="concepto-table">
            <tr>
                <td>
                    <div style="font-size: 8px; text-transform: uppercase; opacity: 0.8;">Resumen del Cobro</div>
                    <div class="concepto-title">
                        @if($payments->count() > 1)
                            Pago Múltiple ({{ $payments->count() }} ítems)
                        @else
                            {{ $primerPayment->concepto === 'mensualidad' ? 'Mensualidad' : ($primerPayment->concepto === 'inscripcion' ? 'Inscripción' : 'Pago de Artículo') }}
                        @endif
                    </div>
                    @if($primerPayment->mes_correspondiente)
                        <div class="concepto-sub">Mes: {{ $primerPayment->mes_correspondiente }}</div>
                    @endif
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 8px; text-transform: uppercase; opacity: 0.8;">Monto Total</div>
                    <div class="monto-title">Bs. {{ number_format($totalMonto, 2) }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($payments->count() > 1)
    <div class="section-title">Detalle del Pago</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Concepto</th>
                <th>Descripción / Mes</th>
                <th style="text-align: right;">Monto (Bs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $idx => $p)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ ucfirst($p->concepto) }}</td>
                <td>{{ $p->descripcion ?? ($p->mes_correspondiente ? 'Mes: ' . $p->mes_correspondiente : '-') }}</td>
                <td style="text-align: right; font-weight: bold;">Bs. {{ number_format($p->monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="section-title">Detalle de Operación</div>
    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">Método de Pago</div>
                <div class="info-value">{{ strtoupper($primerPayment->metodo_pago ?? 'Efectivo') }}</div>
            </td>
            <td>
                <div class="info-label">Registrado Por</div>
                <div class="info-value">{{ $primerPayment->cobrador->name ?? 'Sistema' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Este documento es un comprobante oficial de pago generado por el sistema del Club Olympic Santa Cruz.<br>
        ¡Gracias por su pago!
    </div>
</body>
</html>
