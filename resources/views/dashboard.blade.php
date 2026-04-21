<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - OlimpicSC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f8fafc;
            display: flex;
            min-height: 100vh;
        }

        /* =====================
           OVERLAY (mobile)
           ===================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .sidebar-overlay.active { display: block; }

        /* =====================
           SIDEBAR
           ===================== */
        .sidebar {
            width: 72px;
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow: hidden;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
        }

        .sidebar:hover { width: 240px; }

        .sidebar-header {
            padding: 16px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            background: rgba(0,0,0,0.15);
            min-height: 72px;
        }

        .logo {
            width: 44px;
            height: 44px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .logo img { width: 100%; height: 100%; object-fit: contain; padding: 6px; }

        .company-name {
            color: white;
            font-size: 15px;
            font-weight: 700;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .sidebar:hover .company-name { opacity: 1; }

        .sidebar-nav {
            padding: 12px 0;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }

        .nav-section-label {
            color: rgba(255,255,255,0.35);
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 12px 20px 4px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .sidebar:hover .nav-section-label { opacity: 1; }

        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,0.08);
            margin: 8px 12px;
        }

        .nav-item {
            color: rgba(255,255,255,0.7);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            text-decoration: none;
            margin: 2px 8px;
            border-radius: 8px;
            white-space: nowrap;
        }

        .nav-item:hover { background: rgba(255,255,255,0.1); color: white; border-left-color: #dc2626; }
        .nav-item.active { background: rgba(255,255,255,0.15); border-left-color: #dc2626; color: white; font-weight: 600; }

        .nav-icon {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            font-size: 16px;
            color: #fca5a5;
            transition: color 0.2s ease;
        }

        .nav-item:hover .nav-icon,
        .nav-item.active .nav-icon { color: #dc2626; }

        .nav-label {
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.2s ease;
            overflow: hidden;
        }

        .sidebar:hover .nav-label { opacity: 1; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            background: rgba(0,0,0,0.1);
        }

        .sidebar-footer-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .sidebar-footer-info { opacity: 0; transition: opacity 0.2s ease; overflow: hidden; }
        .sidebar:hover .sidebar-footer-info { opacity: 1; }
        .sidebar-footer-name { color: white; font-size: 13px; font-weight: 600; white-space: nowrap; }
        .sidebar-footer-role { color: rgba(255,255,255,0.5); font-size: 11px; white-space: nowrap; }

        /* =====================
           MAIN CONTENT
           ===================== */
        .main-content {
            flex: 1;
            margin-left: 72px;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 24px;
            padding-bottom: 80px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebar:hover ~ .main-content { margin-left: 240px; }

        /* Header */
        .header {
            background: white;
            padding: 14px 20px;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
            position: relative;
            z-index: 1;
            gap: 12px;
            flex-wrap: wrap;
        }

        .header-left { display: flex; align-items: center; gap: 12px; }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #1e3a8a;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
        }

        .header h1 { font-size: clamp(16px, 3vw, 22px); color: #1e3a8a; font-weight: 700; }

        .user-menu { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

        .user-info p { color: #6b7280; font-size: 13px; text-align: right; }
        .user-info strong { color: #111827; }

        .logout-btn {
            padding: 8px 14px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .logout-btn:hover { background: #b91c1c; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(220,38,38,0.3); }

        /* Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr));
            gap: 16px;
            position: relative;
            z-index: 1;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); border-color: #bfdbfe; }

        .card-icon {
            font-size: 22px;
            color: #1e3a8a;
            margin-bottom: 12px;
            width: 44px;
            height: 44px;
            background: #eff6ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card h3 { color: #111827; margin-bottom: 6px; font-size: 14px; font-weight: 600; }
        .card p { color: #6b7280; font-size: 13px; }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: calc(50% + 36px);
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
            opacity: 0.06;
        }

        .watermark img { width: clamp(200px, 35vw, 420px); filter: grayscale(100%); }

        /* Footer contactos */
        .contact-footer {
            position: fixed;
            bottom: 0;
            left: 72px;
            right: 0;
            background: linear-gradient(135deg, #1e3a8a 0%, #dc2626 100%);
            border-radius: 10px 10px 0 0;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
            z-index: 998;
            transition: left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .contact-divider { width: 1px; height: 24px; background: rgba(255,255,255,0.25); flex-shrink: 0; }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .contact-item:hover { opacity: 0.8; }
        .contact-item i { font-size: 15px; color: rgba(255,255,255,0.85); width: 18px; text-align: center; flex-shrink: 0; }
        .contact-item span { font-size: 12px; font-weight: 500; }

        /* =====================
           RESPONSIVE
           ===================== */

        /* Tablet landscape */
        @media (max-width: 1024px) {
            .main-content { padding: 16px; }
            .sidebar:hover ~ .main-content { margin-left: 240px; }
        }

        /* Tablet portrait */
        @media (max-width: 768px) {
            .sidebar {
                width: 240px;
                left: -240px;
                transition: left 0.25s cubic-bezier(0.4, 0, 0.2, 1), width 0.25s;
            }

            .sidebar:hover { width: 240px; left: -240px; }
            .sidebar.open { left: 0; }
            .sidebar.open .company-name { opacity: 1; }
            .sidebar.open .nav-label { opacity: 1; }
            .sidebar.open .nav-section-label { opacity: 1; }
            .sidebar.open .sidebar-footer-info { opacity: 1; }

            .main-content { margin-left: 0 !important; padding: 12px; padding-bottom: 80px; }
            .sidebar:hover ~ .main-content { margin-left: 0; }

            .menu-toggle { display: block; }

            .header { padding: 12px 16px; }
            .header h1 { font-size: 18px; }

            .user-info { display: none; }

            .dashboard-grid { grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 12px; }

            .contact-footer { left: 0 !important; gap: 16px; padding: 10px 16px; }
            .contact-divider { display: none; }
            .contact-item span { font-size: 11px; }

            .watermark { left: 50%; }
        }

        /* Mobile */
        @media (max-width: 480px) {
            .main-content { padding: 10px; }

            .header { padding: 10px 12px; margin-bottom: 16px; }
            .header h1 { font-size: 16px; }

            .dashboard-grid { grid-template-columns: 1fr 1fr; gap: 10px; }

            .card { padding: 14px; }
            .card-icon { width: 38px; height: 38px; font-size: 18px; margin-bottom: 8px; }
            .card h3 { font-size: 13px; }
            .card p { font-size: 11px; }

            .contact-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 14px 16px;
            }

            .contact-item span { font-size: 12px; white-space: normal; }

            .logout-btn span { display: none; }
            .logout-btn { padding: 8px 10px; }
        }

        /* Very small screens */
        @media (max-width: 360px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Overlay mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="{{ asset('images/logo-olimpic-sc.svg') }}" alt="OlimpicSC"
                     onerror="this.style.display='none';this.parentElement.innerHTML='<span style=\'color:#1e3a8a;font-weight:700;font-size:18px\'>O</span>'">
            </div>
            <span class="company-name">OlimpicSC</span>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-section-label">Principal</span>
            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/            <a href="{{ url('/dashboard') }}" class="nav-item active">
                <span class="nav-icon"><i class="fas fa-home"></i></span>
                <span class="nav-label">Panel de Control</span>
            </a>') }}" class="nav-item active">
                <span class="nav-icon"><i class="fas fa-home"></i></span>
                <span class="nav-label">Panel de Control</span>
            </a>

            <div class="nav-divider"></div>
            <span class="nav-section-label">Gestión Administrativa</span>
            @if(Auth::user()->hasRole(['SuperAdmin', 'Administrador', 'Admin']))
            <a href="{{ url('/admin-panel/users') }}" class="nav-item">
                <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
                <span class="nav-label">Usuarios y Roles</span>
            </a>
            @endif
            <a href="{{ url('/admin-panel/athletes') }}" class="nav-item">
                <span class="nav-icon"><i class="fas fa-running"></i></span>
                <span class="nav-label">Atletas Olimpic</span>
            </a>
            
            <div class="nav-divider"></div>
            <span class="nav-section-label">Cancha y Tesorería</span>
            <a href="{{ url('/admin-panel/trainings') }}" class="nav-item">
                <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                <span class="nav-label">Planificaciones Semanales</span>
            </a>
            <a href="{{ url('/admin-panel/payments') }}" class="nav-item">
                <span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                <span class="nav-label">Pagos y Recibos</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-avatar"><i class="fas fa-user"></i></div>
            <div class="sidebar-footer-info">
                <div class="sidebar-footer-name">{{ Auth::user()->name ?? 'Usuario' }}</div>
                <div class="sidebar-footer-role">Administrador</div>
            </div>
        </div>
    </aside>

    <!-- Watermark -->
    <div class="watermark">
        <img src="{{ asset('images/logo-olimpic-sc.svg') }}" alt=""
             onerror="this.parentElement.style.display='none'">
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Panel de Control</h1>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <p><strong>{{ Auth::user()->name }}</strong></p>
                    <p>{{ Auth::user()->email }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Cards -->
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h3>Usuarios Activos</h3>
                <p>0 usuarios registrados en el sistema</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-calendar"></i></div>
                <h3>Próximos Eventos</h3>
                <p>No hay eventos programados</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-trophy"></i></div>
                <h3>Competencias Activas</h3>
                <p>0 competencias en curso</p>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-chart-bar"></i></div>
                <h3>Estadísticas</h3>
                <p>Visualiza las estadísticas del sistema</p>
            </div>
        </div>
    </div>

    <!-- Footer contactos (fixed) -->
    <footer class="contact-footer" id="contactFooter">
        <a class="contact-item" href="https://wa.me/59169039107?text=Hola%2C%20me%20comunico%20desde%20el%20panel%20de%20Olympic%20SC" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp"></i>
            <span>+591 69039107</span>
        </a>
        <div class="contact-divider"></div>
        <a class="contact-item" href="https://mail.google.com/mail/?view=cm&to=club.olympic.sc@gmail.com&su=Contacto%20Olympic%20SC" target="_blank" rel="noopener">
            <i class="fas fa-envelope"></i>
            <span>club.olympic.sc@gmail.com</span>
        </a>
        <div class="contact-divider"></div>
        <a class="contact-item" href="https://maps.app.goo.gl/Ff4booYPdt4TbZ2s9" target="_blank" rel="noopener">
            <i class="fas fa-map-marker-alt"></i>
            <span>Calle Ignacio Salvatierra, Cuarto Anillo y Radial 27, Santa Cruz – Bolivia</span>
        </a>
    </footer>

    <script>
        // Evitar inicialización múltiple con Turbo
        if (!window.dashboardInitialized) {
            window.dashboardInitialized = true;

            // Nav active state
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', e => {
                    if (item.getAttribute('href') === '#') {
                        e.preventDefault();
                    }
                    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                });
            });

            // Mobile sidebar toggle
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle');
            const footer = document.getElementById('contactFooter');

            // Desktop: mover footer cuando sidebar hace hover
            sidebar.addEventListener('mouseenter', () => {
                if (window.innerWidth > 768) footer.style.left = '240px';
            });
            sidebar.addEventListener('mouseleave', () => {
                if (window.innerWidth > 768) footer.style.left = '72px';
            });

            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }
    </script>
</body>
</html>
