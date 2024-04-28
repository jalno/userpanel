<?php

namespace themes\clipone\Users;

class ActionButton extends AdditionalInformation
{
    public const DEFAULT = 'default';
    public const PRIMARY = 'primary';
    public const SUCCESS = 'success';
    public const INFO = 'info';
    public const WARNING = 'warning';
    public const DANGER = 'danger';
    public const LINK = 'link';
    public const INVERSE = 'inverse';

    private ?string $type = null;
    private ?string $link = null;
    private ?string $icon = null;
    private ?SubmitModal $submitModal = null;

    public function __construct(string $name, ?string $type = null, ?string $link = null)
    {
        parent::__construct($name);

        if (!$type) {
            $type = $link ? self::LINK : self::DEFAULT;
        }

        $this->setType($type);
        $this->setLink($link);

        $this->addClass('btn');
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setType(string $type): void
    {
        $allTypes = [self::DEFAULT, self::PRIMARY, self::SUCCESS, self::INFO, self::WARNING, self::DANGER, self::LINK, self::INVERSE];
        if (!in_array($type, $allTypes)) {
            throw new \InvalidArgumentException('Button type must be one of'.implode(', ', $allTypes));
        }

        if ($this->type) {
            $this->removeClass('btn-'.$this->type);
        }

        $this->addClass('btn-'.$type);

        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setSubmitModalData(string $link, string $title, string $content, ?string $id = null): void
    {
        $this->addData('toggle', 'modal');
        $this->submitModal = new SubmitModal($this->getView(), $link, SubmitModal::POST, $id);
        $this->submitModal->setTitle($title);
        $this->submitModal->setContent($content);
    }

    public function getSubmitModal(): ?SubmitModal
    {
        return $this->submitModal;
    }

    public function removeSubmitModal(): void
    {
        $this->submitModal = null;
    }

    public function getHtml(): string
    {
        $el = ($this->submitModal ? 'a' : 'button');

        return "\n".'<'.$el.
                ' class="'.$this->getClasses().'"'.
                ($this->getLinkHtml() ? ' '.$this->getLinkHtml() : '').
                (!empty($this->data) ? ' '.$this->generateItemData() : '').
                (!$this->submitModal ? ' type="button"' : '').
            '>'.
            $this->getIconHtml().
            $this->getName().
            '</'.$el.'>'."\n".
            ($this->submitModal ? $this->submitModal->getHTML() : '');
    }

    protected function getIconHtml(): string
    {
        if (!$this->icon) {
            return '';
        }

        return '<div class="btn-icons"><i class="'.$this->icon.'"></i></div>';
    }

    protected function getLinkHtml(): string
    {
        return 'href="'.($this->submitModal ? '#'.$this->submitModal->getID() : $this->link).'"';
    }
}
