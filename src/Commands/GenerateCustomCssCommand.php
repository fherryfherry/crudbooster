<?php

namespace CrudBooster\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use function Laravel\Prompts\confirm;

class GenerateCustomCssCommand extends Command
{
    protected $signature = 'cb:custom-css
                            {--force : Overwrite if cb-custom.css already exists}
                            {--path=resources/css : Target directory for the CSS template}';

    protected $description = 'Generate cb-custom.css template (Tailwind utilities-only) to extend CRUDBooster base styles';

    public function handle()
    {
        $fs = new Filesystem();
        $targetDir = base_path($this->option('path') ?? 'resources/css');
        $filePath = $targetDir . '/cb-custom.css';

        $fs->ensureDirectoryExists($targetDir);

        if ($fs->exists($filePath) && !$this->option('force')) {
            $proceed = confirm('cb-custom.css already exists. Overwrite?', default: false);
            if (!$proceed) {
                $this->info('Skipped. Existing file kept: ' . $filePath);
                return self::SUCCESS;
            }
        }

        $content = <<<CSS
/* CRUDBooster Custom Tailwind CSS */
/* Recommended: only utilities to avoid duplicate preflight/components */
@tailwind utilities;

/* Put your custom components/utilities here */
@layer components {
  .btn-primary { @apply bg-sky-600 text-white px-3 py-2 rounded; }
}

/* Tips:
 - Hindari @tailwind base agar Preflight tidak duplikat dengan app.min.css
 - Kelas dinamis? pakai safelist di tailwind.config.js
 - Build: npx tailwindcss -c tailwind.config.js -i ./resources/css/cb-custom.css -o ./public/css/cb-custom.css --minify
 - Register: CbThemeAssetRegistrar::addCss('css/cb-custom.css') di AppServiceProvider
*/
CSS;

        $fs->put($filePath, $content);

        $this->info('Generated: ' . $filePath);
        $this->line('Next steps:');
        $this->line(' - Build: npx tailwindcss -c tailwind.config.js -i ./resources/css/cb-custom.css -o ./public/css/cb-custom.css --minify');
        $this->line(" - Register CSS: \\CrudBooster\\Themes\\CbThemeAssetRegistrar::addCss('css/cb-custom.css') in App\\Providers\\AppServiceProvider");
        return self::SUCCESS;
    }
}