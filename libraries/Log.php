<?php

namespace packages\userpanel;

use packages\base\DB\DBObject;
use packages\base\HTTP;
use packages\userpanel\logging\Exception\InvalidTypeException;

/**
 * @property int                             $id
 * @property User|int|null                   $user
 * @property string                          $ip
 * @property int                             $time
 * @property string                          $title
 * @property class-string                    $type
 * @property array<string,mixed>|mixed       $parameters
 * @property \packages\userpanel\log_param[] $params
 */
class Log extends DBObject
{
    use Paramable;
    use CursorPaginateTrait;

    protected $dbTable = 'userpanel_logs';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'user' => ['type' => 'int'],
        'ip' => ['type' => 'text', 'required' => true],
        'time' => ['type' => 'int', 'required' => true],
        'title' => ['type' => 'text', 'required' => true],
        'type' => ['type' => 'text', 'required' => true],
        'parameters' => ['type' => 'text'],
    ];
    protected $serializeFields = ['parameters'];
    protected $relations = [
        'params' => ['hasMany', log_param::class, 'log'],
        'user' => ['hasOne', User::class, 'user'],
    ];
    private $handler;

    public function preLoad(array $data): array
    {
        if (!isset($data['time']) or !$data['time']) {
            $data['time'] = time();
        }
        if (!isset($data['ip']) or !$data['ip']) {
            if (isset(HTTP::$client['ip'])) {
                $data['ip'] = HTTP::$client['ip'];
            } elseif (isset(HTTP::$server['ip'])) {
                $data['ip'] = HTTP::$server['ip'];
            } else {
                $data['ip'] = '0.0.0.0';
            }
        }

        return $data;
    }

    public function getHandler()
    {
        if (!$this->handler) {
            if (!class_exists($this->type)) {
                throw new InvalidTypeException($this->type);
            }
            $this->handler = new $this->type();
            $this->handler->setLog($this);
        }

        return $this->handler;
    }
}
