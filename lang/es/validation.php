<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'max' => [
        'string' => 'El campo :attribute no debe ser mayor a :max caracteres.',
    ],
    'unique' => 'Esta :attribute ya se encuentra registrada en el sistema.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'in' => 'El :attribute seleccionado es inválido.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'date_format' => 'El campo :attribute no corresponde al formato :format.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'exists' => 'El :attribute seleccionado es inválido.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
    
    'custom' => [
        'cedula' => [
            'unique' => 'Esta cédula ya se encuentra registrada en el sistema.',
        ],
    ],

    'attributes' => [
        'cedula' => 'cédula',
        'nombres' => 'nombres',
        'apellidos' => 'apellidos',
        'fecha_nacimiento' => 'fecha de nacimiento',
        'sexo' => 'sexo',
        'telefono' => 'teléfono',
        'telefono_emergencia' => 'teléfono de emergencia',
        'email' => 'correo electrónico',
        'direccion' => 'dirección',
        'ocupacion' => 'ocupación',
        'especialista_id' => 'especialista',
        'paciente_id' => 'paciente',
        'fecha' => 'fecha',
        'hora_inicio' => 'hora de inicio',
        'hora_fin' => 'hora de fin',
        'motivo' => 'motivo',
        'motivo_cancelacion' => 'motivo de cancelación',
        'estado' => 'estado',
    ],
];
