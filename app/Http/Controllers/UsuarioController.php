<?php

namespace App\Http\Controllers;

use App\Models\Especialista;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Lista todos los usuarios (trabajadores) del sistema.
     */
    public function index(Request $request): View
    {
        $query = User::with('especialista')->orderBy('name');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('name', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('rol')) {
            $query->role($request->rol);
        }

        $usuarios = $query->paginate(15)->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Formulario para crear un nuevo trabajador.
     */
    public function create(): View
    {
        return view('usuarios.create');
    }

    /**
     * Guarda un nuevo trabajador en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:6', 'confirmed'],
            'rol'           => ['required', 'in:admin,especialista'],
            // Campos de especialista (opcionales, requeridos si rol = especialista)
            'cedula'        => ['required_if:rol,especialista', 'nullable', 'string', 'max:20'],
            'nombres'       => ['required_if:rol,especialista', 'nullable', 'string', 'max:100'],
            'apellidos'     => ['required_if:rol,especialista', 'nullable', 'string', 'max:100'],
            'especialidad'  => ['required_if:rol,especialista', 'nullable', 'string', 'max:150'],
            'telefono'      => ['nullable', 'string', 'max:20'],
            'hora_entrada'  => ['required_if:rol,especialista', 'nullable', 'date_format:H:i'],
            'hora_salida'   => ['required_if:rol,especialista', 'nullable', 'date_format:H:i', 'after:hora_entrada'],
            'dias_laborables'=> ['required_if:rol,especialista', 'nullable', 'array', 'min:1'],
        ]);

        // Crear el usuario
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'activo'   => true,
        ]);

        // Asignar rol con Spatie
        $user->assignRole($validated['rol']);

        // Si es especialista, crear su perfil en la tabla especialistas
        if ($validated['rol'] === 'especialista') {
            Especialista::create([
                'user_id'      => $user->id,
                'cedula'       => $validated['cedula'],
                'nombres'      => $validated['nombres'],
                'apellidos'    => $validated['apellidos'],
                'especialidad' => $validated['especialidad'],
                'telefono'     => $validated['telefono'] ?? null,
                'hora_entrada' => $validated['hora_entrada'] ?? '08:00',
                'hora_salida'  => $validated['hora_salida'] ?? '17:00',
                'dias_laborables' => $validated['dias_laborables'] ?? [1,2,3,4,5],
                'activo'       => true,
            ]);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Trabajador registrado exitosamente.');
    }

    /**
     * Formulario para editar un trabajador existente.
     */
    public function edit(User $usuario): View
    {
        $usuario->load('especialista');

        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Actualiza los datos de un trabajador.
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'password'      => ['nullable', 'string', 'min:6', 'confirmed'],
            'activo'        => ['required', 'boolean'],
            // Campos de especialista
            'cedula'        => ['nullable', 'string', 'max:20'],
            'nombres'       => ['nullable', 'string', 'max:100'],
            'apellidos'     => ['nullable', 'string', 'max:100'],
            'especialidad'  => ['nullable', 'string', 'max:150'],
            'telefono'      => ['nullable', 'string', 'max:20'],
            'hora_entrada'  => ['nullable', 'date_format:H:i'],
            'hora_salida'   => ['nullable', 'date_format:H:i', 'after:hora_entrada'],
            'dias_laborables'=> ['nullable', 'array', 'min:1'],
        ]);

        // Actualizar usuario
        $datosUsuario = [
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'activo' => $validated['activo'],
        ];

        // Solo actualizar contraseña si se proporcionó una nueva
        if (!empty($validated['password'])) {
            $datosUsuario['password'] = $validated['password'];
        }

        $usuario->update($datosUsuario);

        // Si es especialista, actualizar su perfil
        if ($usuario->hasRole('especialista') && $usuario->especialista) {
            $usuario->especialista->update([
                'cedula'       => $validated['cedula'],
                'nombres'      => $validated['nombres'],
                'apellidos'    => $validated['apellidos'],
                'especialidad' => $validated['especialidad'],
                'telefono'     => $validated['telefono'] ?? null,
                'hora_entrada' => $validated['hora_entrada'] ?? $usuario->especialista->hora_entrada,
                'hora_salida'  => $validated['hora_salida'] ?? $usuario->especialista->hora_salida,
                'dias_laborables' => $validated['dias_laborables'] ?? $usuario->especialista->dias_laborables,
                'activo'       => $validated['activo'],
            ]);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Trabajador actualizado correctamente.');
    }
}
