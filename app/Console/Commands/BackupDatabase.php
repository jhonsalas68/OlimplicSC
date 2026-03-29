<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extrae la base de datos PostgreSQL, la comprime y la envía por correo electrónico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando extracción de Base de Datos...');

        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $port = env('DB_PORT', '5432');
        
        $emailDestination = env('BACKUP_EMAIL_TO', env('MAIL_FROM_ADDRESS'));

        if (!$emailDestination) {
            $this->error('Falta definir BACKUP_EMAIL_TO o MAIL_FROM_ADDRESS en el archivo .env');
            return;
        }

        $date = now()->format('Y-m-d');
        $filename = "olimpicsc_backup_{$date}.sql";
        $filepath = storage_path("app/" . $filename);

        // Ocultar la contraseña usando variable de entorno temporal
        putenv("PGPASSWORD={$password}");
        
        // El comando pg_dump para PostgreSQL
        $command = "pg_dump -U {$username} -h {$host} -p {$port} {$database} --clean --if-exists > {$filepath} 2>&1";
        exec($command, $output, $resultCode);
        
        putenv("PGPASSWORD="); // Limpiar contraseña por seguridad

        if ($resultCode === 0 && file_exists($filepath)) {
            $this->info("Backup finalizado con exito. Enviando correo a {$emailDestination}...");
            
            try {
                Mail::raw("Adjunto encontrarás el resguardo automático de la base de datos de OlimpicSC del día {$date}. ¡Guárdalo en un lugar seguro!", function ($message) use ($emailDestination, $filepath, $filename) {
                    $message->to($emailDestination)
                            ->subject("OlimpicSC - Respaldo Diario de Base de Datos ({$filename})")
                            ->attach($filepath);
                });
                $this->info('Correo enviado exitosamente.');
                Log::info("Backup DB completado y enviado por correo a {$emailDestination}");
            } catch (\Exception $e) {
                $this->error('Error al enviar el correo: ' . $e->getMessage());
                Log::error('Fallo el envio del Backup DB por correo: ' . $e->getMessage());
            }

            // Eliminar el archivo después de enviarlo para no consumir disco
            unlink($filepath);
        } else {
            $this->error('Fallo el comando pg_dump: ' . implode("\n", $output));
            Log::error('Fallo en la creacion del Backup DB con pg_dump');
        }
    }
}
