<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #1e293b; }
    .header { background: #1e3a8a; color: white; padding: 10px 14px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }
    .header h1 { font-size: 14px; font-weight: bold; }
    .header p { font-size: 8px; opacity: 0.8; margin-top: 2px; }
    .header-right { text-align: right; font-size: 8px; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1e3a8a; color: white; }
    thead th { padding: 6px 5px; text-align: left; font-size: 8px; font-weight: bold; }
    tbody tr:nth-child(even) { background: #f0f4ff; }
    tbody tr:nth-child(odd) { background: #ffffff; }
    tbody td { padding: 5px; border-bottom: 1px solid #e2e8f0; font-size: 8px; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 7px; font-weight: bold; }
    .badge-si { background: #dcfce7; color: #15803d; }
    .badge-no { background: #fee2e2; color: #dc2626; }
    .footer { margin-top: 12px; text-align: center; font-size: 7px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; }
</style>
</head>
<body>
<div class="header">
    <div>
        <h1>Club OlimpicSC — Reporte de Atletas</h1>
        <p>Total: {{ $atletas->count() }} atletas registrados</p>
    </div>
    <div class="header-right">
        <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
        <p>club.olympic.sc@gmail.com</p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Código</th>
            <th>Nombre Completo</th>
            <th>CI</th>
            <th>Categoría</th>
            <th>Fecha Nac.</th>
            <th>Género</th>
            <th>Contacto</th>
            <th>Habilitado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($atletas as $i => $a)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $a->id_alfanumerico_unico }}</td>
            <td>{{ $a->nombre }} {{ $a->apellido_paterno }} {{ $a->apellido_materno }}</td>
            <td>{{ $a->ci }}</td>
            <td>{{ $a->category->nombre ?? '—' }}</td>
            <td>{{ $a->fecha_nacimiento ? $a->fecha_nacimiento->format('d/m/Y') : '—' }}</td>
            <td>{{ $a->genero ?? '—' }}</td>
            <td>{{ $a->contacto_telefono ?? $a->telefono_padre ?? '—' }}</td>
            <td><span class="badge {{ $a->habilitado_booleano ? 'badge-si' : 'badge-no' }}">{{ $a->habilitado_booleano ? 'SÍ' : 'NO' }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Club OlimpicSC · Santa Cruz, Bolivia · +591 69039107 · Reporte generado automáticamente
</div>
</body>
</html>
