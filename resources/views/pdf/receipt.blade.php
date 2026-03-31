<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago - {{ $payment->id }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 3cm 2cm 2cm;
            font-size: 14px;
        }
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2.5cm;
            background-color: #0b1f4a; /* Blue */
            color: white;
            text-align: center;
            line-height: 2.5cm;
            border-bottom: 5px solid #d32f2f; /* Red */
        }
        .header-content {
            display: inline-block;
            vertical-align: middle;
            line-height: normal;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .receipt-box {
            margin-top: 1cm;
            padding: 20px;
            border: 2px solid #0b1f4a;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .info-row {
            margin-bottom: 15px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #0b1f4a;
            width: 150px;
            display: inline-block;
        }
        .info-value {
            display: inline-block;
            color: #000;
        }
        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #d32f2f;
        }
        .footer {
            margin-top: 2cm;
            text-align: center;
            font-size: 11px;
            color: #777;
        }
        .logo-placeholder {
            float: left;
            margin-top: 0.2cm;
            margin-left: 0.5cm;
            width: 2cm;
            height: 2cm;
            background-color: rgba(255,255,255,0.2);
            line-height: 2cm;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo-placeholder">LOGO</div>
        <div class="header-content">
            <span class="title">Club Deportivo OlimpicSC</span>
        </div>
    </header>

    <h2 style="text-align: center; color: #d32f2f;">RECIBO OFICIAL DE PAGO</h2>

    <div class="receipt-box">
        <div class="info-row">
            <span class="info-label">Nº de Recibo:</span>
            <span class="info-value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Estudiante/Atleta:</span>
            <span class="info-value">{{ $payment->athlete->nombre }} {{ $payment->athlete->apellido_paterno }} (C.I.: {{ $payment->athlete->ci }})</span>
        </div>
        <div class="info-row">
            <span class="info-label">Concepto / Mes:</span>
            <span class="info-value">Mensualidad - {{ $payment->mes_correspondiente }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Pagado:</span>
            <span class="amount">${{ number_format($payment->monto, 2) }}</span>
        </div>
        <div class="info-row" style="border-bottom: none;">
            <span class="info-label">Estado:</span>
            <span class="info-value" style="text-transform: uppercase; font-weight: bold; color: {{ strtolower($payment->estado_pago) === 'pagado' ? '#2e7d32' : '#c62828' }};">{{ $payment->estado_pago }}</span>
        </div>
    </div>

    <div class="footer">
        Este documento es un comprobante válido de pago generado por el sistema OlimpicSC.<br>
        Gracias por su confianza.
    </div>

</body>
</html>
