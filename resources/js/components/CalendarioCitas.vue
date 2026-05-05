<template>
  <div class="calendario-citas">

    <!-- Controles del calendario -->
    <div class="cal-header">
      <div class="cal-nav">
        <button class="cal-btn" @click="mesAnterior" id="btn-mes-anterior">‹</button>
        <div class="cal-titulo">
          <span class="cal-mes">{{ nombreMes }}</span>
          <span class="cal-anio">{{ anioActual }}</span>
        </div>
        <button class="cal-btn" @click="mesSiguiente" id="btn-mes-siguiente">›</button>
      </div>

      <div class="cal-controles">
        <button
          v-for="modo in modos"
          :key="modo.value"
          :class="['cal-modo-btn', { active: modoVista === modo.value }]"
          @click="modoVista = modo.value"
          :id="`btn-vista-${modo.value}`"
        >{{ modo.label }}</button>
      </div>
    </div>

    <!-- Filtro por especialista -->
    <div class="cal-filtros">
      <select v-model="filtroEspecialista" class="form-select" style="max-width:220px;" id="filtro-especialista-cal">
        <option value="">Todos los especialistas</option>
        <option v-for="e in especialistas" :key="e.id" :value="e.id">
          {{ e.nombres }} {{ e.apellidos }}
        </option>
      </select>
      <span class="text-muted" style="font-size:.8rem;">{{ citasFiltradas.length }} citas en este mes</span>
    </div>

    <!-- Vista MENSUAL -->
    <div v-if="modoVista === 'mes'" class="cal-grid-mes">
      <!-- Cabecera días -->
      <div class="cal-dia-label" v-for="dia in diasSemana" :key="dia">{{ dia }}</div>

      <!-- Celdas del mes -->
      <div
        v-for="celda in celdasMes"
        :key="celda.key"
        :class="['cal-celda', {
          'cal-celda--otro-mes': !celda.esMesActual,
          'cal-celda--hoy':      celda.esHoy,
          'cal-celda--tiene-citas': celda.citas.length > 0,
        }]"
        @click="seleccionarDia(celda)"
        :id="`celda-dia-${celda.fecha}`"
      >
        <div class="cal-celda-num">{{ celda.dia }}</div>
        <div class="cal-celda-citas">
          <div
            v-for="(cita, i) in celda.citas.slice(0, 3)"
            :key="cita.id"
            :class="['cal-pip', `pip-${cita.estado}`]"
            :title="`${cita.hora_inicio} — ${cita.paciente_nombre}`"
          >
            <span class="pip-hora">{{ cita.hora_inicio }}</span>
            <span class="pip-nombre">{{ cita.paciente_nombre }}</span>
          </div>
          <div v-if="celda.citas.length > 3" class="cal-mas">+{{ celda.citas.length - 3 }} más</div>
        </div>
      </div>
    </div>

    <!-- Vista SEMANAL -->
    <div v-else-if="modoVista === 'semana'" class="cal-semana">
      <div class="semana-nav">
        <button class="cal-btn" @click="semanaAnterior" id="btn-semana-anterior">‹ Semana anterior</button>
        <span class="semana-titulo">{{ semanaLabel }}</span>
        <button class="cal-btn" @click="semanaSiguiente" id="btn-semana-siguiente">Semana siguiente ›</button>
      </div>
      <div class="semana-grid">
        <div class="semana-hora-col">
          <div class="semana-hora" v-for="hora in horasLaboral" :key="hora">{{ hora }}</div>
        </div>
        <div class="semana-dias">
          <div class="semana-dia-col" v-for="dia in diasSemanaActual" :key="dia.fecha">
            <div :class="['semana-dia-header', { 'dia-hoy': dia.esHoy }]">
              <span class="semana-dia-nombre">{{ dia.nombre }}</span>
              <span class="semana-dia-num">{{ dia.num }}</span>
            </div>
            <div class="semana-bloques">
              <div
                v-for="cita in citasDelDia(dia.fecha)"
                :key="cita.id"
                :class="['semana-cita', `cita-${cita.estado}`]"
                :style="posicionCita(cita)"
                @click="verCita(cita)"
                :id="`cita-semana-${cita.id}`"
              >
                <div class="semana-cita-hora">{{ cita.hora_inicio }}</div>
                <div class="semana-cita-nombre">{{ cita.paciente_nombre }}</div>
                <estado-badge :estado="cita.estado" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel lateral: detalle del día seleccionado -->
    <transition name="slide">
      <div v-if="diaSeleccionado" class="cal-detalle">
        <div class="detalle-header">
          <strong>{{ formatFechaDetalle(diaSeleccionado.fecha) }}</strong>
          <button class="cal-btn" @click="diaSeleccionado = null">✕</button>
        </div>
        <div v-if="diaSeleccionado.citas.length === 0" class="text-muted" style="padding:.75rem;">
          Sin citas para este día.
        </div>
        <div v-else class="detalle-lista">
          <div
            v-for="cita in diaSeleccionado.citas"
            :key="cita.id"
            class="detalle-cita"
            :id="`detalle-cita-${cita.id}`"
          >
            <div class="detalle-hora">{{ cita.hora_inicio }} — {{ cita.hora_fin }}</div>
            <div class="detalle-nombre">{{ cita.paciente_nombre }}</div>
            <div class="detalle-especialista">{{ cita.especialista_nombre }}</div>
            <estado-badge :estado="cita.estado" />
            <a :href="`/citas/${cita.id}/editar`" class="btn btn-secondary btn-sm" style="margin-top:.5rem;">
              Editar
            </a>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import EstadoBadge from './EstadoBadge.vue'

