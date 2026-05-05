<template>
  <span :class="badgeClass">{{ label }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  estado: {
    type: String,
    required: true,
  },
})

const ESTADOS = {
  pendiente:    { label: 'Pendiente',    cls: 'badge-pendiente'    },
  confirmada:   { label: 'Confirmada',   cls: 'badge-confirmada'   },
  completada:   { label: 'Completada',   cls: 'badge-completada'   },
  cancelada:    { label: 'Cancelada',    cls: 'badge-cancelada'    },
  ausente:      { label: 'Ausente',      cls: 'badge-ausente'      },
  reprogramada: { label: 'Reprogramada', cls: 'badge-reprogramada' },
}

const info       = computed(() => ESTADOS[props.estado] ?? { label: props.estado, cls: '' })
const label      = computed(() => info.value.label)
const badgeClass = computed(() => ['badge', info.value.cls])
</script>
