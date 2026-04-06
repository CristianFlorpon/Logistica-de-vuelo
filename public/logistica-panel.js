(() => {
const cfg = window.LOGISTICA_CONFIG

const state = {
  dashboard: null,
  aerolineas: [],
  aeropuertos: [],
  rutas: [],
  flota: [],
  vuelos: [],
  pasajeros: [],
  reservas: [],
}

const simulation = {
  origin: null,
  destination: null,
  meta: null,
  curve: null,
  pathPoints: [],
  progress: 0,
  startProgress: 0,
  speed: 1,
  playing: false,
  durationMs: 12000,
  rafId: null,
  lastTimestamp: null,
  map: null,
  airportLayer: null,
  routeLayer: null,
  routeBaseLine: null,
  routeProgressLine: null,
  originMarker: null,
  destinationMarker: null,
  movingMarker: null,
  selectedCountry: '',
}

const modules = {
  aerolineas: {
    endpoint: '/api/aerolineas',
    form: 'form-aerolineas',
    table: 'table-aerolineas',
    filterIds: ['search-aerolineas'],
    params: () => searchParams({ search: value('search-aerolineas') }),
    render: () => renderTable('aerolineas', 6, (item) => `
      <tr>
        <td><strong>${esc(item.nombre)}</strong></td>
        <td>${esc(item.codigo)}</td>
        <td>${esc(item.pais || '--')}</td>
        <td>${num(item.vuelos_count || 0)}</td>
        <td>${num(item.flotas_count || 0)}</td>
        <td>${actionButtons('aerolineas', item.id)}</td>
      </tr>`),
  },
  aeropuertos: {
    endpoint: '/api/aeropuertos',
    form: 'form-aeropuertos',
    table: 'table-aeropuertos',
    filterIds: ['search-aeropuertos'],
    params: () => searchParams({ search: value('search-aeropuertos') }),
    beforeSubmit: (data) => { if (data.codigo_iata) data.codigo_iata = data.codigo_iata.toUpperCase() },
    render: () => renderTable('aeropuertos', 5, (item) => `
      <tr>
        <td><strong>${esc(item.nombre)}</strong><div class="text-sm text-slate-400 mt-1">${esc(item.ciudad)}</div></td>
        <td>${esc(item.codigo_iata)}</td>
        <td>${esc(item.ciudad)}, ${esc(item.pais)}</td>
        <td>${Number(item.latitud).toFixed(4)}, ${Number(item.longitud).toFixed(4)}</td>
        <td>${actionButtons('aeropuertos', item.id)}</td>
      </tr>`),
  },
  rutas: {
    endpoint: '/api/rutas',
    form: 'form-rutas',
    table: 'table-rutas',
    filterIds: ['search-rutas'],
    params: () => searchParams({ search: value('search-rutas') }),
    render: () => renderTable('rutas', 7, (item) => `
      <tr>
        <td><strong>${esc(item.codigo)}</strong></td>
        <td>${esc(item.origen?.ciudad || '--')} (${esc(item.origen?.codigo_iata || '--')})</td>
        <td>${esc(item.destino?.ciudad || '--')} (${esc(item.destino?.codigo_iata || '--')})</td>
        <td>${esc(item.pais || '--')}</td>
        <td>${item.distancia_km ? `${num(item.distancia_km)} km` : '--'}</td>
        <td>${item.tiempo_estimado_min ? `${num(item.tiempo_estimado_min)} min` : '--'}</td>
        <td>${actionButtons('rutas', item.id)}</td>
      </tr>`),
  },
  flota: {
    endpoint: '/api/flotas',
    form: 'form-flota',
    table: 'table-flota',
    filterIds: ['search-flota'],
    params: () => searchParams({ search: value('search-flota') }),
    render: () => renderTable('flota', 6, (item) => `
      <tr>
        <td><strong>${esc(item.matricula)}</strong></td>
        <td>${esc(item.aerolinea?.nombre || '--')}</td>
        <td>${esc(item.modelo)}<div class="text-sm text-slate-400 mt-1">${esc(item.tipo)}</div></td>
        <td>${num(item.capacidad)} pax</td>
        <td>${item.alcance_km ? `${num(item.alcance_km)} km` : '--'}</td>
        <td>${actionButtons('flota', item.id)}</td>
      </tr>`),
  },
  vuelos: {
    endpoint: '/api/vuelos',
    form: 'form-vuelos',
    table: 'table-vuelos',
    filterIds: ['search-vuelos', 'filter-vuelos-estado'],
    params: () => searchParams({ search: value('search-vuelos'), estado: value('filter-vuelos-estado') }),
    render: () => renderTable('vuelos', 7, (item) => {
      const occ = item.asientos > 0 ? (((item.reservas_count || 0) / item.asientos) * 100).toFixed(1) : '0.0'
      return `<tr>
        <td><strong>${esc(item.codigo)}</strong><div class="text-sm text-slate-400 mt-1">${esc(item.flota?.matricula || 'Sin flota')}</div></td>
        <td>${esc(item.ruta ? routeLong(item.ruta) : `${item.origen || '--'} -> ${item.destino || '--'}`)}</td>
        <td>${esc(item.aerolinea?.nombre || '--')}</td>
        <td>
          <select class="select text-sm py-2 px-2 mb-2" onchange="Panel.setFlightStatus(${item.id}, this.value)">${cfg.flightStates.map((estado) => `<option value="${estado}" ${estado === item.estado ? 'selected' : ''}>${title(estado)}</option>`).join('')}</select>
          ${badge(item.estado)}
        </td>
        <td><div>${esc(dt(item.salida_programada))}</div><div class="text-sm text-slate-400 mt-1">${esc(dt(item.llegada_programada))}</div></td>
        <td>${num(item.reservas_count || 0)} / ${num(item.asientos || 0)}<div class="text-sm text-slate-400 mt-1">${occ}%</div></td>
        <td>${actionButtons('vuelos', item.id)}</td>
      </tr>`
    }),
  },
  pasajeros: {
    endpoint: '/api/pasajeros',
    form: 'form-pasajeros',
    table: 'table-pasajeros',
    filterIds: ['search-pasajeros'],
    params: () => searchParams({ search: value('search-pasajeros') }),
    render: () => renderTable('pasajeros', 6, (item) => `
      <tr>
        <td><strong>${esc(item.nombre)} ${esc(item.apellido)}</strong></td>
        <td>${esc(item.documento || '--')}</td>
        <td><div>${esc(item.email || '--')}</div><div class="text-sm text-slate-400 mt-1">${esc(item.telefono || '--')}</div></td>
        <td>${esc(item.vuelo?.codigo || '--')} - ${esc(flightRoute(item.vuelo || {}))}</td>
        <td>${num(item.reservas?.length || 0)}</td>
        <td>${actionButtons('pasajeros', item.id)}</td>
      </tr>`),
  },
  reservas: {
    endpoint: '/api/reservas',
    form: 'form-reservas',
    table: 'table-reservas',
    filterIds: ['search-reservas', 'filter-reservas-clase', 'filter-reservas-estado'],
    params: () => searchParams({ search: value('search-reservas'), clase: value('filter-reservas-clase'), estado: value('filter-reservas-estado') }),
    render: () => renderTable('reservas', 7, (item) => `
      <tr>
        <td><strong>${esc(item.codigo)}</strong></td>
        <td>${esc(item.pasajero?.nombre || '--')} ${esc(item.pasajero?.apellido || '')}</td>
        <td>${esc(item.vuelo?.codigo || '--')} - ${esc(flightRoute(item.vuelo || {}))}</td>
        <td>${badge(item.clase)}</td>
        <td>${badge(item.estado)}</td>
        <td><div>${esc(item.asiento || '--')}</div><div class="text-sm text-slate-400 mt-1">${esc(money(item.precio))}</div></td>
        <td>${actionButtons('reservas', item.id)}</td>
      </tr>`),
  },
}

const refreshMap = {
  aerolineas: ['aerolineas', 'flota', 'vuelos', 'dashboard'],
  aeropuertos: ['aeropuertos', 'rutas', 'vuelos', 'dashboard'],
  rutas: ['rutas', 'vuelos', 'dashboard'],
  flota: ['flota', 'vuelos', 'dashboard'],
  vuelos: ['vuelos', 'pasajeros', 'reservas', 'dashboard'],
  pasajeros: ['pasajeros', 'reservas', 'dashboard'],
  reservas: ['reservas', 'vuelos', 'dashboard'],
}

function value(id) {
  return document.getElementById(id)?.value?.trim() || ''
}

function searchParams(values) {
  return Object.fromEntries(Object.entries(values).filter(([, item]) => item))
}

function esc(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;')
}

function num(value) {
  return new Intl.NumberFormat('es-BO').format(Number(value || 0))
}

function money(value) {
  if (value === null || value === undefined || value === '') return '--'
  return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(Number(value))
}

function dt(value) {
  if (!value) return '--'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return new Intl.DateTimeFormat('es-BO', { dateStyle: 'short', timeStyle: 'short' }).format(date)
}

function toInput(value) {
  if (!value) return ''
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return ''
  const adjusted = new Date(date.getTime() - date.getTimezoneOffset() * 60000)
  return adjusted.toISOString().slice(0, 16)
}

function title(value) {
  return String(value || '').split(' ').map((part) => part ? part[0].toUpperCase() + part.slice(1) : part).join(' ')
}

function badge(value) {
  const key = String(value || '').toLowerCase().replaceAll(' ', '-')
  return `<span class="badge badge-${key}">${esc(title(value || 'sin dato'))}</span>`
}

function flightRoute(item) {
  if (item?.ruta?.origen && item?.ruta?.destino) return `${item.ruta.origen.codigo_iata} -> ${item.ruta.destino.codigo_iata}`
  return `${item?.origen || '--'} -> ${item?.destino || '--'}`
}

function routeLong(route) {
  return `${route.origen?.ciudad || '--'} (${route.origen?.codigo_iata || '--'}) -> ${route.destino?.ciudad || '--'} (${route.destino?.codigo_iata || '--'})`
}

function actionButtons(moduleName, id) {
  return `<div class="flex gap-2"><button class="btn-secondary" type="button" onclick="Panel.edit('${moduleName}', ${id})">Editar</button><button class="btn-danger" type="button" onclick="Panel.remove('${moduleName}', ${id})">Eliminar</button></div>`
}

function renderTable(name, colspan, rowBuilder) {
  const tbody = document.getElementById(modules[name].table)
  if (!state[name].length) {
    tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-slate-400 py-8">Sin registros disponibles.</td></tr>`
    return
  }
  tbody.innerHTML = state[name].map(rowBuilder).join('')
}

function setOptions(select, items, label, placeholder) {
  const current = select.value
  select.innerHTML = `<option value="">${placeholder}</option>` + items.map((item) => `<option value="${item.id}">${label(item)}</option>`).join('')
  if ([...select.options].some((option) => String(option.value) === String(current))) select.value = current
}

function optionLabel(type, item) {
  if (type === 'aerolineas') return `${item.codigo} | ${item.nombre}`
  if (type === 'aeropuertos') return `${item.codigo_iata} | ${item.ciudad}, ${item.pais}`
  if (type === 'rutas') return `${item.codigo} | ${item.origen?.codigo_iata || '--'} -> ${item.destino?.codigo_iata || '--'}`
  if (type === 'flota') return `${item.matricula} | ${item.modelo} (${item.capacidad} pax)`
  if (type === 'vuelos') return `${item.codigo} | ${flightRoute(item)}`
  if (type === 'pasajeros') return `${item.nombre} ${item.apellido}`
  return item.id
}

function bindViews() {
  document.querySelectorAll('[data-view-target]').forEach((button) => {
    button.addEventListener('click', () => {
      const view = button.dataset.viewTarget
      document.querySelectorAll('.view-section').forEach((section) => section.classList.toggle('hidden', section.dataset.view !== view))
      document.querySelectorAll('[data-view-target]').forEach((item) => item.classList.toggle('active', item === button))
      document.getElementById('viewTitle').textContent = title(view === 'dashboard' ? 'dashboard operativo' : `gestion de ${view}`)
      if (view === 'simulacion') {
        if (simulation.meta) drawSimulation({ fit: true })
        else renderSimulationHint()
        window.setTimeout(() => {
          if (simulation.map) simulation.map.invalidateSize()
          if (simulation.meta) fitRouteOnMap(simulation.pathPoints, simulation.meta.distance || 0)
          else renderAirportMarkersOnMap({ fit: true })
        }, 120)
      }
    })
  })
}

function fillStaticControls() {
  document.getElementById('flightStateLegend').innerHTML = cfg.flightStates.map((stateName) => badge(stateName)).join('')
  fillSelect(document.querySelector('#form-vuelos [name="estado"]'), cfg.flightStates, 'Estado del vuelo')
  fillSelect(document.getElementById('filter-vuelos-estado'), [''].concat(cfg.flightStates), 'Todos los estados')
  fillSelect(document.querySelector('#form-reservas [name="clase"]'), cfg.reservationClasses, 'Clase')
  fillSelect(document.querySelector('#form-reservas [name="estado"]'), cfg.reservationStates, 'Estado')
  fillSelect(document.getElementById('filter-reservas-clase'), [''].concat(cfg.reservationClasses), 'Todas las clases')
  fillSelect(document.getElementById('filter-reservas-estado'), [''].concat(cfg.reservationStates), 'Todos los estados')
}

function fillSelect(select, items, placeholder) {
  const current = select.value
  select.innerHTML = `<option value="">${placeholder}</option>` + items.filter(Boolean).map((item) => `<option value="${item}">${title(item)}</option>`).join('')
  if ([...select.options].some((option) => option.value === current)) select.value = current
}

function refreshLookups() {
  document.querySelectorAll('[data-options]').forEach((select) => {
    const type = select.dataset.options
    const key = type === 'flotas' ? 'flota' : type
    setOptions(select, state[key], (item) => optionLabel(key, item), placeholderFor(key))
  })
  populateCountryFilter()
  if (simulation.meta) drawSimulation({ fit: true })
  else renderAirportMarkersOnMap({ fit: true })
}

function placeholderFor(type) {
  if (type === 'aeropuertos') return 'Seleccionar aeropuerto'
  if (type === 'aerolineas') return 'Seleccionar aerolinea'
  if (type === 'rutas') return 'Seleccionar ruta'
  if (type === 'flota') return 'Seleccionar flota'
  if (type === 'vuelos') return 'Seleccionar vuelo'
  if (type === 'pasajeros') return 'Seleccionar pasajero'
  return 'Seleccionar'
}

function populateCountryFilter() {
  const select = document.getElementById('simulation-country-filter')
  if (!select) return
  const countries = [...new Set(state.aeropuertos.map((airport) => airport.pais).filter(Boolean))].sort((a, b) => a.localeCompare(b))
  const current = simulation.selectedCountry || select.value
  select.innerHTML = `<option value="">Todos los paises en el mapa</option>` + countries.map((country) => `<option value="${esc(country)}">${esc(country)}</option>`).join('')
  if ([...select.options].some((option) => option.value === current)) select.value = current
  simulation.selectedCountry = select.value
}

async function api(method, url, data = null, params = null) {
  try {
    const response = await axios({ method, url, data, params })
    return response.data
  } catch (error) {
    const payload = error?.response?.data
    if (payload?.errors) throw new Error(Object.values(payload.errors).flat().join(' '))
    throw new Error(payload?.message || error.message || 'No se pudo completar la operacion')
  }
}

async function confirmAction(message) {
  const result = await Swal.fire({
    icon: 'warning',
    title: 'Confirmar accion',
    text: message,
    showCancelButton: true,
    confirmButtonText: 'Si, continuar',
    cancelButtonText: 'Cancelar',
    background: '#0f172a',
    color: '#e2e8f0',
  })
  return result.isConfirmed
}

function toast(icon, title) {
  Swal.fire({ icon, title, timer: 1700, showConfirmButton: false, background: '#0f172a', color: '#e2e8f0' })
}

function formData(name) {
  const form = document.getElementById(modules[name].form)
  const data = Object.fromEntries(new FormData(form).entries())
  const id = data.id || ''
  delete data.id
  Object.keys(data).forEach((key) => { if (data[key] === '') data[key] = '' })
  return { form, id, data }
}

async function load(name) {
  const mod = modules[name]
  state[name] = await api('get', mod.endpoint, null, mod.params ? mod.params() : null)
  mod.render()
  refreshLookups()
}

async function loadDashboard() {
  state.dashboard = await api('get', '/api/dashboard')
  renderDashboard()
}

async function refreshAll() {
  for (const key of ['aerolineas', 'aeropuertos', 'rutas', 'flota', 'vuelos', 'pasajeros', 'reservas']) {
    await load(key)
  }
  await loadDashboard()
  resetDefaults()
}

function renderDashboard() {
  const data = state.dashboard || {}
  const totals = data.totales || {}
  document.getElementById('dashboardStats').innerHTML = [
    stat('Vuelos', totals.vuelos || 0),
    stat('Pasajeros', totals.pasajeros || 0),
    stat('Reservas', totals.reservas || 0),
    stat('Aerolineas', totals.aerolineas || 0),
    stat('Rutas', totals.rutas || 0),
    stat('Aeropuertos', totals.aeropuertos || 0),
    stat('Flota', totals.flota || 0),
  ].join('')
  document.getElementById('dashboardStates').innerHTML = bars(data.vuelos_por_estado || [], 'estado')
  document.getElementById('dashboardClasses').innerHTML = bars(data.reservas_por_clase || [], 'clase')
  document.getElementById('dashboardOccupancy').innerHTML = occupancy(data.ocupacion_vuelos || [])
  document.getElementById('dashboardRoutes').innerHTML = topRoutes(data.rutas_mas_usadas || [])
  document.getElementById('dashboardUpcoming').innerHTML = upcoming(data.proximos_vuelos || [])
}

function stat(label, value) {
  return `<div class="card p-5"><div class="text-xs uppercase tracking-[0.3em] text-slate-400 mb-3">${esc(label)}</div><div class="text-4xl font-bold text-sky-300">${num(value)}</div></div>`
}

function bars(items, key) {
  if (!items.length) return `<p class="text-slate-400">Sin datos todavia.</p>`
  const max = Math.max(...items.map((item) => Number(item.total || 0)), 1)
  return items.map((item) => `<div><div class="flex items-center justify-between mb-2 text-sm"><span>${esc(title(item[key]))}</span><strong>${num(item.total)}</strong></div><div class="bar"><span style="width:${Math.max(8, (item.total / max) * 100)}%"></span></div></div>`).join('')
}

function occupancy(items) {
  if (!items.length) return `<p class="text-slate-400">Aun no hay vuelos para calcular ocupacion.</p>`
  return items.map((item) => `<div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4"><div class="flex items-center justify-between gap-3"><div><div class="font-semibold">${esc(item.codigo)}</div><div class="text-sm text-slate-400 mt-1">${esc(item.ruta)}</div></div>${badge(item.estado)}</div><div class="flex items-center justify-between text-sm mt-3 mb-2"><span>${num(item.reservas)} / ${num(item.asientos)} asientos</span><strong>${item.ocupacion}%</strong></div><div class="bar"><span style="width:${Math.min(100, item.ocupacion)}%"></span></div></div>`).join('')
}

function topRoutes(items) {
  if (!items.length) return `<p class="text-slate-400">Todavia no hay rutas con uso registrado.</p>`
  return items.map((item) => `<div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4"><div class="font-semibold">${esc(item.ruta)}</div><div class="text-sm text-slate-400 mt-2">${num(item.vuelos)} vuelos | ${num(item.reservas)} reservas</div></div>`).join('')
}

function upcoming(items) {
  if (!items.length) return `<p class="text-slate-400">No hay vuelos con salida programada.</p>`
  return items.map((item) => `<div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between"><div><div class="font-semibold">${esc(item.codigo)} - ${esc(item.aerolinea || 'Sin aerolinea')}</div><div class="text-sm text-slate-400 mt-1">${esc(item.ruta)}</div></div><div class="flex items-center gap-3">${badge(item.estado)}<span class="text-sm">${esc(item.salida_programada || '--')}</span></div></div>`).join('')
}

function resetDefaults() {
  const flightForm = document.getElementById('form-vuelos')
  if (flightForm && !flightForm.querySelector('[name="estado"]').value) flightForm.querySelector('[name="estado"]').value = 'programado'
  const bookingForm = document.getElementById('form-reservas')
  if (bookingForm) {
    if (!bookingForm.querySelector('[name="clase"]').value) bookingForm.querySelector('[name="clase"]').value = 'economica'
    if (!bookingForm.querySelector('[name="estado"]').value) bookingForm.querySelector('[name="estado"]').value = 'confirmada'
  }
}

function clearRouteLayer() {
  if (simulation.routeLayer) simulation.routeLayer.clearLayers()
  simulation.routeBaseLine = null
  simulation.routeProgressLine = null
  simulation.originMarker = null
  simulation.destinationMarker = null
  simulation.movingMarker = null
}

function ensureSimulationMap() {
  const canvas = document.getElementById('simulation-canvas')
  if (!canvas) return null

  if (typeof window.L === 'undefined') {
    canvas.innerHTML = '<div class="flex h-[520px] items-center justify-center text-slate-400">No se pudo cargar OpenStreetMap.</div>'
    return null
  }

  if (simulation.map && !document.getElementById('simulation-map')) {
    simulation.map.remove()
    simulation.map = null
    simulation.airportLayer = null
    simulation.routeLayer = null
    clearRouteLayer()
  }

  if (!document.getElementById('simulation-map')) {
    canvas.innerHTML = '<div id="simulation-map" style="height:520px;width:100%;"></div>'
  }

  if (!simulation.map) {
    simulation.map = L.map('simulation-map', {
      zoomControl: true,
      worldCopyJump: true,
      zoomSnap: 0.25,
      minZoom: 2,
    })

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">OpenStreetMap</a> contributors',
      maxZoom: 19,
    }).addTo(simulation.map)

    simulation.airportLayer = L.layerGroup().addTo(simulation.map)
    simulation.routeLayer = L.layerGroup().addTo(simulation.map)
    simulation.map.setView([-15, -60], 3)
  }

  requestAnimationFrame(() => {
    if (simulation.map) simulation.map.invalidateSize()
  })

  return simulation.map
}

