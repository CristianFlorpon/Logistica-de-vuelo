<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Aerobooking</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ── Carrusel pantalla completa ── */
        #carruselHero,
        #carruselHero .carousel-inner,
        #carruselHero .carousel-item {
            height: 100vh;
        }

        #carruselHero .carousel-item img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
            object-position: center;
        }

        /* ── Overlay oscuro ── */
        .hero-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.55);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        /* ── Navbar flotante ── */
        .navbar-hero {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            z-index: 30;
            padding: 1rem 2rem;
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(6px);
        }

        /* ── Alerta fija arriba ── */
        .alerta-top {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            z-index: 50;
            background: #1e2a3a;
            color: #d1d5db;
            font-size: 0.88rem;
            padding: 0.6rem 1.2rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            border-bottom: 1px solid #2d3f55;
        }

        .alerta-top a {
            color: #60a5fa;
        }

        .alerta-top .btn-close-alerta {
            margin-left: auto;
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 1.1rem;
            cursor: pointer;
            flex-shrink: 0;
            padding: 0 0.2rem;
        }

        .alerta-top .btn-close-alerta:hover {
            color: #fff;
        }

        body {
            margin: 0; padding: 0;
        }

        /* ── Animación de entrada ── */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 1.5s ease;
        }

        /* ── Flechas y puntitos sobre el overlay ── */
        .carousel-control-prev,
        .carousel-control-next,
        .carousel-indicators {
            z-index: 20;
        }
    </style>
</head>

<body class="bg-dark text-white m-0 p-0">

<!-- ══════════════════════════════════════
     ALERTA INFORMATIVA (fija arriba)
══════════════════════════════════════ -->
<div class="alerta-top" id="alertaInfo">
    <i class="bi bi-exclamation-circle-fill text-warning mt-1 flex-shrink-0"></i>
    <span>
        <strong>Importante:</strong> Pasajeros bolivianos menores de 10 años deben presentar
        constancia de vacunación contra el sarampión para vuelos nacionales e internacionales.
        Más información en la sección
        <a href="#">documentos de viaje</a>
        o a través de nuestros canales de atención.
    </span>
    <button class="btn-close-alerta" onclick="cerrarAlerta()" title="Cerrar">
        <i class="bi bi-x-lg"></i>
    </button>
</div>

<!-- ══════════════════════════════════════
     WRAPPER (empujado por la alerta)
══════════════════════════════════════ -->
<div id="wrapper">

    <!-- ── CARRUSEL CON FADE ── -->
    <div id="carruselHero" class="carousel slide carousel-fade position-relative" data-bs-ride="carousel" data-bs-interval="4500">

        <!-- Indicadores -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carruselHero" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carruselHero" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carruselHero" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <!-- Imágenes -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://www.cladera.org/canvas/images/imagemodelo/canvas-227.jpg" alt="Vuelo 1">
            </div>
            <div class="carousel-item">
                <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEg_H1IugEi7O2krFWUMBDOhPIUli6GO8pqUlFOV9tCTbkWJW55cUb_WQzx1QK34XtLQTvLH3FR2XL4WDzhVVMQ3vZmSSbqsA6MvK5F1QiJr5yPAHXrt_6dBjIVU5dtrQVNMdLTrUUhkv4CwCfhXD-rQPBl9-jBmGI-GXPL69U8BPm_qsVD4qHSeAaD-2J7Q/s1920/Avi%C3%B3n%20de%20BoA%20-%20Travel%20Services.jpg" alt="Vuelo 2">
            </div>
            <div class="carousel-item">
                <img src="https://cladera.org/foda/images/subcat-1336.jpg" alt="Vuelo 3">
            </div>
        </div>

        <!-- Flechas -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carruselHero" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carruselHero" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>

        <!-- ── NAVBAR flotante (botones arriba derecha) ── -->
        <nav class="navbar-hero d-flex align-items-center justify-content-between">
            <span class="text-white fw-bold fs-5">✈️ Aerobooking</span>
            <div class="d-flex gap-2">
                <a href="/login"  class="btn btn-outline-light btn-sm px-4 py-2">Iniciar Sesión</a>
                <a href="/register" class="btn btn-success btn-sm px-4 py-2">Registrarse</a>
            </div>
        </nav>

        <!-- ── OVERLAY con título centrado ── -->
        <div class="hero-overlay">
            <div class="text-center animate-fade-in px-3">
                <h1 class="display-3 fw-bold mb-3"> Aerobooking</h1>
                <p class="fs-5 text-white-50 mb-0">
                    Donde cada vuelo comienza con un clic.
                </p>
            </div>
        </div>

    </div>
    <!-- ── FIN CARRUSEL ── -->

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function ajustarWrapper() {
        const alerta = document.getElementById('alertaInfo');
        const wrapper = document.getElementById('wrapper');
        if (alerta && alerta.style.display !== 'none') {
            wrapper.style.paddingTop = alerta.offsetHeight + 'px';
        } else {
            wrapper.style.paddingTop = '0';
        }
    }

    function cerrarAlerta() {
        document.getElementById('alertaInfo').style.display = 'none';
        ajustarWrapper();
    }

    ajustarWrapper();
    window.addEventListener('resize', ajustarWrapper);
</script>

</body>
</html>