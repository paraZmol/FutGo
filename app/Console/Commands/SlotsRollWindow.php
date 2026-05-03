<?php

namespace App\Console\Commands;

use App\Models\Field;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SlotsRollWindow extends Command
{
    protected $signature   = 'slots:roll-window';
    protected $description = 'Genera slots del dia +30 y elimina slots pasados no reservados. Idempotente (RF-03).';

    public function handle(): int
    {
        $this->info('Iniciando ventana movil de 30 dias...');
        $start   = microtime(true);
        $fields  = Field::with('operatingHours')->where('status', 'active')->get();
        $target  = Carbon::today()->addDays(30);
        $created = 0;
        $deleted = 0;

        foreach ($fields as $field) {
            $dow   = (int) $target->dayOfWeek;
            $sched = $field->operatingHours->firstWhere('day_of_week', $dow);
            if (!$sched || !$sched->is_active) continue;

            $open  = (int) substr($sched->opens_at, 0, 2);
            $close = (int) substr($sched->closes_at, 0, 2);

            for ($h = $open; $h < $close; $h++) {
                $startsAt = $target->copy()->setTime($h, 0);
                // Idempotente: firstOrCreate no duplica
                Slot::firstOrCreate(
                    ['field_id' => $field->id, 'starts_at' => $startsAt],
                    [
                        'ends_at'    => $target->copy()->setTime($h + 1, 0),
                        'status'     => 'available',
                        'unit_price' => $h >= 18
                            ? (float) $sched->price_night
                            : (float) $sched->price_day,
                    ]
                );
                $created++;
            }
        }

        // Limpia slots pasados no reservados (inventario muerto)
        $deleted = Slot::where('ends_at', '<', Carbon::today())
            ->where('status', 'available')
            ->delete();

        $elapsed = round(microtime(true) - $start, 2);
        $this->info("Slots creados/verificados: {$created}");
        $this->info("Slots basura eliminados:   {$deleted}");
        $this->line("Tiempo: {$elapsed}s");
        return Command::SUCCESS;
    }
}
