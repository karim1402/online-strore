<?php

namespace App\Console\Commands;

use App\Models\HomeAd;
use App\Models\Module;
use App\Models\Category;
use Illuminate\Console\Command;

class SyncV2ImagePaths extends Command
{
    protected $signature = 'ads:sync-v2-paths';

    protected $description = 'Populate image_v2 / image_ar_v2 from existing image / image_ar paths for records that are missing them';

    public function handle()
    {
        $this->info('Syncing v2 image paths in the database...');

        $this->syncHomeAds();
        $this->syncModules();
        $this->syncCategories();

        $this->info('Done.');
    }

    private function syncHomeAds()
    {
        $ads = HomeAd::where(function ($q) {
            $q->whereNotNull('image')->whereNull('image_v2');
        })->orWhere(function ($q) {
            $q->whereNotNull('image_ar')->whereNull('image_ar_v2');
        })->get();

        $this->info("Found {$ads->count()} Home Ads to sync.");
        $bar = $this->output->createProgressBar($ads->count());
        $bar->start();

        foreach ($ads as $ad) {
            if ($ad->image && !$ad->image_v2) {
                $ad->image_v2 = $ad->image;
            }
            if ($ad->image_ar && !$ad->image_ar_v2) {
                $ad->image_ar_v2 = $ad->image_ar;
            }
            $ad->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Home Ads sync finished.');
    }

    private function syncModules()
    {
        $modules = Module::where(function ($q) {
            $q->whereNotNull('image')->whereNull('image_v2');
        })->orWhere(function ($q) {
            $q->whereNotNull('image_ar')->whereNull('image_ar_v2');
        })->get();

        $this->info("Found {$modules->count()} Modules to sync.");
        $bar = $this->output->createProgressBar($modules->count());
        $bar->start();

        foreach ($modules as $module) {
            if ($module->image && !$module->image_v2) {
                $module->image_v2 = $module->image;
            }
            if ($module->image_ar && !$module->image_ar_v2) {
                $module->image_ar_v2 = $module->image_ar;
            }
            $module->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Modules sync finished.');
    }

    private function syncCategories()
    {
        $categories = Category::where(function ($q) {
            $q->whereNotNull('image')->whereNull('image_v2');
        })->orWhere(function ($q) {
            $q->whereNotNull('image_ar')->whereNull('image_ar_v2');
        })->get();

        $this->info("Found {$categories->count()} Categories to sync.");
        $bar = $this->output->createProgressBar($categories->count());
        $bar->start();

        foreach ($categories as $category) {
            if ($category->image && !$category->image_v2) {
                $category->image_v2 = $category->image;
            }
            if ($category->image_ar && !$category->image_ar_v2) {
                $category->image_ar_v2 = $category->image_ar;
            }
            $category->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Categories sync finished.');
    }
}
