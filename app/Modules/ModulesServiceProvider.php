<?php
    namespace App\Modules;

    class ModulesServiceProvider extends \Illuminate\Support\ServiceProvider
    {
        public function boot()
        {
            //получаем список модулей, которые надо подгрузить
            $modules = config("module.modules");
            if($modules)
            {
                foreach ($modules as $module)
                {
                    if (file_exists(__DIR__.'/'.$module.'/Routes/routes.php'))
                    {
                        $this->loadRoutesFrom(__DIR__.'/'.$module.'/Routes/routes.php');
                    }
                    if (is_dir(__DIR__.'/'.$module.'/Database/Migrations'))
                    {
                        $this->loadMigrationsFrom(__DIR__.'/'.$module.'/Database/Migrations');
                    }
                    if (is_dir(__DIR__.'/'.$module.'/Lang'))
                    {
                        $this->loadTranslationsFrom(__DIR__.'/'.$module.'/Lang', $module);
                    }
                    if(is_dir(__DIR__.'/'.$module.'/Views'))
                    {
                        $this->loadViewsFrom(__DIR__.'/'.$module.'/Views', $module);
                    }
                    if(is_file(__DIR__.'/'.$module.'/Vendor'.'/autoload.php'))
                    {
                        require_once __DIR__.'/'.$module.'/Vendor'.'/autoload.php';
                    }
                    if(is_file(__DIR__.'/'.$module.'/Vendor'.'/init.php'))
                    {
                        require_once __DIR__.'/'.$module.'/Vendor'.'/init.php';
                    }
                }
            }
        }

        public function register()
        {

        }
    }
    ?>