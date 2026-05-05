<template>
  <div class="buscador-pacientes">
    <!-- Input de búsqueda -->
    <div class="search-wrapper">
      <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input
        v-model="query"
        @input="buscar"
        type="text"
        class="form-control search-input"
        :placeholder="placeholder"
        autocomplete="off"
        id="buscador-paciente-input"
      />
      <span v-if="cargando" class="search-spinner">⏳</span>
      <button v-if="query" @click="limpiar" class="search-clear" title="Limpiar">✕</button>
    </div>

    <!-- Resultados dropdown -->
    <div v-if="mostrarResultados" class="resultados-dropdown">
      <div v-if="cargando" class="resultado-item text-muted">Buscando...</div>

      <div v-else-if="resultados.length === 0 && query.length >= 2" class="resultado-item text-muted">
        No se encontraron pacientes para "{{ query }}"
      </div>

      <div
        v-for="paciente in resultados"
        :key="paciente.id"
        class="resultado-item"
        @click="seleccionar(paciente)"
        :id="`resultado-paciente-${paciente.id}`"
      >
        <div class="resultado-avatar">
          {{ iniciales(paciente.nombres, paciente.apellidos) }}
        </div>
        <div class="resultado-info">
          <div class="resultado-nombre">{{ paciente.nombres }} {{ paciente.apellidos }}</div>
          <div class="resultado-sub">{{ paciente.cedula }} · {{ paciente.telefono ?? 'Sin teléfono' }}</div>
        </div>
      </div>
    </div>

    <!-- Campo seleccionado -->
    <div v-if="seleccionado" class="seleccion-actual">
      <div class="seleccion-avatar">{{ iniciales(seleccionado.nombres, seleccionado.apellidos) }}</div>
      <div class="seleccion-info">
        <span class="seleccion-nombre">{{ seleccionado.nombres }} {{ seleccionado.apellidos }}</span>
        <span class="seleccion-cedula">{{ seleccionado.cedula }}</span>
      </div>
      <button class="seleccion-quitar" @click="quitarSeleccion" title="Cambiar">✕</button>
      <!-- Hidden input para el formulario -->
      <input type="hidden" :name="campoNombre" :value="seleccionado.id">
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  placeholder:  { type: String, default: 'Buscar por nombre o cédula...' },
  campoNombre:  { type: String, default: 'paciente_id' },
  valorInicial: { type: Object, default: null },
  rutaBusqueda: { type: String, default: '/pacientes' },
})

const emit = defineEmits(['seleccionado'])

const query          = ref('')
const resultados     = ref([])
const cargando       = ref(false)
const seleccionado   = ref(props.valorInicial)
let   debounceTimer  = null

const mostrarResultados = computed(
  () => !seleccionado.value && (cargando.value || resultados.value.length > 0 || query.value.length >= 2)
)

function iniciales(nombres, apellidos) {
  return ((nombres?.[0] ?? '') + (apellidos?.[0] ?? '')).toUpperCase()
}

function buscar() {
  clearTimeout(debounceTimer)
  resultados.value = []

  if (query.value.length < 2) return

  cargando.value = true
  debounceTimer = setTimeout(async () => {
    try {
      const url = `${props.rutaBusqueda}?buscar=${encodeURIComponent(query.value)}&_format=json`
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      })
      if (res.ok) {
        const data = await res.json()
        resultados.value = Array.isArray(data) ? data : (data.data ?? [])
      }
    } catch (e) {
      console.error('Error al buscar pacientes:', e)
    } finally {
      cargando.value = false
    }
  }, 350)
}

function seleccionar(paciente) {
  seleccionado.value = paciente
  query.value        = ''
  resultados.value   = []
  emit('seleccionado', paciente)
}

function quitarSeleccion() {
  seleccionado.value = null
  emit('seleccionado', null)
}

function limpiar() {
  query.value      = ''
  resultados.value = []
}
</script>

<style scoped>
.buscador-pacientes { position: relative; }

.search-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.search-icon {
  position: absolute;
  left: .75rem;
  width: 16px; height: 16px;
  color: var(--color-text-muted, #7c84a3);
  pointer-events: none;
}
.search-input { padding-left: 2.5rem !important; }
.search-spinner {
  position: absolute; right: 2rem;
  font-size: .8rem;
  animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.search-clear {
  position: absolute; right: .6rem;
  background: none; border: none;
  color: var(--color-text-muted, #7c84a3);
  cursor: pointer; font-size: .9rem;
  line-height: 1;
  padding: .2rem;
}
.search-clear:hover { color: var(--color-danger, #ef4444); }

.resultados-dropdown {
  position: absolute;
  top: calc(100% + 4px);
  left: 0; right: 0;
  background: var(--color-surface, #1a1d27);
  border: 1px solid var(--color-border, #2e3248);
  border-radius: 10px;
  box-shadow: 0 12px 32px rgba(0,0,0,.5);
  z-index: 200;
  max-height: 300px;
  overflow-y: auto;
}
.resultado-item {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .75rem 1rem;
  cursor: pointer;
  transition: background .15s;
  border-bottom: 1px solid rgba(46,50,72,.4);
}
.resultado-item:last-child { border-bottom: none; }
.resultado-item:hover { background: var(--color-surface-2, #22263a); }
.resultado-avatar {
  width: 34px; height: 34px;
  background: linear-gradient(135deg, #4f6ef7, #7c5ef7);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .75rem; font-weight: 700; color: #fff;
  flex-shrink: 0;
}
.resultado-nombre { font-size: .875rem; font-weight: 500; color: var(--color-text, #e2e6f3); }
.resultado-sub    { font-size: .72rem; color: var(--color-text-muted, #7c84a3); }

.seleccion-actual {
  display: flex;
  align-items: center;
  gap: .75rem;
  background: rgba(79,110,247,.08);
  border: 1px solid rgba(79,110,247,.25);
  border-radius: 8px;
  padding: .6rem .9rem;
  margin-top: .5rem;
}
.seleccion-avatar {
  width: 30px; height: 30px;
  background: linear-gradient(135deg, #4f6ef7, #7c5ef7);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .7rem; font-weight: 700; color: #fff;
  flex-shrink: 0;
}
.seleccion-nombre { font-size: .875rem; font-weight: 600; color: var(--color-text, #e2e6f3); margin-right: .4rem; }
.seleccion-cedula { font-size: .75rem; color: var(--color-text-muted, #7c84a3); }
.seleccion-quitar {
  margin-left: auto;
  background: none; border: none;
  color: var(--color-text-muted, #7c84a3);
  cursor: pointer; font-size: .85rem;
}
.seleccion-quitar:hover { color: var(--color-danger, #ef4444); }
</style>