const props = defineProps({
  citas:        { type: Array,  default: () => [] },
  especialistas: { type: Array,  default: () => [] },
})

// -----------------------------------------------------------------------
// Estado
// -----------------------------------------------------------------------
const hoy               = new Date()
const mesActual         = ref(hoy.getMonth())
const anioActual        = ref(hoy.getFullYear())
const modoVista         = ref('mes')
const filtroEspecialista = ref('')
const diaSeleccionado   = ref(null)
const inicioSemana      = ref(lunes(hoy))

const modos     = [{ value: 'mes', label: 'Mes' }, { value: 'semana', label: 'Semana' }]
const diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']
const horasLaboral = Array.from({ length: 10 }, (_, i) => `${String(i + 8).padStart(2, '0')}:00`)

const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
const nombreMes = computed(() => MESES[mesActual.value])

// -----------------------------------------------------------------------
// Filtrado
// -----------------------------------------------------------------------
const citasFiltradas = computed(() => {
  let lista = props.citas
  if (filtroEspecialista.value) {
    lista = lista.filter(c => c.especialista_id == filtroEspecialista.value)
  }
  return lista.filter(c => {
    const f = new Date(c.fecha + 'T00:00:00')
    return f.getMonth() === mesActual.value && f.getFullYear() === anioActual.value
  })
})

// -----------------------------------------------------------------------
// Vista mensual
// -----------------------------------------------------------------------
const celdasMes = computed(() => {
  const primero  = new Date(anioActual.value, mesActual.value, 1)
  const inicio   = lunes(primero)
  const celdas   = []

  for (let i = 0; i < 42; i++) {
    const fecha = new Date(inicio)
    fecha.setDate(inicio.getDate() + i)
    const iso   = toISO(fecha)
    const citas = citasFiltradas.value.filter(c => c.fecha === iso)
      .sort((a, b) => a.hora_inicio.localeCompare(b.hora_inicio))

    celdas.push({
      key:          iso,
      fecha:        iso,
      dia:          fecha.getDate(),
      esMesActual:  fecha.getMonth() === mesActual.value,
      esHoy:        iso === toISO(hoy),
      citas,
    })
  }
  return celdas
})

// -----------------------------------------------------------------------
// Vista semanal
// -----------------------------------------------------------------------
const diasSemanaActual = computed(() => {
  return Array.from({ length: 7 }, (_, i) => {
    const d = new Date(inicioSemana.value)
    d.setDate(d.getDate() + i)
    return {
      fecha:  toISO(d),
      nombre: diasSemana[i],
      num:    d.getDate(),
      esHoy:  toISO(d) === toISO(hoy),
    }
  })
})

const semanaLabel = computed(() => {
  const inicio = diasSemanaActual.value[0]
  const fin    = diasSemanaActual.value[6]
  return `${inicio.num} — ${fin.num} ${nombreMes.value} ${anioActual.value}`
})

function citasDelDia(fecha) {
  return props.citas.filter(c => c.fecha === fecha)
    .sort((a, b) => a.hora_inicio.localeCompare(b.hora_inicio))
}

