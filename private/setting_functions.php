<?php

class Settings
{
    protected array $settings = [];

    public function load(): void
    {
        global $db;
        $db_settings = $db->get_settings();
        //TODO Figure out if there is a cleaner way to do this, like a map or reduce
        foreach($db_settings as $db_setting){
            $this->settings[$db_setting['name']] = $db_setting['value'];
        }
    }

    public function save(): void
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