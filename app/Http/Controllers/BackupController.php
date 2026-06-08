<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Asegurarse de que solo el administrador puede acceder a los backups
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Ejecuta el comando de backup y descarga el archivo resultante
     */
    public function download()
    {
        try {
            // Ejecutamos el respaldo solo de la base de datos para que sea rápido
            Artisan::call('backup:run', ['--only-db' => true]);
            
            // Buscamos el archivo más reciente en el disco local dentro de la carpeta del nombre de la app
            $backupName = config('backup.backup.name');
            $files = Storage::disk('local')->files($backupName);
            
            if (empty($files)) {
                return back()->with('error', 'No se pudo generar el respaldo. Verifique los logs del sistema.');
            }

            // Ordenar por última modificación y obtener el más reciente
            $latestBackup = collect($files)->sortByDesc(function ($file) {
                return Storage::disk('local')->lastModified($file);
            })->first();

            // Descargar el archivo
            return Storage::disk('local')->download($latestBackup);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el respaldo: ' . $e->getMessage());
        }
    }
}
