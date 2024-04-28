<?php

namespace themes\clipone\Views;

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
     *
     * @return void
     */
    public function output()
    {
        if ($this->activeTab) {
            $this->outputTab();
        } else {
            parent::output();
        }
    }

    /**
     * Ouput the html file.
     *
     * @return void
     */
    public function outputTab()
    {
        $this->loadHTMLFile();
        if (!$this->file) {
            return;
        }
        require_once $this->file->getPath();
        (new View\Events\AfterOutput($this))->trigger();
    }

    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }
    }
}
