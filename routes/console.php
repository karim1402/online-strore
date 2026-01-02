<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fcm:test {token}', function (string $token, \App\Services\FcmService $fcmService) {
    $this->info("Sending test notification to: {$token}");
    $success = $fcmService->sendNotification($token, 'Test Title', 'Test Body');
    if ($success) {
        $this->info('Success!');
    } else {
        $this->error('Failed!');
    }
})->purpose('Test FCM notification');