function posicionCita(cita) {
  const [h, m] = cita.hora_inicio.split(':').map(Number)
  const top     = ((h - 8) * 60 + m) * (60 / 60) // 60px por hora
  const [hf, mf] = cita.hora_fin.split(':').map(Number)
  const height   = ((hf - h) * 60 + (mf - m)) * (60 / 60)
  return { top: `${top}px`, height: `${Math.max(height, 30)}px` }
}

// -----------------------------------------------------------------------
// Navegación
// -----------------------------------------------------------------------
function mesAnterior() {
  if (mesActual.value === 0) { mesActual.value = 11; anioActual.value-- }
  else mesActual.value--
}
function mesSiguiente() {
  if (mesActual.value === 11) { mesActual.value = 0; anioActual.value++ }
  else mesActual.value++
}
function semanaAnterior()  { inicioSemana.value = addDays(inicioSemana.value, -7) }
function semanaSiguiente() { inicioSemana.value = addDays(inicioSemana.value, +7) }

// -----------------------------------------------------------------------
// Acciones
// -----------------------------------------------------------------------
function seleccionarDia(celda) {
  diaSeleccionado.value = celda
}
function verCita(cita) {
  window.location.href = `/citas/${cita.id}/editar`
}

// -----------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------
function lunes(fecha) {
  const d    = new Date(fecha)
  const dia  = d.getDay()
  const diff = dia === 0 ? -6 : 1 - dia
  d.setDate(d.getDate() + diff)
  d.setHours(0, 0, 0, 0)
  return d
}
function toISO(d) {
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
}
function addDays(fecha, dias) {
  const d = new Date(fecha)
  d.setDate(d.getDate() + dias)
  return d
}
function formatFechaDetalle(iso) {
  return new Date(iso + 'T00:00:00').toLocaleDateString('es-VE', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
  })
}
</script>

