<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyChallenge;
use App\Models\Palabra;
use Carbon\Carbon;

class GenerateDailyChallenge extends Command
{
    protected $signature   = 'app:generate-daily-challenge';
    protected $description = 'Selecciona un conjunto de palabras aleatorias con vídeo más votado válido como reto diario';

    private const WORDS_PER_DAY = 5;

    public function handle(): int
    {
        $today = now()->toDateString();
        if (DailyChallenge::whereDate('created_at', $today)->exists()) {
            $this->info('Ya existe un reto para hoy (' . $today . ').');
            return self::SUCCESS;
        }   
        
        $palabras = Palabra::query()
            ->inRandomOrder()
            ->with(['significado.highestVotedVideo' => fn ($q) => $q
                ->whereNotNull('url')
            ])
            ->whereHas('significado.highestVotedVideo', fn ($q) => $q
                ->whereNotNull('url')
            )
            ->limit(self::WORDS_PER_DAY)
            ->get();

        if ($palabras->isEmpty()) {
            $this->error('No se encontraron palabras elegibles con vídeo válido.');
            return self::FAILURE;
        }

        foreach ($palabras as $palabra) {
            $video = $palabra->significado->highestVotedVideo;

            if (!$video) {
                $this->warn("Palabra {$palabra->nombre} omitida: sin vídeo válido.");
                continue;
            }

            DailyChallenge::create([
                'palabra_id' => $palabra->id,
                'video_id'   => $video->id,
            ]);
        }

        $lista = $palabras->pluck('nombre')->join(', ');
        $this->info("Reto diario creado con: {$lista}");
        return self::SUCCESS;
    }
}
