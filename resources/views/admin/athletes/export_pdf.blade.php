<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lista de Convocados - OlimpicSC</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0b2d69; padding-bottom: 10px; }
        .logo-text { font-size: 24px; font-weight: bold; color: #0b2d69; }
        .title { font-size: 18px; margin-top: 5px; color: #c61c2c; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #0b2d69; color: white; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 10px 0; }
        .category-badge { background: #f0f4f8; padding: 2px 8px; border-radius: 4px; font-weight: bold; color: #0b2d69; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-text">OLIMPIC SC</div>
        <div class="title">Lista de Convocados</div>
        <div style="margin-top: 5px;">Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>C.I.</th>
                <th>Categoría</th>
                <th>Género</th>
            </tr>
        </thead>
        <tbody>
            @foreach($athletes as $athlete)
            <tr>
                <td style="font-weight: bold;">{{ $athlete->nombre }} {{ $athlete->apellido_paterno }} {{ $athlete->apellido_materno }}</td>
                <td>{{ $athlete->ci }}</td>
                <td><span class="category-badge">{{ $athlete->category->nombre ?? 'N/A' }}</span></td>
                <td>{{ $athlete->genero }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        OlimpicSC &copy; {{ date('Y') }} &middot; Sistema de Gestión Administrativa
    </div>
</body>
</html>
