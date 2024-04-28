<?php

namespace themes\clipone\Navigation;

class Badge
{
    private const COLORS = ['success', 'warning', 'info', 'danger', 'inverse', 'primary'];

    public static function success(string $title): self
    {
        return new self('success', $title);
    }

    public static function warning(string $title): self
    {
        return new self('warning', $title);
    }

    public static function info(string $title): self
    {
        return new self('info', $title);
    }

    public static function danger(string $title): self
    {
        return new self('danger', $title);
    }

    public static function inverse(string $title): self
    {
        return new self('inverse', $title);
    }

    public static function primary(string $title): self
    {
        return new self('primary', $title);
    }

    /**
     * @var string
     */
    protected $color;

    /**
     * @var string
     */
    protected $title;

    /**
     * @param string $title optional
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $color, string $title)
    {
        $this->setColor($color);
        $this->setTitle($title);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setColor(string $color): void
    {
        if (!in_array($color, self::COLORS)) {
            throw new \InvalidArgumentException($color.' is invalid. valid colors is: ['.implode(', ', self::COLORS).']');
        }

        $this->color = $color;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function build(): string
    {
        $class = 'primary' == $this->color ? 'new' : $this->color;

        return '<span class="badge badge-'.$class.'">'.(string) $this->title.'</span>';
    }
}