function airportLatLng(airport) {
  const lat = Number(airport?.latitud)
  const lng = Number(airport?.longitud)
  if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null
  return [lat, lng]
}

function filteredAirports() {
  return state.aeropuertos.filter((airport) => {
    if (!airportLatLng(airport)) return false
    return !simulation.selectedCountry || airport.pais === simulation.selectedCountry
  })
}

function clamp(value, min, max) {
  return Math.min(max, Math.max(min, value))
}

function simulationStatusProgress(status) {
  return {
    programado: 0.02,
    embarcando: 0.12,
    'en vuelo': 0.55,
    demorado: 0.15,
    aterrizado: 1,
    cancelado: 0,
  }[status] ?? 0
}

function mapFocus(distance) {
  if (distance <= 250) return { label: 'Detalle local', maxZoom: 10, padding: [26, 26] }
  if (distance <= 700) return { label: 'Tramo corto ampliado', maxZoom: 8.25, padding: [30, 30] }
  if (distance <= 1600) return { label: 'Vista regional', maxZoom: 6.5, padding: [36, 36] }
  if (distance <= 3200) return { label: 'Vista continental', maxZoom: 5.5, padding: [44, 44] }
  return { label: 'Vista extensa', maxZoom: 4.75, padding: [52, 52] }
}

