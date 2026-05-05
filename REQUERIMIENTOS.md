# Sistema de Gestión de Citas e Historias Clínicas (Centro San Alfonso)

## Descripción del Proyecto
Desarrollo de un sistema de información web a la medida para optimizar la gestión y control de las historias clínicas y citas de pacientes en el Centro Integral de Reflexología Podal San Alfonso, C.A. El objetivo principal es erradicar el registro manual físico, evitar la duplicidad de tareas, reducir el ausentismo y garantizar la seguridad de la información clínica.

## Stack Tecnológico y Arquitectura
*   **Arquitectura:** Modelo-Vista-Controlador (MVC).
*   **Backend:** PHP utilizando el framework Laravel.
*   **Frontend:** Vue.js y JavaScript (interfaces dinámicas y responsivas).
*   **Base de Datos:** MySQL (Modelo Relacional).

## Módulos Principales (Requerimientos Funcionales)

### 1. Módulo de Gestión de Citas
*   **Agendamiento:** Interfaz para registrar nuevas citas médicas asignando un bloque de tiempo específico.
*   **Validación de Duplicidad:** El sistema debe evitar el cruce de horarios o la asignación de dos pacientes al mismo terapeuta en la misma hora.
*   **Gestión de Estados:** Capacidad para confirmar asistencia, cancelar o reprogramar citas.
*   **Control de Ausentismo:** Registro y seguimiento de citas perdidas.

### 2. Módulo de Historia Clínica Digital
*   **Registro de Pacientes:** Creación de perfiles con datos personales y de contacto.
*   **Evolución Clínica:** Creación y actualización de expedientes médicos digitales detallando los tratamientos de reflexología podal.
*   **Trazabilidad:** Organización cronológica de las consultas y legibilidad garantizada.
*   **Búsqueda Rápida:** Filtros eficientes para ubicar historias clínicas en segundos.

### 3. Módulo de Reportes y Estadísticas
*   Generación de métricas sobre la asistencia de pacientes, volumen de citas atendidas y estadísticas de ausentismo para la toma de decisiones gerenciales.

## Requerimientos No Funcionales y de Seguridad

*   **Autenticación y Control de Acceso (Roles):** 
    *   *Rol Administrativo (Recepción):* Acceso limitado únicamente a datos de contacto y agenda de citas.
    *   *Rol Especialista (Terapeutas/Médicos):* Acceso total a los expedientes y evolución clínica de los pacientes (Garantizando el Secreto Médico).
*   **Respaldos de Seguridad (Backups):** Implementación de mecanismos para exportar o respaldar la base de datos de manera regular.
*   **Integridad de Datos:** Protección contra la modificación no autorizada de los diagnósticos.
*   **Disponibilidad:** El sistema debe ser accesible desde cualquier dispositivo con navegador web moderno dentro de la red del centro.