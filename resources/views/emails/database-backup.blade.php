<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; rounded: 12px; }
        .header { background: #1e3a8a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 30px; background: #ffffff; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #94a3b8; }
        .badge { background: #f1f5f9; padding: 4px 12px; border-radius: 9999px; font-weight: bold; color: #1e3a8a; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;">OlimpicSC Backup</h1>
        </div>
        <div class="content">
            <h2>Respaldo de Seguridad Generado</h2>
            <p>Se ha generado un nuevo respaldo completo de la base de datos de <strong>OlimpicSC</strong>.</p>
            
            <div style="margin: 25px 0; padding: 20px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #1e3a8a;">
                <p style="margin: 0; font-size: 14px;"><strong>Información del Archivo:</strong></p>
                <p style="margin: 5px 0 0 0; font-family: monospace;">SQL Dump adjunto en este correo.</p>
                <p style="margin: 10px 0 0 0;"><span class="badge">{{ now()->format('d/m/Y H:i') }}</span></p>
            </div>

            <p>Se recomienda guardar este archivo en una ubicación segura. Este respaldo contiene toda la información de atletas, pagos, planes de entrenamiento y usuarios.</p>
            
            <p style="font-size: 13px; color: #64748b;"><em>Nota: Si no solicitaste este respaldo, por favor revisa el historial de actividad en el panel de SuperAdmin.</em></p>
        </div>
        <div class="footer">
            Club OlimpicSC &copy; {{ date('Y') }} · Sistema de Gestión Deportiva
        </div>
    </div>
</body>
</html>
