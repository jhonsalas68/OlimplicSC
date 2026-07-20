<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ficha de Atleta - {{ $athlete->nombre }} {{ $athlete->apellido_paterno }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1e293b; padding: 25px; line-height: 1.4; }
        
        .header { border-bottom: 3px solid #1e3a8a; padding-bottom: 12px; margin-bottom: 20px; }
        .logo-title { font-size: 20px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; }
        .subtitle { font-size: 13px; font-weight: bold; color: #dc2626; text-transform: uppercase; margin-top: 3px; }
        .date { font-size: 9px; color: #64748b; margin-top: 4px; }
        
        .section-title { font-size: 11px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-top: 15px; margin-bottom: 10px; }
        
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-grid td { padding: 6px 8px; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        .label { font-weight: bold; color: #475569; width: 30%; }
        .value { color: #0f172a; width: 70%; }
        
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 9px; }
        .badge-si { background-color: #dcfce7; color: #15803d; }
        .badge-no { background-color: #fee2e2; color: #b91c1c; }
        
        .footer { position: fixed; bottom: 20px; left: 25px; right: 25px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-title">Club Olympic Santa Cruz</div>
        <div class="subtitle">Ficha Individual de Atleta</div>
        <div class="date">Fecha de Emisión: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="section-title">Información Personal</div>
    <table class="info-grid">
        <tr>
            <td class="label">Nombre Completo:</td>
            <td class="value" style="font-weight: bold; font-size: 12px; color: #1e3a8a;">{{ $athlete->nombre }} {{ $athlete->apellido_paterno }} {{ $athlete->apellido_materno }}</td>
        </tr>
        <tr>
            <td class="label">Cédula de Identidad (C.I.):</td>
            <td class="value">{{ $athlete->ci }}</td>
        </tr>
        <tr>
            <td class="label">Categoría:</td>
            <td class="value">{{ $athlete->category->nombre ?? 'Sin Categoría' }}</td>
        </tr>
        <tr>
            <td class="label">Fecha de Nacimiento:</td>
            <td class="value">{{ $athlete->fecha_nacimiento ? $athlete->fecha_nacimiento->format('d/m/Y') : 'No registrada' }}</td>
        </tr>
        <tr>
            <td class="label">Género:</td>
            <td class="value">{{ $athlete->genero ?? 'No especificado' }}</td>
        </tr>
        <tr>
            <td class="label">Nro. Carnet Atleta:</td>
            <td class="value">{{ $athlete->nro_carnet_atleta ?? 'No asignado' }}</td>
        </tr>
        <tr>
            <td class="label">Estado Habilitación:</td>
            <td class="value">
                <span class="badge {{ $athlete->habilitado_booleano ? 'badge-si' : 'badge-no' }}">
                    {{ $athlete->habilitado_booleano ? 'HABILITADO' : 'NO HABILITADO' }}
                </span>
                @if($athlete->fecha_inicio_habilitacion && $athlete->fecha_fin_habilitacion)
                    <br><small style="color: #64748b;">(Vigente: {{ $athlete->fecha_inicio_habilitacion->format('d/m/Y') }} - {{ $athlete->fecha_fin_habilitacion->format('d/m/Y') }})</small>
                @endif
            </td>
        </tr>
    </table>

    <div class="section-title">Información Médica y Emergencia</div>
    <table class="info-grid">
        <tr>
            <td class="label">Alergias / Condiciones:</td>
            <td class="value">{{ $athlete->alergias ?? 'Ninguna especificada' }}</td>
        </tr>
        <tr>
            <td class="label">Tiene Seguro Médico:</td>
            <td class="value">
                <span class="badge {{ $athlete->tiene_seguro ? 'badge-si' : 'badge-no' }}">
                    {{ $athlete->tiene_seguro ? 'SÍ' : 'NO' }}
                </span>
            </td>
        </tr>
        @if($athlete->tiene_seguro)
        <tr>
            <td class="label">Aseguradora:</td>
            <td class="value">{{ $athlete->seguro_compania ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Contacto del Seguro:</td>
            <td class="value">{{ $athlete->seguro_contacto ?? 'N/A' }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Contacto Emergencia:</td>
            <td class="value">{{ $athlete->contacto_nombre ?? 'N/A' }} ({{ $athlete->contacto_relacion ?? 'Relación N/A' }})</td>
        </tr>
        <tr>
            <td class="label">Teléfono Emergencia:</td>
            <td class="value">{{ $athlete->contacto_telefono ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Información del Tutor / Padre</div>
    <table class="info-grid">
        <tr>
            <td class="label">Nombre del Padre/Tutor:</td>
            <td class="value">{{ $athlete->nombre_padre ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <td class="label">Relación con Atleta:</td>
            <td class="value">{{ $athlete->relacion_contacto ?? 'Padre/Tutor' }}</td>
        </tr>
        <tr>
            <td class="label">Teléfono del Tutor:</td>
            <td class="value">{{ $athlete->telefono_padre ?? 'No registrado' }}</td>
        </tr>
    </table>

    <div class="footer">
        Club Olympic Santa Cruz &copy; {{ date('Y') }} &middot; Documento Oficial de Control Interno
    </div>
</body>
</html>
