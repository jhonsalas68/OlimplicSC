<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Venta #{{ str_pad($payment->id, 5, "0", STR_PAD_LEFT) }}</title>
    <style>
        /* ===== BASE ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ===== BARRA DE ACCIONES (solo pantalla) ===== */
        .action-bar {
            background: #0b2d69;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .action-bar-title { color: white; font-size: 14px; font-weight: 600; }
        .action-bar-title span { color: #93c5fd; }
        .action-btns { display: flex; gap: 10px; }
        .btn-back {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-back:hover { background: rgba(255,255,255,0.25); }
        .btn-print {
            background: #c61c2c;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-print:hover { background: #9b1421; }

        /* ===== CONTENEDOR PANTALLA ===== */
        .screen-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 32px 16px 48px;
        }

        /* ===== CARD DE NOTA (pantalla) ===== */
        .nota-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            width: 100%;
            max-width: 560px;
            overflow: hidden;
        }

        /* ===== HEADER DE LA NOTA ===== */
        .nota-header {
            background: linear-gradient(135deg, #0b2d69 0%, #06193b 100%);
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .nota-header-left { display: flex; align-items: center; gap: 14px; }
        
        /* Contenedor adaptado para el banner como logo */
        .nota-logo-box { 
            height: 54px; 
            width: 140px; 
            background-color: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .nota-club-name { color: white; font-size: 17px; font-weight: 800; line-height: 1.2; }
        .nota-club-sub { color: #93c5fd; font-size: 10px; margin-top: 2px; }
        .nota-header-right { text-align: right; flex-shrink: 0; }
        .nota-num-label { color: #93c5fd; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; }
        .nota-num { color: white; font-size: 22px; font-weight: 900; line-height: 1; }
        .nota-fecha { color: #bfdbfe; font-size: 10px; margin-top: 4px; }
        .badge-pagado {
            display: inline-block;
            background: #22c55e;
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }

        /* ===== CUERPO ===== */
        .nota-body { padding: 24px; }

        .section { margin-bottom: 20px; }
        .section-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; }
        .info-item label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
        .info-item span { font-size: 13px; font-weight: 600; color: #1e293b; }

        /* Concepto box */
        .concepto-box {
            background: linear-gradient(135deg, #c61c2c, #991220);
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .concepto-box-left .concepto-tipo-label { font-size: 9px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.8px; }
        .concepto-box-left .concepto-tipo { font-size: 16px; font-weight: 800; color: white; margin-top: 2px; }
        .concepto-box-left .concepto-desc { font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 4px; }
        .concepto-box-left .concepto-mes { font-size: 10px; color: rgba(255,255,255,0.65); margin-top: 2px; }
        .concepto-box-right .monto-label { font-size: 9px; color: rgba(255,255,255,0.7); text-align: right; }
        .concepto-box-right .monto-valor { font-size: 26px; font-weight: 900; color: white; text-align: right; line-height: 1; }

        /* Metodo de pago */
        .metodo-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .metodo-row-label { font-size: 11px; color: #64748b; font-weight: 600; }
        .metodo-badge {
            font-size: 12px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 20px;
        }
        .metodo-efectivo { background: #dcfce7; color: #15803d; }
        .metodo-qr { background: #dbeafe; color: #1d4ed8; }
        .metodo-tarjeta { background: #f3e8ff; color: #7c3aed; }

        /* Cobrador */
        .cobrador-row {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .cobrador-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0b2d69, #c61c2c);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 13px; flex-shrink: 0;
        }
        .cobrador-info label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; }
        .cobrador-info span { font-size: 13px; font-weight: 600; color: #1e293b; }

        /* Firmas */
        .firmas { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 8px; }
        .firma-item { text-align: center; }
        .firma-linea { border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 36px; }
        .firma-linea span { font-size: 10px; color: #94a3b8; }

        /* Footer */
        .nota-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 14px 24px;
            text-align: center;
        }
        .nota-footer p { font-size: 10px; color: #94a3b8; line-height: 1.6; }
        .nota-footer .highlight { color: #c61c2c; font-weight: 700; }

        /* ===== PRINT ===== */
        @media print {
            @page { size: 5.5in 8.5in; margin: 0.3in; }
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .action-bar { display: none !important; }
            .screen-wrapper { padding: 0; display: block; }
            .nota-card {
                border-radius: 0;
                box-shadow: none;
                max-width: 100%;
                width: 100%;
            }
            .nota-header,
            .concepto-box,
            .badge-pagado,
            .metodo-badge,
            .cobrador-avatar { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 600px) {
            .nota-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .nota-header-right { text-align: left; }
            .info-grid { grid-template-columns: 1fr; }
            .concepto-box { flex-direction: column; align-items: flex-start; }
            .concepto-box-right .monto-valor { font-size: 22px; }
            .concepto-box-right .monto-label { text-align: left; }
            .firmas { grid-template-columns: 1fr; gap: 16px; }
        }
    </style>
</head>
<body>

{{-- BARRA DE ACCIONES --}}
<div class="action-bar">
    <div class="action-bar-title">
        Nota de Venta <span>#{{ str_pad($payment->id, 5, "0", STR_PAD_LEFT) }}</span>
    </div>
    <div class="action-btns">
        <a href="{{ route('cobros.index') }}" class="btn-back">
            ← Volver
        </a>
        <button onclick="window.print()" class="btn-print">
            🖨 Imprimir
        </button>
    </div>
</div>

{{-- CONTENIDO --}}
<div class="screen-wrapper">
    <div class="nota-card">

        {{-- HEADER --}}
        <div class="nota-header">
            <div class="nota-header-left">
                <div class="nota-logo-box" style="background-image: url('{{ asset('images/banner-login.jpg') }}'); background-size: 280px auto; background-position: -8px center; background-repeat: no-repeat;"></div>
                <div>
                    <div class="nota-club-name">Club OlimpicSC</div>
                    <div class="nota-club-sub">Santa Cruz, Bolivia</div>
                </div>
            </div>
            <div class="nota-header-right">
                <div class="nota-num-label">Nota de Venta</div>
                <div class="nota-num">#{{ str_pad($payment->id, 5, "0", STR_PAD_LEFT) }}</div>
                <div class="nota-fecha">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                <div><span class="badge-pagado">✓ Pagado</span></div>
            </div>
        </div>

        {{-- CUERPO --}}
        <div class="nota-body">

            {{-- Atleta --}}
            <div class="section">
                <div class="section-title">Datos del Atleta</div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nombre Completo</label>
                        <span>{{ trim($payment->athlete->nombre . ' ' . $payment->athlete->apellido_paterno . ' ' . $payment->athlete->apellido_materno) }}</span>
                    </div>
                    <div class="info-item">
                        <label>Cedula de Identidad</label>
                        <span>{{ $payment->athlete->ci }}</span>
                    </div>

                    <div class="info-item">
                        <label>Categoria</label>
                        <span>{{ $payment->athlete->category->nombre ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Concepto</label>
                        <span class="text-blue-700">{{ $payment->concepto === 'mensualidad' ? 'Mensualidad' : 'Articulo Deportivo' }}</span>
                    </div>
                </div>
            </div>

            {{-- Concepto + Monto --}}
            <div class="concepto-box">
                <div class="concepto-box-left">
                    <div class="concepto-tipo-label">Concepto de Pago</div>
                    <div class="concepto-tipo">
                        {{ $payment->concepto === 'mensualidad' ? 'Mensualidad' : 'Articulo Deportivo' }}
                    </div>
                    @if($payment->descripcion)
                        <div class="concepto-desc">{{ $payment->descripcion }}</div>
                    @endif
                    @if($payment->concepto === 'mensualidad' && $payment->mes_correspondiente)
                        @php
                            $mes = $payment->mes_correspondiente;
                            try {
                                // Handles both "2026-03" and "2026-03-01" formats
                                $parsed = \Carbon\Carbon::createFromFormat('Y-m', substr($mes, 0, 7));
                                $mesLabel = $parsed->translatedFormat('F Y');
                            } catch(\Exception $e) {
                                $mesLabel = $mes;
                            }
                        @endphp
                        <div class="concepto-mes">Periodo: {{ $mesLabel }}</div>
                    @endif
                </div>
                <div class="concepto-box-right">
                    <div class="monto-label">Total</div>
                    <div class="monto-valor">Bs. {{ number_format($payment->monto, 2) }}</div>
                </div>
            </div>

            {{-- Metodo de pago --}}
            @php
                $metodoLabel = ['efectivo' => '💵 Efectivo', 'qr' => '📱 QR', 'tarjeta' => '💳 Tarjeta'][$payment->metodo_pago] ?? $payment->metodo_pago;
                $metodoClass = ['efectivo' => 'metodo-efectivo', 'qr' => 'metodo-qr', 'tarjeta' => 'metodo-tarjeta'][$payment->metodo_pago] ?? '';
            @endphp
            <div class="metodo-row">
                <span class="metodo-row-label">Metodo de Pago</span>
                <span class="metodo-badge {{ $metodoClass }}">{{ $metodoLabel }}</span>
            </div>

            {{-- Cobrador --}}
            @php
                $cobradorNombre = trim(($payment->cobrador->nombre ?? '') . ' ' . ($payment->cobrador->apellido_paterno ?? ''));
                if(!$cobradorNombre) $cobradorNombre = $payment->cobrador->name ?? 'Sistema';
                $cobradorIniciales = strtoupper(substr($cobradorNombre, 0, 1));
            @endphp
            <div class="cobrador-row">
                @if($payment->cobrador && $payment->cobrador->avatar)
                    <img src="{{ $payment->cobrador->avatar_url }}" class="cobrador-avatar object-cover" alt="Avatar">
                @else
                    <div class="cobrador-avatar">{{ $cobradorIniciales }}</div>
                @endif
                <div class="cobrador-info">
                    <label>Cobrado por</label>
                    <span>{{ $cobradorNombre }}</span>
                </div>
            </div>

            {{-- Firmas --}}
            <div class="firmas">
                <div class="firma-item">
                    <div class="firma-linea"><span>Firma del Cobrador</span></div>
                </div>
                <div class="firma-item">
                    <div class="firma-linea"><span>Firma del Atleta / Tutor</span></div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="nota-footer">
            <p>
                <span class="highlight">Club OlimpicSC</span> &middot;
                club.olympic.sc@gmail.com &middot; +591 69039107
            </p>
            <p>Calle Ignacio Salvatierra, Cuarto Anillo y Radial 27, Santa Cruz, Bolivia</p>
            <p style="margin-top:4px; font-size:9px;">Este documento es valido como comprobante de pago.</p>
        </div>

    </div>
</div>

</body>
</html>
