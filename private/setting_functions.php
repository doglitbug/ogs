<?php

class Settings
{
    protected array $settings = [];

    public function __construct()
    {
        //TODO Check database connection exists?
    }

    public function load(): void
    {
        $this->settings['page_size'] = 10;
        //TODO Load from database
    }

    public function save(array $settings): void
    {
        //TODO Save to database
    }

    public function get(string $name): mixed
    {
        if (isset($this->settings[$name])) {
            return $this->settings[$name];
        } else {
            error(500, 'Undefined setting: ' . $name);
            exit();
        }
    }
}