<?php

namespace themes\clipone;

use packages\base\Exception;
use packages\base\View;

class Tab
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $link;

    /**
     * @var string|View
     */
    protected $view;

    /**
     * @var View|null
     */
    protected $parent;

    public function __construct(string $name, $view)
    {
        $this->name = $name;
        $this->view = $view;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set title.
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for title of tab.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set link.
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * Getter for link of tab.
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Set View.
     *
     * @param string|View $view
     */
    public function setView($view): void
    {
        $this->view = $view;
    }

    /**
     * Getter for veiw.
     *
     * @throws Exception
     */
    public function getView(): View
    {
        if (is_string($this->view)) {
            $this->view = View::byName($this->view);
            if ($this->parent) {
                $this->view->setData($this->parent->__getData());
            }
        }
        if (!$this->view) {
            throw new Exception('cannot find view');
        }

        return $this->view;
    }

    /**
     * Set Parent View.
     */
    public function setParent(?View $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Getter for parent View.
     */
    public function getParent(): ?View
    {
        return $this->parent;
    }
}
