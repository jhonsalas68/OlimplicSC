<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OlimpicSC — Acceso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            background: #0f172a;
        }

        /* Panel izquierdo — decorativo */
        .left-panel {
            width: 70%;
            background-color: #ffffff; /* Fondo blanco para fusionarse con la imagen */
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .left-panel-img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Asegura que la imagen completa se vea sin cortes */
            object-position: center;
        }
        
        /* Panel derecho — formulario */
        .right-panel {
            width: 30%;
            min-width: 400px;
            flex-shrink: 0;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 40px;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
            z-index: 10;
        }
        .form-header { margin-bottom: 36px; text-align: center; }
        .form-header img { width: auto; max-width: 160px; height: auto; margin: 0 auto 16px; display: block; }
        .form-header h1 { font-size: 24px; font-weight: 800; color: #0b2d69; }
        .form-header p { font-size: 13px; color: #64748b; margin-top: 6px; }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .input-wrap { position: relative; }
        .input-wrap svg {
            position: absolute;
            left: 14px; top: 50%; transform: translateY(-50%);
            width: 18px; height: 18px;
            color: #94a3b8;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            padding: 13px 14px 13px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s;
            outline: none;
        }
        .input-wrap input:focus {
            border-color: #0b2d69;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(11,45,105,0.1);
        }
        .input-wrap input::placeholder { color: #94a3b8; }

        .error-msg {
            font-size: 12px;
            color: #dc2626;
            font-weight: 500;
            margin-top: 6px;
            display: flex; align-items: center; gap: 4px;
        }

        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 28px;
        }
        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #0b2d69;
            cursor: pointer;
        }
        .remember-row label {
            font-size: 13px; color: #64748b; cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0b2d69, #091a3a);
            color: white;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.3px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #c61c2c, #a01522);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(198,28,44,0.35);
        }
        .btn-submit:active { transform: translateY(0); }

        .form-footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }
        .contact-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .contact-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            padding: 9px 12px;
            border-radius: 10px;
            transition: background 0.15s;
        }
        .contact-link:hover { background: #f8fafc; }
        .contact-link .icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .icon-wa  { background: #dcfce7; }
        .icon-mail { background: #dbeafe; }
        .icon-map  { background: #fee2e2; }
        .contact-link svg { width: 16px; height: 16px; }
        .icon-wa  svg { color: #16a34a; }
        .icon-mail svg { color: #2563eb; }
        .icon-map  svg { color: #dc2626; }
        .contact-link span {
            font-size: 12px;
            color: #475569;
            font-weight: 500;
            line-height: 1.3;
        }

        /* Responsive */
        @media (max-width: 900px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; height: 250px; background-position: center; }
            .right-panel { width: 100%; min-width: auto; padding: 32px 24px; border-radius: 24px 24px 0 0; margin-top: -24px; box-shadow: 0 -10px 30px rgba(0,0,0,0.1); }
        }
    </style>
</head>
<body>

    {{-- PANEL IZQUIERDO --}}
    <div class="left-panel">
        <img src="{{ asset('images/banner-login.jpg') }}" alt="Banner OlimpicSC" class="left-panel-img">
    </div>

    {{-- PANEL DERECHO --}}
    <div class="right-panel">
        <div class="form-header">
            <h1>Bienvenido</h1>
            <p>Ingresa tus credenciales para acceder al sistema</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="username">Usuario</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input id="username" name="username" type="text"
                           value="{{ old('username') }}"
                           placeholder="Tu nombre de usuario"
                           autocomplete="username" required>
                </div>
                @error('username')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input id="password" name="password" type="password"
                           placeholder="Tu contraseña"
                           autocomplete="current-password" required>
                </div>
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Mantener sesión iniciada</label>
            </div>

            <button type="submit" class="btn-submit">
                Ingresar al Sistema
            </button>
        </form>

        <div class="form-footer">
            <div class="contact-links">
                <a href="https://wa.me/59169039107" target="_blank" class="contact-link">
                    <div class="icon icon-wa">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.855L.057 23.882l6.186-1.443A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.371l-.36-.214-3.724.868.936-3.42-.235-.372A9.818 9.818 0 1112 21.818z"/>
                        </svg>
                    </div>
                    <span>+591 69039107</span>
                </a>

                <a href="https://mail.google.com/mail/?view=cm&to=club.olympic.sc@gmail.com" target="_blank" class="contact-link">
                    <div class="icon icon-mail">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span>club.olympic.sc@gmail.com</span>
                </a>

                <a href="https://maps.app.goo.gl/byQEvqhF4Dsu14pN8" target="_blank" class="contact-link">
                    <div class="icon icon-map">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span>Calle Ignacio Salvatierra, 4to Anillo y Radial 27, Santa Cruz</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
