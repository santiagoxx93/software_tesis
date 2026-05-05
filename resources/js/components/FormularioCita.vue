<template>
  <div class="formulario-cita">

    <!-- Selección de paciente con buscador -->
    <div class="form-group">
      <label class="form-label">Paciente *</label>
      <BuscadorPacientes
        campo-nombre="paciente_id"
        :valor-inicial="pacienteInicial"
        @seleccionado="onPacienteSeleccionado"
      />
      <span v-if="errores.paciente_id" class="invalid-feedback" style="display:block;">
        {{ errores.paciente_id[0] }}
      </span>
    </div>

    <!-- Especialista -->
    <div class="form-group">
      <label class="form-label" for="select-especialista">Especialista *</label>
      <select
        id="select-especialista"
        v-model="form.especialista_id"
        class="form-select"
        :class="{ 'is-invalid': errores.especialista_id }"
        @change="verificarDisponibilidad"
        required
      >
        <option value="">— Seleccionar especialista —</option>
        <option v-for="e in especialistas" :key="e.id" :value="e.id">
          {{ e.nombres }} {{ e.apellidos }}
        </option>
      </select>
      <span v-if="errores.especialista_id" class="invalid-feedback">{{ errores.especialista_id[0] }}</span>
    </div>

    <!-- Fecha y horas -->
    <div class="grid-3">
      <div class="form-group">
        <label class="form-label" for="input-fecha">Fecha *</label>
        <input
          id="input-fecha"
          v-model="form.fecha"
          type="date"
          class="form-control"
          :class="{ 'is-invalid': errores.fecha }"
          :min="hoy"
          @change="verificarDisponibilidad"
          required
        />
        <span v-if="errores.fecha" class="invalid-feedback">{{ errores.fecha[0] }}</span>
      </div>

      <div class="form-group">
        <label class="form-label" for="input-hora-inicio">Hora inicio *</label>
        <input
          id="input-hora-inicio"
          v-model="form.hora_inicio"
          type="time"
          class="form-control"
          :class="{ 'is-invalid': errores.hora_inicio }"
          @change="autoHoraFin"
          required
        />
        <span v-if="errores.hora_inicio" class="invalid-feedback">{{ errores.hora_inicio[0] }}</span>
      </div>

      <div class="form-group">
        <label class="form-label" for="input-hora-fin">Hora fin *</label>
        <input
          id="input-hora-fin"
          v-model="form.hora_fin"
          type="time"
          class="form-control"
          :class="{ 'is-invalid': errores.hora_fin }"
          required
        />
        <span v-if="errores.hora_fin" class="invalid-feedback">{{ errores.hora_fin[0] }}</span>
      </div>
    </div>

    <!-- Alerta de disponibilidad -->
    <transition name="fade">
      <div v-if="alertaDisponibilidad" :class="['alert', alertaDisponibilidad.tipo]" style="margin-bottom:.75rem;">
        {{ alertaDisponibilidad.mensaje }}
      </div>
    </transition>

    <!-- Motivo -->
    <div class="form-group">
      <label class="form-label" for="input-motivo">Motivo de la cita</label>
      <textarea
        id="input-motivo"
        v-model="form.motivo"
        class="form-control"
        rows="3"
        placeholder="Describe brevemente el motivo de la consulta..."
      ></textarea>
    </div>

    <!-- Botón submit -->
    <div class="flex-between" style="margin-top:1.5rem;">
      <a :href="rutaCancelar" class="btn btn-secondary">Cancelar</a>
      <button
        type="button"
        class="btn btn-primary"
        :disabled="enviando || !formularioValido"
        @click="enviar"
        id="btn-submit-cita"
      >
        <span v-if="enviando">Guardando...</span>
        <span v-else>
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:.3rem;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          {{ textoBoton }}
        </span>
      </button>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import BuscadorPacientes from './BuscadorPacientes.vue'

const props = defineProps({
  especialistas:   { type: Array,  default: () => [] },
  rutaGuardar:     { type: String, required: true },
  rutaCancelar:    { type: String, default: '/citas' },
  metodo:          { type: String, default: 'POST' },
  textoBoton:      { type: String, default: 'Registrar Cita' },
  citaInicial:     { type: Object, default: null },
  pacienteInicial: { type: Object, default: null },
})

const emit = defineEmits(['guardado'])

const hoy    = new Date().toISOString().split('T')[0]
const form   = ref({
  paciente_id:    props.citaInicial?.paciente_id    ?? '',
  especialista_id: props.citaInicial?.especialista_id ?? '',
  fecha:           props.citaInicial?.fecha           ?? '',
  hora_inicio:     props.citaInicial?.hora_inicio?.slice(0,5) ?? '',
  hora_fin:        props.citaInicial?.hora_fin?.slice(0,5)   ?? '',
  motivo:          props.citaInicial?.motivo          ?? '',
  _method:         props.metodo !== 'POST' ? props.metodo : undefined,
})
const errores              = ref({})
const enviando             = ref(false)
const alertaDisponibilidad = ref(null)

const formularioValido = computed(() =>
  form.value.paciente_id &&
  form.value.especialista_id &&
  form.value.fecha &&
  form.value.hora_inicio &&
  form.value.hora_fin
)

function onPacienteSeleccionado(paciente) {
  form.value.paciente_id = paciente?.id ?? ''
}

function autoHoraFin() {
  if (! form.value.hora_inicio) return
  const [h, m] = form.value.hora_inicio.split(':').map(Number)
  const fin = new Date(0, 0, 0, h + 1, m)
  form.value.hora_fin =
    String(fin.getHours()).padStart(2, '0') + ':' + String(fin.getMinutes()).padStart(2, '0')
  verificarDisponibilidad()
}

async function verificarDisponibilidad() {
  alertaDisponibilidad.value = null
  const { especialista_id, fecha, hora_inicio, hora_fin } = form.value
  if (!especialista_id || !fecha || !hora_inicio || !hora_fin) return

  try {
    const params = new URLSearchParams({ especialista_id, fecha, hora_inicio, hora_fin })
    const resp   = await fetch(`/api/citas/disponibilidad?${params}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    })
    const data = await resp.json()
    if (data.cruce) {
      alertaDisponibilidad.value = {
        tipo:    'alert-danger',
        mensaje: '⚠️ El especialista ya tiene una cita en ese horario.',
      }
    } else {
      alertaDisponibilidad.value = {
        tipo:    'alert-success',
        mensaje: '✓ Horario disponible.',
      }
    }
  } catch {
    // silencioso — la validación final ocurre en el servidor
  }
}

async function enviar() {
  if (enviando.value || !formularioValido.value) return
  enviando.value = true
  errores.value  = {}

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  const payload   = { ...form.value }
  if (!payload._method) delete payload._method

  try {
    const resp = await fetch(props.rutaGuardar, {
      method: 'POST',
      headers: {
        'Content-Type':     'application/json',
        'X-CSRF-TOKEN':     csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept':           'application/json',
      },
      body: JSON.stringify({ ...payload, _method: props.metodo }),
    })

    if (resp.ok) {
      const data = await resp.json()
      emit('guardado', data)
      if (data.redirect) window.location.href = data.redirect
    } else if (resp.status === 422) {
      const data = await resp.json()
      errores.value = data.errors ?? {}
    } else {
      alert('Error al guardar la cita. Por favor intenta nuevamente.')
    }
  } catch (e) {
    console.error(e)
    alert('Error de conexión.')
  } finally {
    enviando.value = false
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .25s, transform .25s; }
.fade-enter-from, .fade-leave-to       { opacity: 0; transform: translateY(-4px); }
</style>