function addAirportMarker(layer, airport, options = {}) {
  const latlng = airportLatLng(airport)
  if (!latlng || typeof window.L === 'undefined') return null

  const marker = L.circleMarker(latlng, {
    radius: options.radius ?? 6,
    color: options.stroke ?? '#e2e8f0',
    weight: options.weight ?? 1.6,
    fillColor: options.fill ?? '#38bdf8',
    fillOpacity: options.fillOpacity ?? 0.92,
  })

  marker.bindPopup(`
    <div class="space-y-1">
      <div><strong>${esc(airport.codigo_iata || '--')}</strong> - ${esc(airport.nombre || 'Aeropuerto')}</div>
      <div>${esc(airport.ciudad || '--')}, ${esc(airport.pais || '--')}</div>
      <div>Lat ${Number(latlng[0]).toFixed(4)} | Lon ${Number(latlng[1]).toFixed(4)}</div>
    </div>`)

  if (options.tooltip) {
    marker.bindTooltip(options.tooltip, {
      permanent: true,
      direction: 'top',
      offset: [0, -8],
      opacity: 0.94,
    })
  }

  marker.addTo(layer)
  return marker
}

function fitAirportsOnMap(airports) {
  const map = ensureSimulationMap()
  if (!map) return

  const points = airports.map((airport) => airportLatLng(airport)).filter(Boolean)
  if (!points.length) {
    map.setView([-15, -60], 3)
    return
  }

  if (points.length === 1) {
    map.setView(points[0], 7.5)
    return
  }

  map.fitBounds(L.latLngBounds(points), {
    padding: [34, 34],
    maxZoom: 6.75,
    animate: false,
  })
}

