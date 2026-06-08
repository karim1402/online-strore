<?php

namespace App\Console\Commands;

use App\Models\HomeAd;
use App\Models\ModuleAd;
use App\Models\Module;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
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
    protected $description = 'Migrate existing Home Ads, Module Ads, Modules, Categories, and Products images from local public storage to Cloudflare R2';

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

        // 4. Migrate Categories
        $this->migrateCategories($deleteLocal);

        // 5. Migrate Products
        $this->migrateProducts($deleteLocal);

        $this->info('All migrations completed successfully!');
    }

    private function migrateHomeAds(bool $deleteLocal)
    {
        $homeAds = HomeAd::all();
        $this->info("Found {$homeAds->count()} Home Ads to check.");

        $bar = $this->output->createProgressBar($homeAds->count());
        $bar->start();

        foreach ($homeAds as $ad) {
            foreach (['image', 'image_ar', 'image_v2', 'image_ar_v2'] as $field) {
                $path = $ad->$field;
                if ($path && !filter_var($path, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($path)) {
                    $fileContent = Storage::disk('public')->get($path);
                    if (Storage::disk('r2')->put($path, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($path);
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
            foreach (['image', 'image_ar', 'image_v2', 'image_ar_v2'] as $field) {
                $path = $module->$field;
                if ($path && !filter_var($path, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($path)) {
                    $fileContent = Storage::disk('public')->get($path);
                    if (Storage::disk('r2')->put($path, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Modules migration step finished.');
    }

    private function migrateCategories(bool $deleteLocal)
    {
        $categories = Category::all();
        $this->info("Found {$categories->count()} Categories to check.");

        $bar = $this->output->createProgressBar($categories->count());
        $bar->start();

        foreach ($categories as $category) {
            foreach (['image', 'image_v2'] as $field) {
                $path = $category->$field;
                if ($path && !filter_var($path, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($path)) {
                    $fileContent = Storage::disk('public')->get($path);
                    if (Storage::disk('r2')->put($path, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Categories migration step finished.');
    }

    private function migrateProducts(bool $deleteLocal)
    {
        // 1. Migrate best seller images
        $products = Product::whereNotNull('best_seller_image')->get();
        $this->info("Found {$products->count()} Products with best seller images to check.");

        $bar1 = $this->output->createProgressBar($products->count());
        $bar1->start();

        foreach ($products as $product) {
            if ($product->best_seller_image && !filter_var($product->best_seller_image, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($product->best_seller_image)) {
                    $fileContent = Storage::disk('public')->get($product->best_seller_image);
                    if (Storage::disk('r2')->put($product->best_seller_image, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($product->best_seller_image);
                        }
                    }
                }
            }
            $bar1->advance();
        }

        $bar1->finish();
        $this->newLine(2);

        // 2. Migrate standard product images
        $images = ProductImage::all();
        $this->info("Found {$images->count()} Product Images to check.");

        $bar2 = $this->output->createProgressBar($images->count());
        $bar2->start();

        foreach ($images as $image) {
            if ($image->image_path && !filter_var($image->image_path, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    $fileContent = Storage::disk('public')->get($image->image_path);
                    if (Storage::disk('r2')->put($image->image_path, $fileContent, 'public')) {
                        if ($deleteLocal) {
                            Storage::disk('public')->delete($image->image_path);
                        }
                    }
                }
            }
            $bar2->advance();
        }

        $bar2->finish();
        $this->newLine(2);
        $this->info('Products migration step finished.');
    }
}
