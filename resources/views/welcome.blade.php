<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Olímpico</title>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
        .hero { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; background: linear-gradient(135deg, #0000FF 0%, #000066 100%); color: white; text-align: center; }
        .logo-container { background: white; padding: 2rem; border-radius: 50%; width: 200px; height: 200px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; border: 4px solid #FF0000; box-shadow: 0 10px 25px rgba(0,0,0,0.5); overflow: hidden; }
        .logo-container svg { width: 100%; height: auto; }
        .title { font-size: 3.5rem; font-weight: 800; margin: 0; letter-spacing: -1px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .subtitle { font-size: 1.25rem; font-weight: 300; opacity: 0.9; margin-top: 1rem; margin-bottom: 3rem; }
        .btn-login { background-color: #FF0000; color: white; font-weight: bold; font-size: 1.25rem; padding: 1rem 3rem; border-radius: 9999px; text-decoration: none; transition: transform 0.2s, background-color 0.2s; box-shadow: 0 4px 6px rgba(255, 0, 0, 0.3); }
        .btn-login:hover { background-color: #cc0000; transform: translateY(-2px); }
        .header-login { position: absolute; top: 2rem; right: 2rem; }
    </style>
</head>
<body>
    <div class="header-login">
        <a href="{{ route('login') }}" class="btn-login" style="padding: 0.5rem 1.5rem; font-size: 1rem;">Iniciar Sesión</a>
    </div>
    <div class="hero">
        <div class="logo-container">
            <x-logo />
        </div>
        <h1 class="title">Club Deportivo Olímpico</h1>
        <p class="subtitle">Plataforma exclusiva de gestión integral.</p>
        <a href="{{ route('login') }}" class="btn-login">Ingresar al Sistema</a>
    </div>
</body>
</html>
