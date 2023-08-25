<?php

namespace ZakariaElkashef\RepositoryPattern\Console\Commands;

use Illuminate\Console\Command;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {className?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
                
        $className = $this->argument('className');

        if (!$className) {
            $className = $this->ask('What should the repository be named?');
        }
    
        if (!$className) {
            $this->error('Repository name is required.');
            return;
        }
    

        $interfacePath = app_path('Http/Interfaces/' . $className . 'Interface.php');
        $repositoryPath = app_path('Http/Repository/' . $className . '.php');

        if (file_exists($interfacePath)) {
            $this->error("Interface already exists.");
            return;
        }

        if (file_exists($repositoryPath)) {
            $this->error("Repository already exists.");
            return;
        }

        $interfaceNamespace = str_replace('/', '\\', 'App\Http\Interfaces');
        $repositoryNamespace = str_replace('/', '\\', 'App\Http\Repository');

        $interfaceTemplate = "<?php\n\nnamespace $interfaceNamespace;\n\ninterface {$className}Interface\n{\n    // Interface methods...\n}\n";
        $repositoryTemplate = "<?php\n\nnamespace $repositoryNamespace;\n\nuse $interfaceNamespace\\{$className}Interface;\n\nclass {$className} implements {$className}Interface\n{\n    // Repository methods...\n}\n";


        $interfaceDirectory = dirname($interfacePath);
        $repositoryDirectory = dirname($repositoryPath);

        if (!is_dir($interfaceDirectory)) {
            mkdir($interfaceDirectory, 0755, true);
        }

        if (!is_dir($repositoryDirectory)) {
            mkdir($repositoryDirectory, 0755, true);
        }

        file_put_contents($interfacePath, $interfaceTemplate);
        file_put_contents($repositoryPath, $repositoryTemplate);


        
        $repositoryServiceProviderPath = app_path('Providers/RepositoryServiceProvider.php');

        $repositoryServiceProviderNamespace = str_replace('/', '\\', 'App\Providers');

        // Check if the RepositoryServiceProvider already exists
        if (file_exists($repositoryServiceProviderPath)) {
            $repositoryServiceProviderContents = file_get_contents($repositoryServiceProviderPath);

            // Check if the binding already exists in the RepositoryServiceProvider
            if (strpos($repositoryServiceProviderContents, "{$className}Interface::class") === false) {
                // Add the binding to the existing RepositoryServiceProvider
                $bindingCode = "        \$this->app->bind('App\Http\Interfaces\\{$className}Interface', 'App\Http\Repository\\{$className}');";

                $repositoryServiceProviderContents = str_replace(
                    "public function register()\n    {",
                    "public function register()\n    {\n        $bindingCode",
                    $repositoryServiceProviderContents
                );

                file_put_contents($repositoryServiceProviderPath, $repositoryServiceProviderContents);

                $this->info("Interface and Repository bound in the existing RepositoryServiceProvider.");
            } else {
                $this->error("Interface and Repository already bound in the RepositoryServiceProvider.");
            }
        } else {
            // Create the RepositoryServiceProvider
            $repositoryServiceProviderTemplate = "<?php\n\nnamespace $repositoryServiceProviderNamespace;\n\nuse Illuminate\Support\ServiceProvider;\n\nclass RepositoryServiceProvider extends ServiceProvider\n{\n    public function register()\n    {\n        \$this->app->bind('App\Http\Interfaces\\{$className}Interface', 'App\Http\Repository\\{$className}');\n    }\n}";

            file_put_contents($repositoryServiceProviderPath, $repositoryServiceProviderTemplate);

            $this->info("RepositoryServiceProvider created successfully.");

            

            // Add the RepositoryServiceProvider to the config/app.php file
            $appConfigPath = config_path('app.php');
            $appConfigContent = file_get_contents($appConfigPath);

            if (strpos($appConfigContent, 'App\Providers\RepositoryServiceProvider::class') === false) {
                $providerLine = "        App\Providers\RepositoryServiceProvider::class,";

                // Locate the position to add the RepositoryServiKceProvider
                $providersPosition = strpos($appConfigContent, 'ServiceProvider::defaultProviders()->merge([');
                if ($providersPosition !== false) {
                    $providersPosition += strlen('ServiceProvider::defaultProviders()->merge([');

                    $appConfigContent = substr_replace($appConfigContent, "\n$providerLine", $providersPosition, 0);
                }

                file_put_contents($appConfigPath, $appConfigContent);

                $this->info("RepositoryServiceProvider added to config/app.php successfully.");
            } else {
                $this->error("RepositoryServiceProvider is already present in config/app.php.");
            }

            // Bind the interface and repository in the RepositoryServiceProvider
            $repositoryServiceProviderPath = app_path('Providers/RepositoryServiceProvider.php');

            $repositoryServiceProviderNamespace = str_replace('/', '\\', 'App\Providers');

            // Check if the RepositoryServiceProvider already exists
            if (file_exists($repositoryServiceProviderPath)) {
                $repositoryServiceProviderContents = file_get_contents($repositoryServiceProviderPath);

                // Check if the binding already exists in the RepositoryServiceProvider
                if (strpos($repositoryServiceProviderContents, "{$className}Interface::class") === false) {
                    // Add the binding to the existing RepositoryServiceProvider
                    $bindingCode = "        \$this->app->bind('App\Http\Interfaces\\{$className}Interface', 'App\Http\Repository\\{$className}');";

                    $repositoryServiceProviderContents = str_replace(
                        "public function register()\n    {",
                        "public function register()\n    {\n        $bindingCode",
                        $repositoryServiceProviderContents
                    );

                    file_put_contents($repositoryServiceProviderPath, $repositoryServiceProviderContents);

                    $this->info("Interface and Repository Successfully Bind in the RepositoryServiceProvider.");
                } else {
                    $this->error("Interface and Repository already bound in the RepositoryServiceProvider.");
                }
            } else {
                $this->error("RepositoryServiceProvider does not exist. Please run the command to create it.");
            }
            
        }

        $this->info("Interface and Repository created successfully.");
    }
}

