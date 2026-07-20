<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Atletas - OlimpicSC</title>
    <style>
        @page { margin: 20px; size: landscape; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8px; color: #333; line-height: 1.2; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; }
        .logo-text { font-size: 18px; font-weight: bold; color: #1e3a5f; }
        .title { font-size: 14px; margin-top: 3px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background-color: #1e3a5f; color: white; padding: 5px 3px; text-align: left; text-transform: uppercase; font-size: 7px; border: 1px solid #1e3a5f; }
        td { padding: 4px 3px; border: 1px solid #e2e8f0; word-wrap: break-word; vertical-align: top; }
        .bg-gray { background-color: #f8fafc; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 7px; color: #94a3b8; }
        .status-badge { font-weight: bold; padding: 2px 4px; border-radius: 3px; display: inline-block; }
        .yes { color: #10b981; }
        .no { color: #ef4444; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-text">OLIMPIC SC</div>
        <div class="title">Reporte General de Datos de Atletas</div>
        <div style="font-size: 8px; margin-top: 4px;">Generado el: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Nombres</th>
                <th style="width: 8%;">Apellidos</th>
                <th style="width: 5%;">C.I.</th>
                <th style="width: 6%;">Categoría</th>
                <th style="width: 5%;">Nac.</th>
                <th style="width: 4%;">Gen.</th>
                <th style="width: 7%;">Alergias</th>
                <th style="width: 3%;">Seg.</th>
                <th style="width: 7%;">Aseguradora</th>
                <th style="width: 6%;">Tutor</th>
                <th style="width: 5%;">Rel.</th>
                <th style="width: 6%;">Tel. Tutor</th>
                <th style="width: 7%;">Cont. Emer.</th>
                <th style="width: 6%;">Rel.</th>
                <th style="width: 6%;">Tel. Emer.</th>
                <th style="width: 3%;">Hab.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($athletes as $athlete)
            <tr class="{{ $loop->index % 2 == 0 ? '' : 'bg-gray' }}">
                <td style="font-weight: bold;">{{ $athlete->nombre }}</td>
                <td>{{ $athlete->apellido_paterno }} {{ $athlete->apellido_materno }}</td>
                <td>{{ $athlete->ci }}</td>
                <td>{{ $athlete->category->nombre ?? 'N/A' }}</td>
                <td>{{ $athlete->fecha_nacimiento?->format('d/m/y') }}</td>
                <td>{{ substr($athlete->genero, 0, 1) }}</td>
                <td>{{ $athlete->alergias ?? '-' }}</td>
                <td class="{{ $athlete->tiene_seguro ? 'yes' : 'no' }} font-bold">{{ $athlete->tiene_seguro ? 'SÍ' : 'NO' }}</td>
                <td>{{ $athlete->seguro_compania ?? '-' }} <br> <small>{{ $athlete->seguro_contacto }}</small></td>
                <td>{{ $athlete->nombre_padre }}</td>
                <td>{{ $athlete->relacion_contacto ?? '-' }}</td>
                <td>{{ $athlete->telefono_padre }}</td>
                <td>{{ $athlete->contacto_nombre ?? '-' }}</td>
                <td>{{ $athlete->contacto_relacion ?? '-' }}</td>
                <td>{{ $athlete->contacto_telefono ?? '-' }}</td>
                <td class="{{ $athlete->habilitado_booleano ? 'yes' : 'no' }}">{{ $athlete->habilitado_booleano ? 'SÍ' : 'NO' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        OlimpicSC &copy; {{ date('Y') }} &middot; Documento de Control Interno &middot; Página 1
    </div>
</body>
</html>
