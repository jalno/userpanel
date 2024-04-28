<?php

namespace packages\userpanel\Events\General\Settings;

class Setting
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string fontawesome icon
     */
    private ?string $icon = null;

    /**
     * @var array<string,array<string,mixed>>
     */
    private $inputs = [];

    /**
     * @var mixed[]
     */
    private $fields = [];

    /**
     * @var string|null
     */
    private $controller;

    /**
     * @var array<string,mixed>
     */
    private $data = [];

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function addInput(array $input): void
    {
        if (!isset($input['name'])) {
            throw new InputNameRequiredException($input);
        }
        $this->inputs[$input['name']] = $input;
    }

    public function getInputs(): array
    {
        return $this->inputs;
    }

    public function addField(array $field): void
    {
        $this->fields[] = $field;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setController(string $controller): void
    {
        if (!class_exists($controller) or !((new $controller()) instanceof Controller)) {
            throw new ControllerException($controller);
        }
        $this->controller = $controller;
    }

    public function store(array $inputs)
    {
        return $this->callController($inputs, 'store');
    }

    public function callController(array $inputs, string $method)
    {
        if (!$this->controller) {
            return null;
        }
        if (!method_exists($this->controller, $method)) {
            throw new ControllerException($this->controller.'@'.$method);
        }

        return (new $this->controller())->$method($inputs);
    }

    public function setDataForm(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function getDataForm(string $name = '')
    {
        if ($name) {
            return $this->data[$name] ?? null;
        }

        return $this->data;
    }
}
