<?php

namespace packages\userpanel\Log;

use packages\base\DB;
use packages\userpanel\Log;

class Activity
{
    public static function getActivityTypes(?bool $useCache = null)
    {
        $types = array_column(DB::groupBy('type')->get('userpanel_logs', null, ['type']), 'type');
        $activities = [];
        foreach ($types as $type) {
            // For optimazing purpose isActivity() function should define as static.
            if (!method_exists($type, 'isActivity') or $type::isActivity()) {
                $activities[] = $type;
            }
        }

        return $activities;
    }
    protected $user;
    protected $from;

    public function __construct(int $user)
    {
        $this->user = $user;
    }

    public function from(int $time)
    {
        $this->from = $time;
    }

    public function dayByDay()
    {
        if ($this->from) {
            DB::where('time', $this->from, '>=');
        }
        DB::where('user', $this->user);
        $types = self::getActivityTypes();
        if (!$types) {
            return [];
        }
        if ($this->from) {
            DB::where('time', $this->from, '>=');
        }

        return DB::where('user', $this->user)
                  ->where('type', $types, 'IN')
                 ->groupBy('date')
                 ->orderBy('date', 'DESC')
                 ->get('userpanel_logs', null, ["FROM_UNIXTIME(`time`, '%Y/%m/%d') as `date`", 'count(*) as `activities`']);
    }

    public function get()
    {
        if ($this->from) {
            DB::where('time', $this->from, '>=');
        }

        return Log::where('user', $this->user)
                 ->where('type', self::getActivityTypes(), 'IN')
                 ->orderBy('date', 'DESC')
                 ->get('userpanel_logs');
    }
}
