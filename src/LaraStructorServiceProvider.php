<?php

namespace LaraStructor;

use Illuminate\Support\ServiceProvider;
use LaraStructor\Commands\MakeModelCommand;
use LaraStructor\Commands\MakeServiceCommand;
use LaraStructor\Commands\MakeRepositoryCommand;
use LaraStructor\Commands\MakeRepositoryServiceCommand;

class LaraStructorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeModelCommand::class,
            MakeServiceCommand::class,
            MakeRepositoryCommand::class,
            MakeRepositoryServiceCommand::class
        ]);
    }
}
