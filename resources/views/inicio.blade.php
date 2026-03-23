<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Olympic Santa Cruz</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            width: 100%;
            font-family: 'Segoe UI', Arial, sans-serif;
            overflow: hidden;
        }

        /* FONDO PANTALLA COMPLETA */
        .hero {
            position: fixed;
            inset: 0;
            background-image: url('{{ asset("images/bg-inicio.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-color: #0a1628;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        /* Overlay */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(8, 16, 48, 0.55) 0%,
                rgba(8, 16, 48, 0.30) 50%,
                rgba(60, 8, 8, 0.65) 100%
            );
            z-index: 0;
        }

        /* LOGO HERO — ocupa toda la parte superior */
        .logo-hero {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 40px 24px 20px;
        }

        .logo-hero img {
            /* Ocupa hasta el 70% del alto de la pantalla, respetando el ancho */
            max-height: 70vh;
            max-width: min(480px, 90vw);
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 8px 40px rgba(0,0,0,0.7));
        }

        /* PARTE INFERIOR — botón + contacto */
        .bottom-section {
            position: relative;
            z-index: 10;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 24px 48px;
            gap: 20px;
        }

        .btn-login {
            background: linear-gradient(135deg, #1d3461 0%, #7b1a1a 100%);
            color: white;
            text-decoration: none;
            padding: 16px 52px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.2s;
            box-shadow: 0 6px 28px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 36px rgba(0,0,0,0.6);
            background: linear-gradient(135deg, #254080 0%, #8f2020 100%);
        }
        .btn-login svg { width: 18px; height: 18px; flex-shrink: 0; }

        .contact-line {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        /* RESPONSIVE */
        @media (max-width: 480px) {
            .logo-hero img {
                max-height: 62vh;
            }
            .btn-login {
                padding: 14px 40px;
                font-size: 15px;
            }
            .bottom-section {
                padding-bottom: 36px;
            }
        }
    </style>
</head>
<body>

<div class="hero">

    {{-- LOGO GRANDE --}}
    <div class="logo-hero">
        <img src="{{ asset('images/logo-olimpicsc-full.png') }}" alt="Club Olympic Santa Cruz">
    </div>

    {{-- BOTÓN + CONTACTO --}}
    <div class="bottom-section">
        <a href="{{ route('login') }}" class="btn-login">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Iniciar Sesión
        </a>
        <p class="contact-line">Santa Cruz, Bolivia &middot; +591 69039107</p>
    </div>

</div>

</body>
</html>
