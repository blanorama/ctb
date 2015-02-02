<?php

class Application extends Illuminate\Console\Application
{
    public function boot()
    {
        $path = $this->laravel['path'].'/start/artisan.php';

        if (file_exists($path))
        {
            require $path;
        }

        if (isset($this->laravel['events']) && App::environment() == 'local')
        {
            $this->laravel['events']
                ->fire('artisan.start', array($this));
        }

        return $this;
    }
}