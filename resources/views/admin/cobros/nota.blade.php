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
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255,255,255,0.1);
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
        .btn-whatsapp {
            background: #25d366;
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
            text-decoration: none;
        }
        .btn-whatsapp:hover { background: #128c7e; }

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
            max-width: 700px; /* Un poco mas ancho para la foto */
            overflow: hidden;
        }

        /* ===== HEADER DE LA NOTA ===== */
        .nota-header {
            background: #0b2d69;
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .nota-header-left { display: flex; align-items: center; gap: 14px; }
        
        .nota-logo-box { 
            height: 60px; 
            width: 150px; 
            background-color: white; 
            border-radius: 8px; 
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .nota-logo-box img { width: 100%; height: auto; }
        
        .nota-club-name { color: white; font-size: 20px; font-weight: 800; line-height: 1.2; }
        .nota-club-sub { color: #93c5fd; font-size: 11px; margin-top: 2px; }
        .nota-header-right { text-align: right; flex-shrink: 0; }
        .nota-num-label { color: #93c5fd; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .nota-num { color: white; font-size: 28px; font-weight: 900; line-height: 1; margin: 4px 0; }
        .nota-fecha { color: #bfdbfe; font-size: 11px; margin-bottom: 8px; }
        .badge-pagado {
            display: inline-block;
            background: #22c55e;
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* ===== CUERPO ===== */
        .nota-body { padding: 30px; }

        .section { margin-bottom: 25px; }
        .section-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #94a3b8;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px 30px; }
        .info-item label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
        .info-item span { font-size: 14px; font-weight: 700; color: #1e293b; }

        /* Foto Atleta */
        .athlete-photo-box {
            width: 85px; height: 85px;
            border-radius: 14px;
            border: 3px solid #f1f5f9;
            overflow: hidden;
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .athlete-photo-box img { width: 100%; height: 100%; object-fit: cover; }

        /* Concepto box - ROJO */
        .concepto-box {
            background: #c61c2c;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: white;
        }
        .concepto-tipo-label { font-size: 10px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .concepto-tipo { font-size: 20px; font-weight: 900; margin-top: 4px; }
        .concepto-desc { font-size: 12px; color: rgba(255,255,255,0.9); margin-top: 6px; }
        .concepto-mes { font-size: 11px; color: rgba(255,255,255,0.7); margin-top: 4px; }
        .monto-label { font-size: 10px; color: rgba(255,255,255,0.8); text-align: right; font-weight: 600; }
        .monto-valor { font-size: 34px; font-weight: 900; text-align: right; line-height: 1; }

        /* Metodo de pago */
        .metodo-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }
        .metodo-row-label { font-size: 12px; color: #64748b; font-weight: 700; }
        .metodo-badge {
            font-size: 13px;
            font-weight: 800;
            padding: 6px 18px;
            border-radius: 20px;
            background: #dcfce7;
            color: #15803d;
        }

        /* Cobrador */
        .cobrador-row {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 25px;
        }
        .cobrador-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #0b2d69;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 15px;
        }
        .cobrador-info label { font-size: 9px; color: #94a3b8; text-transform: uppercase; font-weight: 700; }
        .cobrador-info span { font-size: 14px; font-weight: 700; color: #1e293b; }

        /* Firmas */
        .firmas { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 10px; }
        .firma-item { text-align: center; }
        .firma-linea { border-top: 1px dashed #cbd5e1; padding-top: 10px; margin-top: 40px; }
        .firma-linea span { font-size: 11px; color: #94a3b8; font-weight: 600; }

        /* Footer */
        .nota-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px;
            text-align: center;
        }
        .nota-footer p { font-size: 11px; color: #64748b; line-height: 1.6; }
        .nota-footer .highlight { color: #c61c2c; font-weight: 800; }

        /* ===== PRINT (B&W SAVE INK) ===== */
        @media print {
            @page { size: portrait; margin: 0.5in; }
            body { background: white; color: black; }
            .action-bar { display: none !important; }
            .screen-wrapper { padding: 0; display: block; }
            .nota-card { box-shadow: none; border: 1px solid #000; border-radius: 0; max-width: 100%; }
            
            /* Header B&W */
            .nota-header { background: white !important; border-bottom: 2px solid black; padding: 15px; }
            .nota-club-name, .nota-num, .nota-num-label, .nota-fecha, .nota-club-sub { color: black !important; }
            .badge-pagado { border: 1px solid black; color: black !important; background: white !important; }
            
            /* Concepto B&W */
            .concepto-box { 
                background: white !important; 
                color: black !important; 
                border: 2px solid black !important; 
                padding: 15px;
            }
            .concepto-tipo-label, .concepto-desc, .concepto-mes, .monto-label { color: black !important; }
            
            /* Otros elementos */
            .metodo-row, .cobrador-row, .nota-footer { background: white !important; border: 1px solid black !important; }
            .metodo-badge { background: white !important; border: 1px solid black; color: black !important; }
            .cobrador-avatar { border: 1px solid black; background: white !important; color: black !important; }
            .section-title { border-bottom: 1px solid black; color: black !important; }
            .athlete-photo-box { border: 1px solid black; filter: grayscale(100%); }
            .nota-logo-box { border: 1px solid black; filter: grayscale(100%); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 600px) {
            .nota-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .nota-header-right { text-align: left; }
            .info-grid { grid-template-columns: 1fr; }
            .concepto-box { flex-direction: column; align-items: flex-start; }
            .concepto-box-right { width: 100%; }
            .monto-valor { font-size: 28px; text-align: left; margin-top: 5px; }
            .monto-label { text-align: left; }
            .firmas { grid-template-columns: 1fr; gap: 20px; }
        }
    </style>
</head>
<body>

{{-- BARRA DE ACCIONES --}}
@if(!isset($esPublico) || !$esPublico)
<div class="action-bar">
    <div class="action-bar-title">
        Nota de Venta <span>#{{ str_pad($payment->id, 5, "0", STR_PAD_LEFT) }}</span>
    </div>
    <div class="action-btns">
        <a href="{{ route('payments.index') }}" class="btn-back" data-turbo="false">
            ← Volver
        </a>
        <button onclick="window.print()" class="btn-print">
            🖨 Imprimir
        </button>
        @if($payment->whatsapp_number)
            @php
                $atletaNombre = trim($payment->athlete->nombre . ' ' . $payment->athlete->apellido_paterno);
                $conceptoLabel = $payment->concepto === 'mensualidad' ? 'Mensualidad' : 'Artículo Deportivo';
                $montoFormatted = number_format($payment->monto, 2);
                $publicUrl = route('cobros.download_pdf', $payment->external_id);
                $mensaje = "Hola {$atletaNombre}, esta es su nota de venta de OlimpicSC.\n\n" .
                           "*Detalle:* {$conceptoLabel}\n" .
                           "*Monto:* Bs. {$montoFormatted}\n" .
                           "*Fecha:* " . $payment->created_at->format('d/m/Y') . "\n\n" .
                           "*Descargar PDF:* {$publicUrl}\n\n" .
                           "¡Gracias por su pago!";
                $waUrl = "https://wa.me/591" . $payment->whatsapp_number . "?text=" . urlencode($mensaje);
            @endphp
            <a href="{{ $waUrl }}" target="_blank" class="btn-whatsapp">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.237 3.483 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.652zm6.799-3.814c1.543.917 3.31 1.398 5.103 1.399h.005c5.454 0 9.893-4.438 9.895-9.892.001-2.641-1.027-5.127-2.896-6.996s-4.355-2.896-6.998-2.897c-5.453 0-9.891 4.439-9.894 9.894-.001 1.756.459 3.468 1.329 4.972l-.875 3.195 3.268-.857zm11.361-4.947c-.273-.137-1.62-.8-1.87-.891-.249-.09-.431-.137-.613.137-.182.273-.706.891-.865 1.072-.158.182-.317.204-.59.068-.273-.137-1.15-.424-2.19-1.353-.809-.721-1.355-1.612-1.513-1.886-.158-.273-.017-.422.12-.558.122-.122.273-.318.409-.477.136-.159.182-.273.272-.455.09-.181.046-.341-.023-.477-.068-.137-.613-1.477-.841-2.022-.222-.533-.448-.46-.613-.468h-.523c-.182 0-.477.067-.727.341-.25.272-.954.932-.954 2.271 0 1.34.977 2.636 1.114 2.818.136.182 1.921 2.934 4.653 4.111.649.279 1.157.446 1.552.571.652.207 1.245.178 1.713.108.522-.078 1.62-.662 1.848-1.27.227-.609.227-1.133.159-1.272-.068-.138-.25-.227-.523-.364z"/>
                </svg>
                WhatsApp
            </a>
        @endif
    </div>
</div>
@endif

{{-- CONTENIDO --}}
<div class="screen-wrapper">
    <div class="nota-card">

        {{-- HEADER --}}
        <div class="nota-header">
            <div class="nota-header-left">
                <div class="nota-logo-box">
                    <img src="{{ public_path('images/banner-login.jpg') }}" alt="Logo" onerror="this.src='{{ asset('images/banner-login.jpg') }}'">
                </div>
                <div>
                    <div class="nota-club-name">Club OlimpicSC</div>
                    <div class="nota-club-sub">Santa Cruz, Bolivia</div>
                </div>
            </div>
            <div class="nota-header-right">
                <div class="nota-num-label">Nota de Venta</div>
                <div class="nota-num">#{{ str_pad($payment->id, 5, "0", STR_PAD_LEFT) }}</div>
                <div class="nota-fecha">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                <div><span class="badge-pagado">PAGADO</span></div>
            </div>
        </div>

        {{-- CUERPO --}}
        <div class="nota-body">

            {{-- Atleta --}}
            <div class="section">
                <div class="section-title">Datos del Atleta</div>
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    @if($payment->athlete->foto)
                        <div class="athlete-photo-box">
                            @php
                                $fotoRaw = $payment->athlete->foto;
                                $fotoFinal = null;
                                
                                if (str_starts_with($fotoRaw, 'http')) {
                                    $fotoFinal = $fotoRaw;
                                } else {
                                    // Base64 encoding for PDF reliability
                                    $fullPath = public_path('storage/' . $fotoRaw);
                                    if (file_exists($fullPath)) {
                                        $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                                        $data = file_get_contents($fullPath);
                                        $fotoFinal = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                    } else {
                                        $fotoFinal = asset('storage/' . $fotoRaw);
                                    }
                                }
                            @endphp
                            <img src="{{ $fotoFinal }}" alt="Foto Atleta">
                        </div>
                    @else
                        <div class="athlete-photo-box" style="display: flex; align-items: center; justify-content: center; background: #eff6ff; color: #2563eb; font-weight: 800; font-size: 24px;">
                            {{ substr($payment->athlete->nombre, 0, 1) }}{{ substr($payment->athlete->apellido_paterno, 0, 1) }}
                        </div>
                    @endif
                    <div class="info-grid" style="flex: 1;">
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
                $metodoLabel = ['efectivo' => 'Efectivo', 'qr' => 'QR', 'tarjeta' => 'Tarjeta'][$payment->metodo_pago] ?? $payment->metodo_pago;
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
