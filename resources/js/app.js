import './bootstrap';
import { createApp } from 'vue';

// Importar componentes
import CalendarioCitas      from './components/CalendarioCitas.vue';
import FormularioCita       from './components/FormularioCita.vue';
import BuscadorPacientes    from './components/BuscadorPacientes.vue';
import FormularioEvolucion  from './components/FormularioEvolucion.vue';
import EstadoBadge          from './components/EstadoBadge.vue';

/**
 * Monta una app Vue en cada elemento que tenga el atributo [data-vue-app].
 * Esto permite usar múltiples componentes Vue en distintas páginas Blade
 * sin un SPA completo.
 */
document.querySelectorAll('[data-vue-app]').forEach((el) => {
    const app = createApp({});

    app.component('CalendarioCitas',     CalendarioCitas);
    app.component('FormularioCita',      FormularioCita);
    app.component('BuscadorPacientes',   BuscadorPacientes);
    app.component('FormularioEvolucion', FormularioEvolucion);
    app.component('EstadoBadge',         EstadoBadge);

    app.mount(el);
});
