<?php

use App\Jobs\TestHorizonJob;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/test-horizon', function () {
    // Dispatch a few test jobs
    for ($i = 1; $i <= 5; $i++) {
        TestHorizonJob::dispatch("Test job #{$i}");
    }
    
    return response()->json([
        'message' => '5 test jobs dispatched to Horizon!',
        'horizon_url' => url('/horizon')
    ]);
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
