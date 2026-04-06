<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logistica Aerea</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
body{margin:0;font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;background:linear-gradient(145deg,#050b14,#0b1325 45%,#07111f);color:#e2e8f0}
.card{background:rgba(15,23,42,.86);border:1px solid rgba(148,163,184,.16);border-radius:20px;box-shadow:0 18px 40px rgba(2,6,23,.22)}
.input,.select{width:100%;padding:11px 13px;border-radius:12px;border:1px solid rgba(148,163,184,.18);background:rgba(15,23,42,.8);color:#e2e8f0}
.btn-primary,.btn-secondary,.btn-danger{border:0;border-radius:12px;padding:10px 14px;font-weight:700}
.btn-primary{background:#38bdf8;color:#082032}
.btn-secondary{background:rgba(148,163,184,.14);color:#e2e8f0}
.btn-danger{background:rgba(248,113,113,.18);color:#fecaca}
.nav-btn{width:100%;padding:11px 13px;border-radius:12px;background:transparent;border:1px solid transparent;color:#e2e8f0;text-align:left}
.nav-btn.active,.nav-btn:hover{background:rgba(56,189,248,.12);border-color:rgba(56,189,248,.18)}
.badge{display:inline-flex;padding:5px 10px;border-radius:999px;font-size:12px;font-weight:700;text-transform:uppercase}
.table-wrap{overflow:auto;border-radius:16px;border:1px solid rgba(148,163,184,.12)}
table{width:100%;border-collapse:collapse;min-width:760px}
th,td{padding:13px 15px;border-bottom:1px solid rgba(148,163,184,.08);vertical-align:top}
th{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;background:rgba(15,23,42,.9)}
.hint{color:#94a3b8;font-size:13px}
.bar{height:10px;border-radius:999px;background:rgba(148,163,184,.12);overflow:hidden}
.bar>span{display:block;height:100%;border-radius:999px;background:linear-gradient(90deg,#0ea5e9,#22d3ee)}
.leaflet-container{background:#081625;border-radius:18px}
.leaflet-control-attribution{background:rgba(8,17,29,.8)!important;color:#cbd5e1!important}
.leaflet-popup-content-wrapper,.leaflet-popup-tip{background:#0f172a;color:#e2e8f0}
.leaflet-div-icon{background:transparent;border:0}
.plane-badge{width:34px;height:34px;border-radius:999px;background:rgba(56,189,248,.2);border:1px solid rgba(125,211,252,.55);display:flex;align-items:center;justify-content:center;color:#f8fafc;font-size:18px;box-shadow:0 0 0 6px rgba(56,189,248,.08)}
</style>
</head>
<body>
<script>
window.LOGISTICA_CONFIG = {
    flightStates: @json(\App\Models\Vuelo::ESTADOS),
    reservationClasses: @json(\App\Models\Reserva::CLASES),
    reservationStates: @json(\App\Models\Reserva::ESTADOS),
    userName: @json(Auth::user()->name),
};
</script>

<div class="min-h-screen lg:flex">
    <aside class="w-full lg:w-80 p-5 border-b lg:border-b-0 lg:border-r border-slate-800/80" style="background:rgba(7,17,31,.92);backdrop-filter:blur(18px)">
        <p class="text-sky-300 text-xs uppercase tracking-[0.35em] mb-2">Centro de control</p>
        <h1 class="text-3xl font-bold">Logistica Aerea</h1>
        <p class="text-slate-400 mt-3">Dashboard, vuelos, pasajeros, reservas, rutas, aeropuertos, flota, aerolineas y simulacion.</p>

        <div class="card p-4 mt-5">
            <div class="text-xs uppercase tracking-[0.3em] text-slate-400 mb-2">Sesion</div>
            <div class="font-semibold text-lg">{{ Auth::user()->name }}</div>
            <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
                <div class="rounded-2xl bg-slate-950/50 p-3 border border-slate-700/50">
                    <div class="text-slate-400 text-xs uppercase">Base</div>
                    <div class="font-semibold">logistica</div>
                </div>
                <div class="rounded-2xl bg-slate-950/50 p-3 border border-slate-700/50">
                    <div class="text-slate-400 text-xs uppercase">API</div>
                    <div class="font-semibold">Activa</div>
                </div>
            </div>
        </div>

        <nav class="card p-3 mt-5 space-y-2">
            <button class="nav-btn active" data-view-target="dashboard">Dashboard</button>
            <button class="nav-btn" data-view-target="vuelos">Vuelos</button>
            <button class="nav-btn" data-view-target="pasajeros">Pasajeros</button>
            <button class="nav-btn" data-view-target="reservas">Reservas</button>
            <button class="nav-btn" data-view-target="rutas">Rutas</button>
            <button class="nav-btn" data-view-target="aeropuertos">Aeropuertos</button>
            <button class="nav-btn" data-view-target="flota">Flota</button>
            <button class="nav-btn" data-view-target="aerolineas">Aerolineas</button>
            <button class="nav-btn" data-view-target="simulacion">Simulacion</button>
        </nav>

        <div class="card p-4 mt-5">
            <div class="font-semibold mb-3">Estados de vuelo</div>
            <div class="flex flex-wrap gap-2" id="flightStateLegend"></div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-5">
            @csrf
            <button class="btn-danger w-full">Cerrar sesion</button>
        </form>
    </aside>

    <main class="flex-1 p-4 md:p-7">
        <header class="card p-5 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">Panel principal</div>
                <h2 id="viewTitle" class="text-3xl font-bold">Dashboard operativo</h2>
                <p class="text-slate-400 mt-2">Seguimiento operativo y administracion central de la logistica aerea.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm md:w-[320px]">
                <div class="rounded-2xl border border-slate-700/50 bg-slate-950/50 p-4">
                    <div class="text-slate-400 text-xs uppercase">Servidor</div>
                    <div class="font-semibold">Laravel 10</div>
                </div>
                <div class="rounded-2xl border border-slate-700/50 bg-slate-950/50 p-4">
                    <div class="text-slate-400 text-xs uppercase">Modo</div>
                    <div class="font-semibold">Operacion</div>
                </div>
            </div>
        </header>

        <section class="view-section" data-view="dashboard">
            <div id="dashboardStats" class="grid md:grid-cols-2 xl:grid-cols-4 gap-4"></div>
            <div class="grid xl:grid-cols-2 gap-6 mt-6">
                <div class="card p-5"><div class="font-semibold mb-4">Vuelos por estado</div><div id="dashboardStates" class="space-y-4"></div></div>
                <div class="card p-5"><div class="font-semibold mb-4">Reservas por clase</div><div id="dashboardClasses" class="space-y-4"></div></div>
            </div>
            <div class="grid xl:grid-cols-2 gap-6 mt-6">
                <div class="card p-5"><div class="font-semibold mb-4">Ocupacion de vuelos</div><div id="dashboardOccupancy" class="space-y-4"></div></div>
                <div class="card p-5"><div class="font-semibold mb-4">Rutas mas usadas</div><div id="dashboardRoutes" class="space-y-4"></div></div>
            </div>
            <div class="card p-5 mt-6"><div class="font-semibold mb-4">Proximos vuelos</div><div id="dashboardUpcoming" class="space-y-3"></div></div>
        </section>

        <section class="view-section hidden" data-view="vuelos">
            <div class="grid xl:grid-cols-[360px_1fr] gap-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4"><div class="font-semibold">Registrar vuelo</div><button class="btn-secondary" type="button" onclick="Panel.resetForm('vuelos')">Limpiar</button></div>
                    <form id="form-vuelos" class="space-y-3" onsubmit="event.preventDefault(); Panel.submit('vuelos');">
                        <input type="hidden" name="id">
                        <input class="input" name="codigo" placeholder="Codigo de vuelo">
                        <select class="select" name="aerolinea_id" data-options="aerolineas"></select>
                        <select class="select" name="ruta_id" data-options="rutas" onchange="Panel.syncFlightRoute()"></select>
                        <select class="select" name="flota_id" data-options="flotas" onchange="Panel.syncFlightFleet()"></select>
                        <input class="input" name="origen" placeholder="Origen manual">
                        <input class="input" name="destino" placeholder="Destino manual">
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="asientos" type="number" min="1" placeholder="Asientos">
                            <select class="select" name="estado"></select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="salida_programada" type="datetime-local">
                            <input class="input" name="llegada_programada" type="datetime-local">
                        </div>
                        <button class="btn-primary w-full">Guardar vuelo</button>
                    </form>
                </div>
                <div class="card p-5">
                    <div class="grid md:grid-cols-[1fr_220px_auto_auto] gap-3 mb-4">
                        <input class="input" id="search-vuelos" placeholder="Buscar vuelo, ciudad o aerolinea">
                        <select class="select" id="filter-vuelos-estado"></select>
                        <button class="btn-primary" type="button" onclick="Panel.load('vuelos')">Filtrar</button>
                        <button class="btn-secondary" type="button" onclick="Panel.clearFilters('vuelos')">Limpiar</button>
                    </div>
                    <div class="table-wrap"><table><thead><tr><th>Vuelo</th><th>Ruta</th><th>Aerolinea</th><th>Estado</th><th>Horario</th><th>Ocupacion</th><th>Acciones</th></tr></thead><tbody id="table-vuelos"></tbody></table></div>
                </div>
            </div>
        </section>

        <section class="view-section hidden" data-view="pasajeros">
            <div class="grid xl:grid-cols-[320px_1fr] gap-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4"><div class="font-semibold">Registrar pasajero</div><button class="btn-secondary" type="button" onclick="Panel.resetForm('pasajeros')">Limpiar</button></div>
                    <form id="form-pasajeros" class="space-y-3" onsubmit="event.preventDefault(); Panel.submit('pasajeros');">
                        <input type="hidden" name="id">
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="nombre" placeholder="Nombre">
                            <input class="input" name="apellido" placeholder="Apellido">
                        </div>
                        <input class="input" name="documento" placeholder="Documento">
                        <input class="input" name="email" type="email" placeholder="Correo">
                        <input class="input" name="telefono" placeholder="Telefono">
                        <select class="select" name="vuelo_id" data-options="vuelos"></select>
                        <button class="btn-primary w-full">Guardar pasajero</button>
                    </form>
                </div>
                <div class="card p-5">
                    <div class="grid md:grid-cols-[1fr_auto_auto] gap-3 mb-4">
                        <input class="input" id="search-pasajeros" placeholder="Buscar pasajero">
                        <button class="btn-primary" type="button" onclick="Panel.load('pasajeros')">Filtrar</button>
                        <button class="btn-secondary" type="button" onclick="Panel.clearFilters('pasajeros')">Limpiar</button>
                    </div>
                    <div class="table-wrap"><table><thead><tr><th>Pasajero</th><th>Documento</th><th>Contacto</th><th>Vuelo</th><th>Reservas</th><th>Acciones</th></tr></thead><tbody id="table-pasajeros"></tbody></table></div>
                </div>
            </div>
        </section>

        <section class="view-section hidden" data-view="reservas">
            <div class="grid xl:grid-cols-[360px_1fr] gap-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4"><div class="font-semibold">Crear reserva</div><button class="btn-secondary" type="button" onclick="Panel.resetForm('reservas')">Limpiar</button></div>
                    <form id="form-reservas" class="space-y-3" onsubmit="event.preventDefault(); Panel.submit('reservas');">
                        <input type="hidden" name="id">
                        <input class="input" name="codigo" placeholder="Codigo de reserva">
                        <select class="select" name="pasajero_id" data-options="pasajeros" onchange="Panel.syncReservationPassenger()"></select>
                        <select class="select" name="vuelo_id" data-options="vuelos"></select>
                        <div class="grid grid-cols-2 gap-3">
                            <select class="select" name="clase"></select>
                            <select class="select" name="estado"></select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="asiento" placeholder="Asiento">
                            <input class="input" name="precio" type="number" min="0" step="0.01" placeholder="Precio">
                        </div>
                        <button class="btn-primary w-full">Guardar reserva</button>
                    </form>
                </div>
                <div class="card p-5">
                    <div class="grid md:grid-cols-[1fr_180px_180px_auto_auto] gap-3 mb-4">
                        <input class="input" id="search-reservas" placeholder="Buscar reserva">
                        <select class="select" id="filter-reservas-clase"></select>
                        <select class="select" id="filter-reservas-estado"></select>
                        <button class="btn-primary" type="button" onclick="Panel.load('reservas')">Filtrar</button>
                        <button class="btn-secondary" type="button" onclick="Panel.clearFilters('reservas')">Limpiar</button>
                    </div>
                    <div class="table-wrap"><table><thead><tr><th>Reserva</th><th>Pasajero</th><th>Vuelo</th><th>Clase</th><th>Estado</th><th>Asiento / Precio</th><th>Acciones</th></tr></thead><tbody id="table-reservas"></tbody></table></div>
                </div>
            </div>
        </section>

        <section class="view-section hidden" data-view="rutas">
            <div class="grid xl:grid-cols-[320px_1fr] gap-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4"><div class="font-semibold">Definir ruta</div><button class="btn-secondary" type="button" onclick="Panel.resetForm('rutas')">Limpiar</button></div>
                    <form id="form-rutas" class="space-y-3" onsubmit="event.preventDefault(); Panel.submit('rutas');">
                        <input type="hidden" name="id">
                        <input class="input" name="codigo" placeholder="Codigo de ruta">
                        <input class="input" name="pais" placeholder="Pais">
                        <select class="select" name="origen_airport_id" data-options="aeropuertos"></select>
                        <select class="select" name="destino_airport_id" data-options="aeropuertos"></select>
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="distancia_km" type="number" min="1" placeholder="Distancia km">
                            <input class="input" name="tiempo_estimado_min" type="number" min="1" placeholder="Tiempo min">
                        </div>
                        <button class="btn-primary w-full">Guardar ruta</button>
                    </form>
                </div>
                <div class="card p-5">
                    <div class="grid md:grid-cols-[1fr_auto_auto] gap-3 mb-4">
                        <input class="input" id="search-rutas" placeholder="Buscar ruta">
                        <button class="btn-primary" type="button" onclick="Panel.load('rutas')">Filtrar</button>
                        <button class="btn-secondary" type="button" onclick="Panel.clearFilters('rutas')">Limpiar</button>
                    </div>
                    <div class="table-wrap"><table><thead><tr><th>Ruta</th><th>Origen</th><th>Destino</th><th>Pais</th><th>Distancia</th><th>Tiempo</th><th>Acciones</th></tr></thead><tbody id="table-rutas"></tbody></table></div>
                </div>
            </div>
        </section>

        <section class="view-section hidden" data-view="aeropuertos">
            <div class="grid xl:grid-cols-[320px_1fr] gap-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4"><div class="font-semibold">Registrar aeropuerto</div><button class="btn-secondary" type="button" onclick="Panel.resetForm('aeropuertos')">Limpiar</button></div>
                    <form id="form-aeropuertos" class="space-y-3" onsubmit="event.preventDefault(); Panel.submit('aeropuertos');">
                        <input type="hidden" name="id">
                        <input class="input" name="nombre" placeholder="Nombre">
                        <input class="input" name="codigo_iata" placeholder="Codigo IATA">
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="ciudad" placeholder="Ciudad">
                            <input class="input" name="pais" placeholder="Pais">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="latitud" type="number" step="0.0000001" placeholder="Latitud">
                            <input class="input" name="longitud" type="number" step="0.0000001" placeholder="Longitud">
                        </div>
                        <button class="btn-primary w-full">Guardar aeropuerto</button>
                    </form>
                </div>
                <div class="card p-5">
                    <div class="grid md:grid-cols-[1fr_auto_auto] gap-3 mb-4">
                        <input class="input" id="search-aeropuertos" placeholder="Buscar aeropuerto">
                        <button class="btn-primary" type="button" onclick="Panel.load('aeropuertos')">Filtrar</button>
                        <button class="btn-secondary" type="button" onclick="Panel.clearFilters('aeropuertos')">Limpiar</button>
                    </div>
                    <div class="table-wrap"><table><thead><tr><th>Aeropuerto</th><th>Codigo</th><th>Ubicacion</th><th>Coordenadas</th><th>Acciones</th></tr></thead><tbody id="table-aeropuertos"></tbody></table></div>
                </div>
            </div>
        </section>

        <section class="view-section hidden" data-view="flota">
            <div class="grid xl:grid-cols-[320px_1fr] gap-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4"><div class="font-semibold">Registrar avion</div><button class="btn-secondary" type="button" onclick="Panel.resetForm('flota')">Limpiar</button></div>
                    <form id="form-flota" class="space-y-3" onsubmit="event.preventDefault(); Panel.submit('flota');">
                        <input type="hidden" name="id">
                        <select class="select" name="aerolinea_id" data-options="aerolineas"></select>
                        <input class="input" name="matricula" placeholder="Matricula">
                        <input class="input" name="modelo" placeholder="Modelo">
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="capacidad" type="number" min="1" placeholder="Capacidad">
                            <input class="input" name="alcance_km" type="number" min="1" placeholder="Alcance km">
                        </div>
                        <input class="input" name="tipo" placeholder="Tipo de avion">
                        <button class="btn-primary w-full">Guardar avion</button>
                    </form>
                </div>
                <div class="card p-5">
                    <div class="grid md:grid-cols-[1fr_auto_auto] gap-3 mb-4">
                        <input class="input" id="search-flota" placeholder="Buscar matricula o modelo">
                        <button class="btn-primary" type="button" onclick="Panel.load('flota')">Filtrar</button>
                        <button class="btn-secondary" type="button" onclick="Panel.clearFilters('flota')">Limpiar</button>
                    </div>
                    <div class="table-wrap"><table><thead><tr><th>Avion</th><th>Aerolinea</th><th>Modelo / Tipo</th><th>Capacidad</th><th>Alcance</th><th>Acciones</th></tr></thead><tbody id="table-flota"></tbody></table></div>
                </div>
            </div>
        </section>

        <section class="view-section hidden" data-view="aerolineas">
            <div class="grid xl:grid-cols-[320px_1fr] gap-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4"><div class="font-semibold">Registrar aerolinea</div><button class="btn-secondary" type="button" onclick="Panel.resetForm('aerolineas')">Limpiar</button></div>
                    <form id="form-aerolineas" class="space-y-3" onsubmit="event.preventDefault(); Panel.submit('aerolineas');">
                        <input type="hidden" name="id">
                        <input class="input" name="nombre" placeholder="Nombre">
                        <div class="grid grid-cols-2 gap-3">
                            <input class="input" name="codigo" placeholder="Codigo">
                            <input class="input" name="pais" placeholder="Pais">
                        </div>
                        <button class="btn-primary w-full">Guardar aerolinea</button>
                    </form>
                </div>
                <div class="card p-5">
                    <div class="grid md:grid-cols-[1fr_auto_auto] gap-3 mb-4">
                        <input class="input" id="search-aerolineas" placeholder="Buscar aerolinea">
                        <button class="btn-primary" type="button" onclick="Panel.load('aerolineas')">Filtrar</button>
                        <button class="btn-secondary" type="button" onclick="Panel.clearFilters('aerolineas')">Limpiar</button>
                    </div>
                    <div class="table-wrap"><table><thead><tr><th>Aerolinea</th><th>Codigo</th><th>Pais</th><th>Vuelos</th><th>Flota</th><th>Acciones</th></tr></thead><tbody id="table-aerolineas"></tbody></table></div>
                </div>
            </div>
        </section>

        <section class="view-section hidden" data-view="simulacion">
            <div class="grid xl:grid-cols-[380px_1fr] gap-6">
                <div class="space-y-6">
                    <div class="card p-5">
                        <div class="font-semibold mb-4">Simular ruta manual</div>
                        <div class="space-y-3">
                            <select class="select" id="sim-origen" data-options="aeropuertos"></select>
                            <select class="select" id="sim-destino" data-options="aeropuertos"></select>
                            <button class="btn-primary w-full" type="button" onclick="Panel.simulateManual()">Simular ruta</button>
                        </div>
                    </div>
                    <div class="card p-5">
                        <div class="font-semibold mb-4">Simular vuelo registrado</div>
                        <div class="space-y-3">
                            <select class="select" id="sim-vuelo" data-options="vuelos"></select>
                            <button class="btn-primary w-full" type="button" onclick="Panel.simulateFlight()">Simular vuelo</button>
                        </div>
                    </div>
                    <div class="card p-5">
                        <div class="font-semibold mb-4">Ficha de simulacion</div>
                        <div id="simulation-meta" class="space-y-3"></div>
                    </div>
                    <div class="card p-5">
                        <div class="font-semibold mb-4">Controles de reproduccion</div>
                        <div class="space-y-4">
                            <select class="select" id="simulation-country-filter" onchange="Panel.filterAirportMapCountry(this.value)"></select>
                            <div class="grid grid-cols-3 gap-3">
                                <button class="btn-primary" type="button" id="simulation-play" onclick="Panel.toggleSimulation()">Reproducir</button>
                                <button class="btn-secondary" type="button" onclick="Panel.resetSimulation()">Reiniciar</button>
                                <select class="select" id="simulation-speed" onchange="Panel.changeSimulationSpeed(this.value)">
                                    <option value="0.75">0.75x</option>
                                    <option value="1" selected>1x</option>
                                    <option value="1.5">1.5x</option>
                                    <option value="2">2x</option>
                                    <option value="3">3x</option>
                                </select>
                            </div>
                            <div>
                                <input id="simulation-progress" type="range" min="0" max="100" value="0" class="w-full accent-sky-400" oninput="Panel.seekSimulation(this.value)">
                                <div id="simulation-progress-label" class="hint mt-2">Progreso de simulacion: 0%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4"><div class="font-semibold">Recorrido visual</div><span class="hint">Zoom automatico en tramos cortos</span></div>
                    <div id="simulation-canvas" class="rounded-2xl overflow-hidden border border-slate-700/60 bg-slate-950/40"></div>
                </div>
            </div>
        </section>
    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="/logistica-panel.js"></script>
</body>
</html>