<style scoped>
/* ======= HEADER ======= */
.calendario-citas { display: flex; flex-direction: column; gap: 1rem; }
.cal-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem; }
.cal-nav { display: flex; align-items: center; gap: .75rem; }
.cal-titulo { display: flex; flex-direction: column; align-items: center; min-width: 140px; }
.cal-mes  { font-size: 1.1rem; font-weight: 700; }
.cal-anio { font-size: .8rem; color: var(--color-text-muted, #7c84a3); }
.cal-btn {
  background: var(--color-surface-2, #22263a);
  border: 1px solid var(--color-border, #2e3248);
  color: var(--color-text, #e2e6f3);
  border-radius: 8px;
  padding: .4rem .8rem;
  cursor: pointer;
  font-size: .85rem;
  transition: background .15s;
}
.cal-btn:hover { background: var(--color-border, #2e3248); }
.cal-controles { display: flex; gap: .4rem; }
.cal-modo-btn {
  padding: .35rem .8rem;
  border-radius: 20px;
  border: 1px solid var(--color-border, #2e3248);
  background: transparent;
  color: var(--color-text-muted, #7c84a3);
  font-size: .8rem; cursor: pointer;
  transition: all .15s;
}
.cal-modo-btn.active {
  background: var(--color-primary, #4f6ef7);
  color: #fff; border-color: var(--color-primary, #4f6ef7);
}

/* ======= FILTROS ======= */
.cal-filtros { display: flex; align-items: center; gap: 1rem; }

/* ======= GRID MES ======= */
.cal-grid-mes {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
  background: var(--color-border, #2e3248);
  border-radius: 10px;
  overflow: hidden;
}
.cal-dia-label {
  background: var(--color-surface, #1a1d27);
  text-align: center;
  padding: .5rem;
  font-size: .72rem; font-weight: 600; letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--color-text-muted, #7c84a3);
}
.cal-celda {
  background: var(--color-surface, #1a1d27);
  min-height: 90px;
  padding: .4rem;
  cursor: pointer;
  transition: background .15s;
}
.cal-celda:hover { background: var(--color-surface-2, #22263a); }
.cal-celda--otro-mes { opacity: .35; }
.cal-celda--hoy .cal-celda-num {
  background: var(--color-primary, #4f6ef7);
  color: #fff;
  border-radius: 50%;
  width: 24px; height: 24px;
  display: flex; align-items: center; justify-content: center;
}
.cal-celda-num { font-size: .82rem; font-weight: 600; margin-bottom: .3rem; }
.cal-pip {
  display: flex; align-items: center; gap: .3rem;
  padding: .15rem .4rem;
  border-radius: 4px;
  font-size: .68rem;
  margin-bottom: 2px;
  overflow: hidden;
}
.pip-pendiente   { background: rgba(245,158,11,.2); color: #f59e0b; }
.pip-confirmada  { background: rgba(56,189,248,.2); color: #38bdf8; }
.pip-completada  { background: rgba(34,197,94,.2);  color: #22c55e; }
.pip-cancelada   { background: rgba(239,68,68,.2);  color: #ef4444; }
.pip-ausente     { background: rgba(124,132,163,.15); color: #7c84a3; }
.pip-reprogramada { background: rgba(124,94,247,.2); color: #7c5ef7; }
.pip-hora        { font-weight: 600; flex-shrink: 0; }
.pip-nombre      { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cal-mas         { font-size: .65rem; color: var(--color-text-muted, #7c84a3); padding: .1rem .4rem; }

/* ======= SEMANA ======= */
.semana-nav { display: flex; align-items: center; justify-content: space-between; margin-bottom: .75rem; }
.semana-titulo { font-weight: 600; }
.semana-grid { display: flex; gap: 0; border: 1px solid var(--color-border, #2e3248); border-radius: 10px; overflow: hidden; }
.semana-hora-col {
  width: 52px; flex-shrink: 0;
  background: var(--color-surface, #1a1d27);
  border-right: 1px solid var(--color-border, #2e3248);
}
.semana-hora { height: 60px; display: flex; align-items: flex-start; justify-content: center; padding-top: .2rem; font-size: .65rem; color: var(--color-text-muted, #7c84a3); }
.semana-dias { display: grid; grid-template-columns: repeat(7, 1fr); flex: 1; }
.semana-dia-col { border-left: 1px solid var(--color-border, #2e3248); }
.semana-dia-header { text-align: center; padding: .4rem .2rem; border-bottom: 1px solid var(--color-border, #2e3248); background: var(--color-surface, #1a1d27); }
.dia-hoy { background: rgba(79,110,247,.1) !important; }
.semana-dia-nombre { display: block; font-size: .65rem; color: var(--color-text-muted, #7c84a3); text-transform: uppercase; }
.semana-dia-num    { display: block; font-size: .9rem; font-weight: 700; }
.semana-bloques { position: relative; height: 600px; background: var(--color-bg, #0f1117); }
.semana-cita {
  position: absolute;
  left: 2px; right: 2px;
  border-radius: 6px;
  padding: .2rem .4rem;
  font-size: .68rem;
  cursor: pointer;
  overflow: hidden;
  border-left: 3px solid;
  transition: opacity .15s;
}
.semana-cita:hover { opacity: .85; }
.cita-pendiente   { background: rgba(245,158,11,.2); border-color: #f59e0b; }
.cita-confirmada  { background: rgba(56,189,248,.2); border-color: #38bdf8; }
.cita-completada  { background: rgba(34,197,94,.2);  border-color: #22c55e; }
.cita-cancelada   { background: rgba(239,68,68,.2);  border-color: #ef4444; }
.cita-ausente     { background: rgba(124,132,163,.15); border-color: #7c84a3; }
.semana-cita-hora { font-weight: 700; }

/* ======= DETALLE ======= */
.cal-detalle {
  background: var(--color-surface, #1a1d27);
  border: 1px solid var(--color-border, #2e3248);
  border-radius: 10px;
  overflow: hidden;
}
.detalle-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: .75rem 1rem;
  background: var(--color-surface-2, #22263a);
  border-bottom: 1px solid var(--color-border, #2e3248);
  font-size: .9rem;
}
.detalle-lista { display: flex; flex-direction: column; gap: .75rem; padding: .75rem 1rem; }
.detalle-cita {
  background: var(--color-surface-2, #22263a);
  border: 1px solid var(--color-border, #2e3248);
  border-radius: 8px;
  padding: .75rem;
}
.detalle-hora        { font-size: .78rem; color: var(--color-text-muted, #7c84a3); margin-bottom: .2rem; }
.detalle-nombre      { font-weight: 600; }
.detalle-especialista { font-size: .78rem; color: var(--color-text-muted, #7c84a3); margin-bottom: .4rem; }

/* Transiciones */
.slide-enter-active, .slide-leave-active { transition: all .3s ease; }
.slide-enter-from { opacity: 0; transform: translateY(10px); }
.slide-leave-to   { opacity: 0; transform: translateY(10px); }
</style>
