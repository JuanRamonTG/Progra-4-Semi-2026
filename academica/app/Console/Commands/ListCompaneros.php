<?php

namespace App\Console\Commands;

use App\Models\Companero;
use Illuminate\Console\Command;

class ListCompaneros extends Command
{
    protected $signature = 'companeros:list';
    protected $description = 'Muestra todos los compañeros registrados';

    public function handle(): int
    {
        $companeros = Companero::all();

        if ($companeros->isEmpty()) {
            $this->info('No hay compañeros registrados.');
            return 0;
        }

        foreach ($companeros as $companero) {
            $this->info(json_encode($companero->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return 0;
    }
}