function renderAirportMarkersOnMap({ fit = !simulation.meta } = {}) {
  const map = ensureSimulationMap()
  if (!map || !simulation.airportLayer) return

  simulation.airportLayer.clearLayers()
  const airports = filteredAirports()

  airports.forEach((airport) => {
    addAirportMarker(simulation.airportLayer, airport, {
      fill: '#38bdf8',
      stroke: '#dbeafe',
      radius: 6,
    })
  })

  if (fit) fitAirportsOnMap(airports)
}

function simulationStage(progress) {
  if (progress <= 0.04) return 'Preparando salida'
  if (progress <= 0.18) return 'Despegando'
  if (progress <= 0.82) return 'En ruta'
  if (progress < 1) return 'Aproximando aterrizaje'
  return 'Arribo completado'
}

function syncSimulationControls() {
  const playButton = document.getElementById('simulation-play')
  const progressInput = document.getElementById('simulation-progress')
  const progressLabel = document.getElementById('simulation-progress-label')
  const speedInput = document.getElementById('simulation-speed')

  if (!playButton || !progressInput || !progressLabel || !speedInput) return

  playButton.textContent = simulation.playing ? 'Pausar' : 'Reproducir'
  playButton.disabled = !simulation.meta
  progressInput.disabled = !simulation.meta
  progressInput.value = Math.round(simulation.progress * 100)
  speedInput.value = String(simulation.speed)
  progressLabel.textContent = simulation.meta
    ? `Progreso de simulacion: ${Math.round(simulation.progress * 100)}% | ${simulationStage(simulation.progress)}`
    : 'Progreso de simulacion: 0%'
}

