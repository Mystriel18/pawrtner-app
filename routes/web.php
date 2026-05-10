<?php

use App\Services\ReminderDispatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return view('welcome');
    }

    if ($user->hasRole('admin')) {
        return redirect()->route('filament.admin.pages.dashboard');
    }

    if ($user->hasRole('vet')) {
        return redirect()->route('filament.vet.pages.dashboard');
    }

    if ($user->hasRole('client')) {
        return redirect()->route('filament.client.pages.dashboard');
    }

    return view('welcome');
});

$dispatchRemindersResponse = function (Request $request, ReminderDispatchService $reminders) {
    $type = (string) $request->input('type', 'all');
    $isDryRun = $request->boolean('dry_run', false);
    $summary = $reminders->dispatch($type, $isDryRun);

    return response()->json([
        'ok' => true,
        'message' => 'Reminder dispatch completed.',
        'summary' => $summary,
    ]);
};

Route::match(['GET', 'POST'], '/internal/reminders/dispatch', function (Request $request, ReminderDispatchService $reminders) use ($dispatchRemindersResponse) {
    $expectedKey = (string) env('REMINDER_DISPATCH_KEY', '');

    if ($expectedKey === '') {
        abort(503, 'Reminder dispatch key is not configured.');
    }

    $providedKey = (string) ($request->header('X-Reminder-Key')
        ?? $request->query('key')
        ?? $request->input('key', ''));

    if ($providedKey === '' || ! hash_equals($expectedKey, $providedKey)) {
        abort(403);
    }

    return $dispatchRemindersResponse($request, $reminders);
})->middleware('throttle:6,1');

Route::get('/internal/reminders/dispatch/signed', function (Request $request, ReminderDispatchService $reminders) use ($dispatchRemindersResponse) {
    return $dispatchRemindersResponse($request, $reminders);
})
    ->name('internal.reminders.dispatch.signed')
    ->middleware(['signed', 'throttle:6,1']);
