<?php

namespace packages\userpanel;

use packages\base\DB;

trait Paramable
{
    protected $tempParams = [];

    public function setParam(string $name, $value)
    {
        if ($this->isNew or !$this->id) {
            $this->tempParams[$name] = $value;
        } else {
            if ($this->hasParam($name)) {
                $this->updateParam($name, $value);
            } else {
                $this->insertParam($name, $value);
            }
        }
    }

    public function hasParam(string $name)
    {
        if (isset($this->tempParams[$name])) {
            return true;
        }
        if (!$this->isNew and $this->id) {
            return DB::where($this->getObjectName(), $this->id)->where('name', $name)->has($this->getParamsTable());
        }

        return false;
    }

    public function param(string $name)
    {
        if (isset($this->tempParams[$name])) {
            return $this->tempParams[$name];
        }
        if (!$this->isNew and $this->id) {
            $param = DB::where($this->getObjectName(), $this->id)->where('name', $name)->getOne($this->getParamsTable());
            if ($param) {
                return $this->unserializeValue($param['value']);
            }
        }

        return null;
    }

    public function getParams(): array
    {
        $result = [];
        $params = DB::where($this->getObjectName(), $this->id)->get($this->getParamsTable(), null, ['name', 'value']);
        foreach ($params as $param) {
            $result[$param['name']] = $this->unserializeValue($param['value']);
        }

        return $result;
    }

    public function save($data = null)
    {
        $result = parent::save($data);
        if ($result) {
            $this->saveParams();
        }

        return $result;
    }

    protected function saveParams()
    {
        if (!$this->isNew and $this->id and $this->tempParams) {
            foreach ($this->tempParams as $name => $value) {
                $this->insertParam($name, $value);
            }
        }
    }

    protected function insertParam(string $name, $value)
    {
        return DB::insert($this->getParamsTable(), [
            $this->getObjectName() => $this->id,
            'name' => $name,
            'value' => $this->serializeValue($value),
        ]);
    }

    protected function updateParam(string $name, $value)
    {
        return DB::where($this->getObjectName(), $this->id)->where('name', $name)->update($this->getParamsTable(), [
            'value' => $this->serializeValue($value),
        ]);
    }

    protected function getParamsTable(): string
    {
        return $this->dbTable.'_params';
    }

    protected function getObjectName(): string
    {
        $objName = get_class($this);
        $lastBackSlash = strrpos($objName, '\\');
        if (false !== $lastBackSlash) {
            $objName = substr($objName, $lastBackSlash + 1);
        }

        return $objName;
    }

    protected function serializeValue($value): string
    {
        if (is_array($value) or is_object($value)) {
            $value = serialize($value);
        }

        return $value;
    }

    protected function unserializeValue(string $value)
    {
        if (preg_match('/^(?:(?:a|i|s|C|O|b|d)\:\d+|N;)/', $value)) {
            $value = unserialize($value);
        }

        return $value;
    }
}
