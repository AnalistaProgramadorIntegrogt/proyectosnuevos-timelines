<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$before = \Carbon\Carbon::parse('2026-06-05 00:00:00');
$validated = '2026-06-11';

$changed = $before != $validated;
echo "Changed? " . ($changed ? "YES" : "NO") . "\n";

$before2 = \Carbon\Carbon::parse('2026-06-11 00:00:00');
$validated2 = '2026-06-11';
$changed2 = $before2 != $validated2;
echo "Changed2? " . ($changed2 ? "YES" : "NO") . "\n";
