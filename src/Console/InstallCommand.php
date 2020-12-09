<?php

namespace Painless\BreezeMultiAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Painless\BreezeMultiAuth\Editors\AuthConfigEditor;
use Painless\BreezeMultiAuth\Editors\AuthenticateMiddlewareEditor;
use Painless\BreezeMultiAuth\Editors\RedirectIfAuthMiddlewareEditor;

class InstallCommand extends Command
{
    protected $name;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'breeze:multiauth
                            { name : Name of user role }
                            {--asset : Install Breeze with assets }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Breeze controllers and resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->name = Str::snake($this->argument('name'));
        if($this->option('asset')){
            // NPM Packages...
            $this->updateNodePackages(function ($packages) {
                return [
                        '@tailwindcss/forms' => '^0.2.1',
                        'alpinejs' => '^2.7.3',
                        'postcss-import' => '^12.0.1',
                        'tailwindcss' => 'npm:@tailwindcss/postcss7-compat@^2.0.1',
                        'autoprefixer' => '^9.8.6',
                    ] + $packages;
            });
        }

        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/'.Str::studly($this->name).'/Auth'));
        $this->copyDirectory(__DIR__.'/../../stubs/App/Http/Controllers/Auth', app_path('Http/Controllers/'.Str::studly($this->name).'/Auth'));
        $this->putCompiledFile(__DIR__.'/../../stubs/App/Http/Controllers/DashboardController.php', app_path('Http/Controllers/'.Str::studly($this->name).'/DashboardController.php'));


        // Requests...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests/'.Str::studly($this->name).'/Auth'));
        $this->copyDirectory(__DIR__.'/../../stubs/App/Http/Requests/Auth', app_path('Http/Requests/'.Str::studly($this->name).'/Auth'));

        // Views...
        (new Filesystem)->ensureDirectoryExists(resource_path('views/'.$this->name.'/auth'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/'.$this->name.'/layouts'));

        $this->copyDirectory(__DIR__.'/../../stubs/resources/views/auth', resource_path('views/'.$this->name.'/auth'));
        $this->copyDirectory(__DIR__.'/../../stubs/resources/views/layouts', resource_path('views/'.$this->name.'/layouts'));

        if(!(new Filesystem)->exists(resource_path('views/components/button.blade.php'))){
            (new Filesystem)->ensureDirectoryExists(resource_path('views/components'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/resources/views/components', resource_path('views/components'));
        }

        $this->putCompiledFile(__DIR__.'/../../stubs/resources/views/dashboard.blade.php', resource_path('views/'.$this->name.'/dashboard.blade.php'));

        // Tests...
        $this->copyDirectory(__DIR__.'/../../stubs/tests/Feature', base_path('tests/Feature'), Str::studly($this->name));

        // Routes...
        if(!str_contains(
            (new Filesystem)->get(base_path('routes/web.php')),
            trim((new Filesystem)->get(__DIR__.'/../../stubs/routes/web.php'))
        )){
            (new Filesystem())->append(
                base_path('routes/web.php'),
                $this->compile(
                    (new Filesystem)->get(__DIR__.'/../../stubs/routes/web.php')
                )
            );
        }

        //Database
        $this->putCompiledFile(__DIR__.'/../../stubs/App/Models/User.php', app_path('Models'.DIRECTORY_SEPARATOR.Str::Studly($this->name).'.php'));
        $this->putCompiledFile(__DIR__.'/../../stubs/database/factories/UserFactory.php', database_path('factories'.DIRECTORY_SEPARATOR.Str::Studly($this->name).'Factory.php'));
        $this->putCompiledFile(__DIR__.'/../../stubs/database/migrations/2014_10_12_000000_create_users_table.php', database_path('migrations'.DIRECTORY_SEPARATOR.date('Y_m_d').'_000000_create_'.Str::plural($this->name).'_table.php'));


        if((new AuthConfigEditor($this->name))->edit()) {
            (new AuthenticateMiddlewareEditor($this->name))->edit();
            (new RedirectIfAuthMiddlewareEditor($this->name))->edit();
        }

        if($this->option('asset')){
            // Tailwind / Webpack...
            copy(__DIR__.'/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
            copy(__DIR__.'/../../stubs/webpack.mix.js', base_path('webpack.mix.js'));
            copy(__DIR__.'/../../stubs/resources/css/app.css', resource_path('css/app.css'));
            copy(__DIR__.'/../../stubs/resources/js/app.js', resource_path('js/app.js'));
        }

        $this->info('Breeze scaffolding installed successfully.');
        if($this->option('asset')){
            $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
        }
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Delete the "node_modules" directory and remove the associated lock files.
     *
     * @return void
     */
    protected static function flushNodeModules()
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    protected function copyDirectory($source, $destination, $prefix = '')
    {
        $filesystem = new Filesystem;
        foreach ($filesystem->allFiles($source) as $file) {
            $this->putCompiledFile($file->getPathname(), $destination.DIRECTORY_SEPARATOR.$prefix.$file->getFilename());
        }
    }

    protected function putCompiledFile($source, $destination)
    {
        $filesystem = new Filesystem;

        $filesystem->put($destination, $this->compile($filesystem->get($source)));
    }

    protected function compile($input)
    {
        $replacements = [
            '{{name}}' => $this->name,
            '{{names}}' => Str::plural($this->name),
            '{{Name}}' => Str::studly($this->name),
            '{{Names}}' => Str::pluralStudly($this->name)
        ];
        return str_replace(array_keys($replacements), array_values($replacements), $input);
    }
}
