<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FcmService;

class TestFcmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:test {token} {--title=Test Notification} {--body=This is a test notification from Makook}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test FCM notification sending';

    /**
     * Execute the console command.
     */
    public function handle(FcmService $fcmService)
    {
        $token = $this->argument('token');
        $title = $this->option('title');
        $body = $this->option('body');

        $this->info("Sending test notification to: {$token}");

        $success = $fcmService->sendNotification($token, $title, $body, ['test' => 'true']);

        if ($success) {
            $this->info('Notification sent successfully!');
        } else {
            $this->error('Failed to send notification. Check logs for details.');
        }
    }
}