function stopSimulation() {
  if (simulation.rafId) cancelAnimationFrame(simulation.rafId)
  simulation.rafId = null
  simulation.lastTimestamp = null
  simulation.playing = false
}

function formatCoordinate(value) {
  const number = Number(value)
  if (!Number.isFinite(number)) return '--'
  return number.toFixed(4)
}

function greatCirclePoints(origin, destination, steps = 64) {
  const start = airportLatLng(origin)
  const end = airportLatLng(destination)
  if (!start || !end) return []

  const lat1 = radians(start[0])
  const lon1 = radians(start[1])
  const lat2 = radians(end[0])
  const lon2 = radians(end[1])
  const dot = (Math.cos(lat1) * Math.cos(lon1) * Math.cos(lat2) * Math.cos(lon2))
    + (Math.cos(lat1) * Math.sin(lon1) * Math.cos(lat2) * Math.sin(lon2))
    + (Math.sin(lat1) * Math.sin(lat2))
  const omega = Math.acos(clamp(dot, -1, 1))

  if (!Number.isFinite(omega) || omega === 0) return [start, end]

  const points = []
  for (let index = 0; index <= steps; index += 1) {
    const factor = index / steps
    const startWeight = Math.sin((1 - factor) * omega) / Math.sin(omega)
    const endWeight = Math.sin(factor * omega) / Math.sin(omega)
    const x = (startWeight * Math.cos(lat1) * Math.cos(lon1)) + (endWeight * Math.cos(lat2) * Math.cos(lon2))
    const y = (startWeight * Math.cos(lat1) * Math.sin(lon1)) + (endWeight * Math.cos(lat2) * Math.sin(lon2))
    const z = (startWeight * Math.sin(lat1)) + (endWeight * Math.sin(lat2))
    const lat = Math.atan2(z, Math.sqrt((x ** 2) + (y ** 2))) * (180 / Math.PI)
    const lng = Math.atan2(y, x) * (180 / Math.PI)
    points.push([lat, lng])
  }

  return points
}

