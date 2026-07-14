<?php
require __DIR__.'/vendor/autoload.php';
\ = require_once __DIR__.'/bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();
\ = App\Models\Task::whereHas('responsibles')->first();
echo json_encode(['id' => \->id ?? null, 'start' => \->calculated_start_date ?? null, 'end' => \->calculated_end_date ?? null]);
