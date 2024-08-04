<?php

namespace packages\userpanel\Events\General\Settings;

class Log
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     * @var int
     */
    private $oldValue;
    /**
     * @var string
     * @var int
     */
    private $newValue;

    /**
     * @var name     string
     * @var oldValue string | int
     * @var newValue string | int
     * @var title    string and Optional
     */
    public function __construct(string $name, $oldValue, $newValue, string $title = '')
    {
        $this->setName($name);
        $this->setOldValue($oldValue);
        $this->setNewValue($newValue);
        if ('' !== $title) {
            $this->setTitle($title);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     * @return null
     */
    public function getTitle()
    {
        if ($this->title) {
            return $this->title;
        }
        $title = t("packages.userpanel.settings.{$this->title}");

        return $title ? $title : $this->name;
    }

    /**
     * @return string
     * @return int
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * @return string
     * @return int
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * @var string
     */
    private function setName(string $name)
    {
        $this->name = $name;
    }

    private function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @var string
     * @var int
     */
    private function setOldValue($oldValue)
    {
        if (!is_string($oldValue) and !is_numeric($oldValue)) {
            throw new \TypeError('the old value can string or numeric');
        }
        $this->oldValue = $oldValue;
    }

    /**
     * @var string
     * @var int
     */
    private function setNewValue($newValue)
    {
        if (!is_string($newValue) and !is_numeric($newValue)) {
            throw new \TypeError('the new value can string or numeric');
        }
        $this->newValue = $newValue;
    }
}