function bearingDegrees(start, end) {
  const lat1 = radians(start[0])
  const lat2 = radians(end[0])
  const lon1 = radians(start[1])
  const lon2 = radians(end[1])
  const y = Math.sin(lon2 - lon1) * Math.cos(lat2)
  const x = (Math.cos(lat1) * Math.sin(lat2))
    - (Math.sin(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1))
  return (Math.atan2(y, x) * (180 / Math.PI) + 360) % 360
}

function routePointAtProgress(pathPoints, progress) {
  if (!pathPoints.length) return null
  if (pathPoints.length === 1) return { latlng: pathPoints[0], angle: 0 }

  const scaled = clamp(progress, 0, 1) * (pathPoints.length - 1)
  const segment = Math.min(pathPoints.length - 2, Math.floor(scaled))
  const ratio = scaled - segment
  const start = pathPoints[segment]
  const end = pathPoints[segment + 1]

  return {
    latlng: [
      start[0] + ((end[0] - start[0]) * ratio),
      start[1] + ((end[1] - start[1]) * ratio),
    ],
    angle: bearingDegrees(start, end),
  }
}

function routeProgressPoints(pathPoints, progress) {
  const current = routePointAtProgress(pathPoints, progress)
  if (!current) return []

  const scaled = clamp(progress, 0, 1) * (pathPoints.length - 1)
  const segment = Math.floor(scaled)
  const points = pathPoints.slice(0, Math.min(segment + 1, pathPoints.length))
  const last = points[points.length - 1]

  if (!last || last[0] !== current.latlng[0] || last[1] !== current.latlng[1]) points.push(current.latlng)
  return points
}

function planeIcon(angle) {
  return L.divIcon({
    className: 'plane-icon',
    html: `
      <div class="plane-badge">
        <svg width="20" height="20" viewBox="-18 -18 36 36" style="transform:rotate(${angle}deg);transform-origin:50% 50%">
          <path d="M -16 0 L 11 -8 L 5 0 L 11 8 Z" fill="#f8fafc"></path>
        </svg>
      </div>`,
    iconSize: [34, 34],
    iconAnchor: [17, 17],
  })
}

function fitRouteOnMap(pathPoints, distance) {
  const map = ensureSimulationMap()
  if (!map || !pathPoints.length) return

  const focus = mapFocus(distance)
  if (pathPoints.length === 1) {
    map.setView(pathPoints[0], focus.maxZoom)
    return
  }

  map.fitBounds(L.latLngBounds(pathPoints), {
    padding: focus.padding,
    maxZoom: focus.maxZoom,
    animate: false,
  })
}

function renderSimulationHint({ fit = true } = {}) {
  stopSimulation()
  simulation.origin = null
  simulation.destination = null
  simulation.meta = null
  simulation.curve = null
  simulation.pathPoints = []
  simulation.progress = 0
  simulation.startProgress = 0
  clearRouteLayer()
  ensureSimulationMap()
  renderAirportMarkersOnMap({ fit })

  const airports = filteredAirports()
  document.getElementById('simulation-meta').innerHTML = `
    <div class="grid sm:grid-cols-3 gap-3">
      <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4">
        <div class="text-xs uppercase tracking-[0.25em] text-slate-400 mb-2">Mapa</div>
        <div class="font-semibold">OpenStreetMap activo</div>
      </div>
      <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4">
        <div class="text-xs uppercase tracking-[0.25em] text-slate-400 mb-2">Aeropuertos visibles</div>
        <div class="font-semibold">${num(airports.length)}</div>
      </div>
      <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4">
        <div class="text-xs uppercase tracking-[0.25em] text-slate-400 mb-2">Pais en mapa</div>
        <div class="font-semibold">${esc(simulation.selectedCountry || 'Todos')}</div>
      </div>
    </div>
    <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4 text-slate-300">
      Selecciona una ruta manual o un vuelo registrado para ver el recorrido.
      Si el tramo es corto, el mapa se acerca automaticamente para que la ruta se vea con mas detalle.
    </div>`
  syncSimulationControls()
}

function updateSimulationMeta(point) {
  const focus = mapFocus(simulation.meta.distance || 0)
  document.getElementById('simulation-meta').innerHTML = `
    <div class="grid sm:grid-cols-2 gap-3">
      <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4"><div class="text-xs uppercase tracking-[0.25em] text-slate-400 mb-2">Modo</div><div class="font-semibold">${esc(simulation.meta.mode)}</div></div>
      <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4"><div class="text-xs uppercase tracking-[0.25em] text-slate-400 mb-2">Vista del mapa</div><div class="font-semibold">${esc(focus.label)}</div></div>
      <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4"><div class="text-xs uppercase tracking-[0.25em] text-slate-400 mb-2">Distancia</div><div class="font-semibold">${num(simulation.meta.distance)} km</div></div>
      <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4"><div class="text-xs uppercase tracking-[0.25em] text-slate-400 mb-2">Tiempo estimado</div><div class="font-semibold">${num(simulation.meta.minutes)} min</div></div>
    </div>
    <div class="rounded-2xl border border-slate-700/60 bg-slate-950/40 p-4">
      <div class="text-sm text-slate-400 mb-2">${esc(simulation.meta.title)}</div>
      <div class="font-semibold">${esc(simulation.meta.subtitle)}</div>
      ${simulation.meta.status ? `<div class="mt-3">${badge(simulation.meta.status)}</div>` : ''}
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4 text-sm">
        <div><div class="text-slate-400 text-xs uppercase mb-1">Fase</div><div>${esc(simulationStage(simulation.progress))}</div></div>
        <div><div class="text-slate-400 text-xs uppercase mb-1">Posicion actual</div><div>${formatCoordinate(point[0])}, ${formatCoordinate(point[1])}</div></div>
        <div><div class="text-slate-400 text-xs uppercase mb-1">Aeropuertos en mapa</div><div>${num(filteredAirports().length)}</div></div>
        <div><div class="text-slate-400 text-xs uppercase mb-1">Filtro de pais</div><div>${esc(simulation.selectedCountry || 'Todos')}</div></div>
      </div>
    </div>`
}

