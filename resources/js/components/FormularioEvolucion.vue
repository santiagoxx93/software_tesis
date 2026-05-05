<template>
  <div class="formulario-evolucion">

    <div class="grid-2">
      <!-- Fecha de consulta -->
      <div class="form-group">
        <label class="form-label" for="evo-fecha">Fecha de consulta *</label>
        <input
          id="evo-fecha"
          v-model="form.fecha_consulta"
          type="date"
          class="form-control"
          :class="{ 'is-invalid': errores.fecha_consulta }"
          :max="hoy"
          required
        />
        <span v-if="errores.fecha_consulta" class="invalid-feedback">{{ errores.fecha_consulta[0] }}</span>
      </div>

      <!-- Cita vinculada (opcional) -->
      <div class="form-group">
        <label class="form-label" for="evo-cita">Cita vinculada</label>
        <select id="evo-cita" v-model="form.cita_id" class="form-select">
          <option value="">— Sin vincular —</option>
          <option v-for="cita in citasCompletadas" :key="cita.id" :value="cita.id">
            {{ formatFecha(cita.fecha) }} — {{ formatHora(cita.hora_inicio) }}
          </option>
        </select>
      </div>
    </div>

    <!-- Evaluación -->
    <div class="form-group">
      <label class="form-label" for="evo-evaluacion">Evaluación clínica *</label>
      <textarea
        id="evo-evaluacion"
        v-model="form.evaluacion"
        class="form-control"
        :class="{ 'is-invalid': errores.evaluacion }"
        rows="4"
        placeholder="Evaluación del estado del paciente en esta sesión..."
        required
      ></textarea>
      <div class="char-count">{{ form.evaluacion.length }} caracteres</div>
      <span v-if="errores.evaluacion" class="invalid-feedback">{{ errores.evaluacion[0] }}</span>
    </div>

    <!-- Tratamiento -->
    <div class="form-group">
      <label class="form-label" for="evo-tratamiento">Tratamiento aplicado *</label>
      <textarea
        id="evo-tratamiento"
        v-model="form.tratamiento_aplicado"
        class="form-control"
        :class="{ 'is-invalid': errores.tratamiento_aplicado }"
        rows="4"
        placeholder="Técnicas y zonas de reflexología podal tratadas..."
        required
      ></textarea>
      <span v-if="errores.tratamiento_aplicado" class="invalid-feedback">{{ errores.tratamiento_aplicado[0] }}</span>
    </div>

    <!-- Respuesta del paciente -->
    <div class="form-group">
      <label class="form-label" for="evo-respuesta">Respuesta del paciente</label>
      <textarea
        id="evo-respuesta"
        v-model="form.respuesta_paciente"
        class="form-control"
        rows="3"
        placeholder="¿Cómo respondió el paciente al tratamiento?"
      ></textarea>
    </div>

    <!-- Plan siguiente sesión -->
    <div class="form-group">
      <label class="form-label" for="evo-plan">Plan para la próxima sesión</label>
      <textarea
        id="evo-plan"
        v-model="form.plan_siguiente_sesion"
        class="form-control"
        rows="3"
        placeholder="Indicaciones y objetivos para la próxima visita..."
      ></textarea>
    </div>

    <!-- Mensaje de éxito -->
    <transition name="fade">
      <div v-if="exito" class="alert alert-success">
        ✓ Evolución registrada exitosamente.
      </div>
    </transition>

    <!-- Errores globales -->
    <transition name="fade">
      <div v-if="errorGeneral" class="alert alert-danger">{{ errorGeneral }}</div>
    </transition>

    <!-- Botón -->
    <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
      <button
        type="button"
        class="btn btn-primary"
        :disabled="enviando || !formularioValido"
        @click="enviar"
        id="btn-guardar-evolucion-vue"
      >
        <span v-if="enviando">Guardando...</span>
        <span v-else>Registrar evolución</span>
      </button>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  rutaGuardar:      { type: String, required: true },
  citasCompletadas: { type: Array,  default: () => [] },
})

const emit = defineEmits(['guardado'])

const hoy  = new Date().toISOString().split('T')[0]
const form = ref({
  fecha_consulta:        hoy,
  cita_id:               '',
  evaluacion:            '',
  tratamiento_aplicado:  '',
  respuesta_paciente:    '',
  plan_siguiente_sesion: '',
})
const errores      = ref({})
const enviando     = ref(false)
const exito        = ref(false)
const errorGeneral = ref('')

const formularioValido = computed(
  () => form.value.fecha_consulta && form.value.evaluacion.trim() && form.value.tratamiento_aplicado.trim()
)

function formatFecha(fecha) {
  return new Date(fecha + 'T00:00:00').toLocaleDateString('es-VE', {
    day: '2-digit', month: '2-digit', year: 'numeric',
  })
}

function formatHora(hora) {
  const [h, m] = hora.split(':')
  const d = new Date(0, 0, 0, +h, +m)
  return d.toLocaleTimeString('es-VE', { hour: '2-digit', minute: '2-digit', hour12: true })
}

async function enviar() {
  if (enviando.value || !formularioValido.value) return
  enviando.value = true
  errores.value  = {}
  errorGeneral.value = ''
  exito.value    = false

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

  try {
    const resp = await fetch(props.rutaGuardar, {
      method: 'POST',
      headers: {
        'Content-Type':     'application/json',
        'X-CSRF-TOKEN':     csrf,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept':           'application/json',
      },
      body: JSON.stringify(form.value),
    })

    if (resp.ok) {
      const data = await resp.json()
      exito.value = true
      emit('guardado', data)
      // Limpiar formulario
      form.value = {
        fecha_consulta:        hoy,
        cita_id:               '',
        evaluacion:            '',
        tratamiento_aplicado:  '',
        respuesta_paciente:    '',
        plan_siguiente_sesion: '',
      }
      setTimeout(() => { exito.value = false }, 4000)
      if (data.redirect) window.location.href = data.redirect
    } else if (resp.status === 422) {
      const data = await resp.json()
      errores.value = data.errors ?? {}
    } else {
      errorGeneral.value = 'Error al guardar. Por favor intenta nuevamente.'
    }
  } catch (e) {
    errorGeneral.value = 'Error de conexión.'
    console.error(e)
  } finally {
    enviando.value = false
  }
}
</script>

<style scoped>
.char-count { font-size: .72rem; color: var(--color-text-muted, #7c84a3); text-align: right; margin-top: .2rem; }
.fade-enter-active, .fade-leave-active { transition: opacity .3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
