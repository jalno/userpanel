<?php

namespace themes\clipone\Views;

use Exception;
use packages\base\View;

trait TabTrait
{
    protected $activeTab = false;

    /**
     * Getter and setter for activeTab property.
     *
     * @return bool current value of activeTab
     */
    public function isActiveTab(?bool $activeTab = null): bool
    {
        if (null !== $activeTab) {
            $this->activeTab = $activeTab;
        }

        return $this->activeTab;
    }

    /**
     * Ouput the html file.
     */
    public function output(): string
    {
        return $this->activeTab ? $this->outputTab() : parent::output();
    }

    /**
     * Ouput the html file.
     *
     * @return void
     */
    public function outputTab(): string
    {
        $this->loadHTMLFile();
        if (!$this->file) {
            throw new Exception("Cannot find html file");
        }
        $obLevel = ob_get_level();
        ob_start();
        try {
            require $this->file->getPath();
        } catch (\Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        }
        $result = ltrim(ob_get_clean());

        (new View\Events\AfterOutput($this))->trigger();

        return $result;
    }

    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }
    }
}