function drawSimulation({ fit = false } = {}) {
  if (!simulation.meta || !simulation.pathPoints.length) {
    renderSimulationHint({ fit })
    return
  }

  const map = ensureSimulationMap()
  if (!map || !simulation.routeLayer) return

  if (fit || !simulation.routeBaseLine) renderAirportMarkersOnMap({ fit: false })

  const current = routePointAtProgress(simulation.pathPoints, simulation.progress)
  const progressPoints = routeProgressPoints(simulation.pathPoints, simulation.progress)
  if (!current) return

  if (!simulation.routeBaseLine) {
    simulation.routeBaseLine = L.polyline(simulation.pathPoints, {
      color: '#64748b',
      weight: 4,
      opacity: 0.75,
      dashArray: '8 10',
    }).addTo(simulation.routeLayer)
  } else {
    simulation.routeBaseLine.setLatLngs(simulation.pathPoints)
  }

  if (!simulation.routeProgressLine) {
    simulation.routeProgressLine = L.polyline(progressPoints, {
      color: '#38bdf8',
      weight: 5,
      opacity: 0.95,
      lineCap: 'round',
    }).addTo(simulation.routeLayer)
  } else {
    simulation.routeProgressLine.setLatLngs(progressPoints)
  }

  if (!simulation.originMarker) {
    simulation.originMarker = addAirportMarker(simulation.routeLayer, simulation.origin, {
      fill: '#22d3ee',
      stroke: '#bae6fd',
      radius: 8,
      weight: 2,
      tooltip: `Salida ${esc(simulation.origin.codigo_iata || '')}`,
    })
  }

  if (!simulation.destinationMarker) {
    simulation.destinationMarker = addAirportMarker(simulation.routeLayer, simulation.destination, {
      fill: '#34d399',
      stroke: '#bbf7d0',
      radius: 8,
      weight: 2,
      tooltip: `Destino ${esc(simulation.destination.codigo_iata || '')}`,
    })
  }

  if (!simulation.movingMarker) {
    simulation.movingMarker = L.marker(current.latlng, {
      icon: planeIcon(current.angle),
      zIndexOffset: 1000,
    }).addTo(simulation.routeLayer)
  } else {
    simulation.movingMarker.setLatLng(current.latlng)
    simulation.movingMarker.setIcon(planeIcon(current.angle))
  }

  if (fit) fitRouteOnMap(simulation.pathPoints, simulation.meta.distance || 0)
  updateSimulationMeta(current.latlng)
  syncSimulationControls()
}

function animateSimulation(timestamp) {
  if (!simulation.playing) return
  if (simulation.lastTimestamp === null) simulation.lastTimestamp = timestamp
  const delta = timestamp - simulation.lastTimestamp
  simulation.lastTimestamp = timestamp
  simulation.progress = clamp(simulation.progress + ((delta / simulation.durationMs) * simulation.speed), 0, 1)
  drawSimulation()
  if (simulation.progress >= 1) {
    stopSimulation()
    syncSimulationControls()
    return
  }
  simulation.rafId = requestAnimationFrame(animateSimulation)
}

function radians(value) {
  return Number(value) * (Math.PI / 180)
}

function distanceKm(a, b) {
  const earth = 6371
  const lat1 = radians(a.latitud)
  const lon1 = radians(a.longitud)
  const lat2 = radians(b.latitud)
  const lon2 = radians(b.longitud)
  const dLat = lat2 - lat1
  const dLon = lon2 - lon1
  const c = Math.sin(dLat / 2) ** 2 + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLon / 2) ** 2
  return Math.round(earth * (2 * Math.atan2(Math.sqrt(c), Math.sqrt(1 - c))))
}

function renderSimulation(origin, destination, meta) {
  stopSimulation()
  clearRouteLayer()
  simulation.origin = origin
  simulation.destination = destination
  simulation.meta = meta
  simulation.curve = { short: (meta.distance || 0) <= 700 }
  simulation.pathPoints = greatCirclePoints(origin, destination, clamp(Math.round((meta.distance || 0) / 60), 24, 100))
  simulation.startProgress = clamp(meta.startProgress ?? 0, 0, 1)
  simulation.progress = simulation.startProgress
  simulation.durationMs = clamp((meta.distance || 1000) * 2.1, 8000, 26000)
  drawSimulation({ fit: true })
  if (meta.autoPlay !== false && simulation.progress < 1) {
    simulation.playing = true
    syncSimulationControls()
    simulation.rafId = requestAnimationFrame(animateSimulation)
  }
}

