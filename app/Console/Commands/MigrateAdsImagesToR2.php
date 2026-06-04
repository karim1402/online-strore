<?php

namespace App\Console\Commands;

use App\Models\HomeAd;
use App\Models\ModuleAd;
use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateAdsImagesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:migrate-to-r2 {--delete-local : Delete local files after successful upload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing Home Ads, Module Ads, and Modules images from local public storage to Cloudflare R2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleteLocal = $this->option('delete-local');

        $this->info('Starting Images Migration to Cloudflare R2...');

        // 1. Migrate Home Ads
        $this->migrateHomeAds($deleteLocal);

        // 2. Migrate Module Ads
        $this->migrateModuleAds($deleteLocal);

        // 3. Migrate Modules
        $this->migrateModules($deleteLocal);

        $this->info('All migrations completed successfully!');
    }

    private function migrateHomeAds(bool $deleteLocal)
    {
        $homeAds = HomeAd::all();
        $this->info("Found {$homeAds->count()} Home Ads to check.");

        $bar = $this->output->createProgressBar($homeAds->count());
        $bar->start();

        foreach ($homeAds as $ad) {
            $updated = false;

            // Handle legacy 'image' field (local public)
            if ($ad->image && !filter_var($ad->image, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($ad->image)) {
                    $fileName = basename($ad->image);
                    $newPath = 'home_ads/v2/' . $fileName;

                    $fileContent = Storage::disk('public')->get($ad->image);
                    if (Storage::disk('r2')->put($newPath, $fileContent, 'public')) {
                        $oldPath = $ad->image;
                        $ad->image_v2 = $newPath;
                        $ad->image = null; // Clear legacy field
                        $ad->save();
                        $updated = true;

                        if ($deleteLocal) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
            }

            // Handle 'image_v2' if stored locally
            if ($ad->image_v2 && !filter_var($ad->image_v2, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($ad->image_v2)) {
                    $fileContent = Storage::disk('public')->get($ad->image_v2);
                    if (Storage::disk('r2')->put($ad->image_v2, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($ad->image_v2);
                        }
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Home Ads migration step finished.');
    }

    private function migrateModuleAds(bool $deleteLocal)
    {
        $moduleAds = ModuleAd::all();
        $this->info("Found {$moduleAds->count()} Module Ads to check.");

        $bar = $this->output->createProgressBar($moduleAds->count());
        $bar->start();

        foreach ($moduleAds as $ad) {
            if ($ad->image && !filter_var($ad->image, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($ad->image)) {
                    $fileContent = Storage::disk('public')->get($ad->image);
                    if (Storage::disk('r2')->put($ad->image, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($ad->image);
                        }
                    }
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Module Ads migration step finished.');
    }

    private function migrateModules(bool $deleteLocal)
    {
        $modules = Module::all();
        $this->info("Found {$modules->count()} Modules to check.");

        $bar = $this->output->createProgressBar($modules->count());
        $bar->start();

        foreach ($modules as $module) {
            $updated = false;

            // Handle legacy 'image' field (local public)
            if ($module->image && !filter_var($module->image, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($module->image)) {
                    $fileName = basename($module->image);
                    $newPath = 'modules/v2/' . $fileName;

                    $fileContent = Storage::disk('public')->get($module->image);
                    if (Storage::disk('r2')->put($newPath, $fileContent, 'public')) {
                        $oldPath = $module->image;
                        $module->image_v2 = $newPath;
                        $module->image = null;
                        $updated = true;
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
            }

            // Handle legacy 'image_ar' field (local public)
            if ($module->image_ar && !filter_var($module->image_ar, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($module->image_ar)) {
                    $fileName = basename($module->image_ar);
                    $newPath = 'modules/v2/' . $fileName;

                    $fileContent = Storage::disk('public')->get($module->image_ar);
                    if (Storage::disk('r2')->put($newPath, $fileContent, 'public')) {
                        $oldPath = $module->image_ar;
                        $module->image_ar_v2 = $newPath;
                        $module->image_ar = null;
                        $updated = true;
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
            }

            // Handle 'image_v2' if stored locally
            if ($module->image_v2 && !filter_var($module->image_v2, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($module->image_v2)) {
                    $fileContent = Storage::disk('public')->get($module->image_v2);
                    if (Storage::disk('r2')->put($module->image_v2, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($module->image_v2);
                        }
                    }
                }
            }

            // Handle 'image_ar_v2' if stored locally
            if ($module->image_ar_v2 && !filter_var($module->image_ar_v2, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($module->image_ar_v2)) {
                    $fileContent = Storage::disk('public')->get($module->image_ar_v2);
                    if (Storage::disk('r2')->put($module->image_ar_v2, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($module->image_ar_v2);
                        }
                    }
                }
            }

            if ($updated) {
                $module->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Modules migration step finished.');
    }
}