window.Panel = {
  async load(name) {
    try { await load(name) } catch (error) { Swal.fire({ icon: 'error', title: 'Operacion no completada', text: error.message, background: '#0f172a', color: '#e2e8f0' }) }
  },
  clearFilters(name) {
    modules[name].filterIds.forEach((id) => { document.getElementById(id).value = '' })
    this.load(name)
  },
  resetForm(name) {
    document.getElementById(modules[name].form).reset()
    resetDefaults()
  },
  edit(name, id) {
    const item = state[name].find((row) => row.id === id)
    if (!item) return
    const form = document.getElementById(modules[name].form)
    ;[...form.elements].forEach((field) => {
      if (!field.name) return
      if (field.name === 'id') field.value = item.id
      else if (field.type === 'datetime-local') field.value = toInput(item[field.name])
      else field.value = item[field.name] ?? ''
    })
  },
  async submit(name) {
    try {
      const mod = modules[name]
      const { form, id, data } = formData(name)
      if (mod.beforeSubmit) mod.beforeSubmit(data)
      await api(id ? 'put' : 'post', id ? `${mod.endpoint}/${id}` : mod.endpoint, data)
      form.reset()
      resetDefaults()
      toast('success', 'Registro guardado')
      for (const target of refreshMap[name]) target === 'dashboard' ? await loadDashboard() : await load(target)
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Operacion no completada', text: error.message, background: '#0f172a', color: '#e2e8f0' })
    }
  },
  async remove(name, id) {
    if (!await confirmAction('Se eliminara el registro seleccionado.')) return
    try {
      await api('delete', `${modules[name].endpoint}/${id}`)
      toast('success', 'Registro eliminado')
      for (const target of refreshMap[name]) target === 'dashboard' ? await loadDashboard() : await load(target)
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Operacion no completada', text: error.message, background: '#0f172a', color: '#e2e8f0' })
    }
  },
  async setFlightStatus(id, status) {
    try {
      await api('patch', `/api/vuelos/${id}/estado`, { estado: status })
      await load('vuelos')
      await loadDashboard()
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Operacion no completada', text: error.message, background: '#0f172a', color: '#e2e8f0' })
    }
  },
  syncFlightRoute() {
    const route = state.rutas.find((item) => item.id === Number(document.querySelector('#form-vuelos [name="ruta_id"]').value))
    if (!route) return
    document.querySelector('#form-vuelos [name="origen"]').value = `${route.origen?.ciudad || ''} (${route.origen?.codigo_iata || ''})`.trim()
    document.querySelector('#form-vuelos [name="destino"]').value = `${route.destino?.ciudad || ''} (${route.destino?.codigo_iata || ''})`.trim()
  },
  syncFlightFleet() {
    const fleet = state.flota.find((item) => item.id === Number(document.querySelector('#form-vuelos [name="flota_id"]').value))
    if (!fleet) return
    document.querySelector('#form-vuelos [name="asientos"]').value = fleet.capacidad || ''
    if (!document.querySelector('#form-vuelos [name="aerolinea_id"]').value) document.querySelector('#form-vuelos [name="aerolinea_id"]').value = fleet.aerolinea_id || ''
  },
  syncReservationPassenger() {
    const passenger = state.pasajeros.find((item) => item.id === Number(document.querySelector('#form-reservas [name="pasajero_id"]').value))
    if (passenger) document.querySelector('#form-reservas [name="vuelo_id"]').value = passenger.vuelo_id || ''
  },
  simulateManual() {
    const origin = state.aeropuertos.find((item) => item.id === Number(document.getElementById('sim-origen').value))
    const destination = state.aeropuertos.find((item) => item.id === Number(document.getElementById('sim-destino').value))
    if (!origin || !destination) return Swal.fire({ icon: 'error', title: 'Faltan datos', text: 'Selecciona origen y destino.', background: '#0f172a', color: '#e2e8f0' })
    const distance = distanceKm(origin, destination)
    renderSimulation(origin, destination, { mode: 'Manual', title: `Ruta manual ${origin.codigo_iata} -> ${destination.codigo_iata}`, subtitle: `${origin.ciudad}, ${origin.pais} -> ${destination.ciudad}, ${destination.pais}`, distance, minutes: Math.max(45, Math.round((distance / 750) * 60)), startProgress: 0, autoPlay: true })
  },
  simulateFlight() {
    const flight = state.vuelos.find((item) => item.id === Number(document.getElementById('sim-vuelo').value))
    if (!flight) return Swal.fire({ icon: 'error', title: 'Faltan datos', text: 'Selecciona un vuelo.', background: '#0f172a', color: '#e2e8f0' })
    if (!flight.ruta?.origen || !flight.ruta?.destino) return Swal.fire({ icon: 'error', title: 'Ruta no disponible', text: 'Ese vuelo no tiene una ruta con aeropuertos vinculados.', background: '#0f172a', color: '#e2e8f0' })
    const distance = flight.ruta.distancia_km || distanceKm(flight.ruta.origen, flight.ruta.destino)
    renderSimulation(flight.ruta.origen, flight.ruta.destino, { mode: 'Vuelo registrado', title: `Vuelo ${flight.codigo}`, subtitle: `${flight.aerolinea?.nombre || 'Sin aerolinea'} | ${flightRoute(flight)}`, distance, minutes: flight.ruta.tiempo_estimado_min || Math.max(45, Math.round((distance / 750) * 60)), status: flight.estado, startProgress: simulationStatusProgress(flight.estado), autoPlay: flight.estado !== 'aterrizado' && flight.estado !== 'cancelado' })
  },
  toggleSimulation() {
    if (!simulation.meta) return Swal.fire({ icon: 'error', title: 'Sin simulacion', text: 'Primero selecciona una ruta manual o un vuelo.', background: '#0f172a', color: '#e2e8f0' })
    if (simulation.playing) {
      stopSimulation()
      syncSimulationControls()
      return
    }
    if (simulation.progress >= 1) simulation.progress = simulation.startProgress
    simulation.playing = true
    simulation.lastTimestamp = null
    syncSimulationControls()
    simulation.rafId = requestAnimationFrame(animateSimulation)
  },
  resetSimulation() {
    if (!simulation.meta) {
      renderSimulationHint()
      return
    }
    stopSimulation()
    simulation.progress = simulation.startProgress
    drawSimulation()
  },
  changeSimulationSpeed(value) {
    simulation.speed = clamp(Number(value) || 1, 0.5, 5)
    syncSimulationControls()
  },
  seekSimulation(value) {
    if (!simulation.meta) return
    simulation.progress = clamp(Number(value) / 100, 0, 1)
    drawSimulation()
  },
  filterAirportMapCountry(value) {
    simulation.selectedCountry = value || ''
    if (simulation.meta) {
      drawSimulation({ fit: true })
      return
    }
    renderSimulationHint({ fit: true })
  },
}

document.addEventListener('DOMContentLoaded', async () => {
  bindViews()
  fillStaticControls()
  renderSimulationHint()
  try { await refreshAll() } catch (error) { Swal.fire({ icon: 'error', title: 'No se pudo iniciar', text: error.message, background: '#0f172a', color: '#e2e8f0' }) }
})
})()
