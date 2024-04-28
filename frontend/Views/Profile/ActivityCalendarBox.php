<?php

namespace themes\clipone\Views\Profile;

use packages\userpanel;
use packages\userpanel\Authorization;
use packages\userpanel\Date;
use packages\userpanel\Log;
use packages\userpanel\User;
use themes\clipone\Views\Dashboard\Box;

class ActivityCalendarBox extends Box
{
    /** @var User */
    protected $user;

    /** @var int */
    protected $totalActivities = 0;

    public function __construct(User $user, $name = 'ActivityCalendar')
    {
        parent::__construct($name);
        $this->user = $user;
    }

    /**
     * Get the value of user.
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set the value of user.
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getHTML()
    {
        $calender = $this->buildCalendar();
        $logs = $this->buildLogs();
        $canViewLogs = Authorization::is_accessed('logs_search');
        $this->html = '<div class="panel panel-white panel-activity" data-user="'.$this->user->id.'">
		<div class="panel-heading">
			<i class="clip-calendar-3"></i> '.t('userpanel.profile.activity_calendar.title', ['activities' => $this->totalActivities]).'
			<div class="panel-tools">'.
                ($canViewLogs ? '<a class="btn btn-xs btn-link tooltips" title="'.t('packages.userpanel.logs.all').'" href="'.userpanel\url('logs', ['user' => $this->user->id]).'"><i class="fa fa-list-ul"></i></a>' : '').
                '<div class="calendar-guide">
					<div class="calendar-square tooltips color0" title="'.t('userpanel.profile.activity_calendar.no_activity').'"></div>
					<div class="calendar-square tooltips color1" title="'.t('userpanel.profile.activity_calendar.up_to_activities', ['activities' => 20]).'"></div>
					<div class="calendar-square tooltips color2" title="'.t('userpanel.profile.activity_calendar.up_to_activities', ['activities' => 40]).'"></div>
					<div class="calendar-square tooltips color3" title="'.t('userpanel.profile.activity_calendar.up_to_activities', ['activities' => 60]).'"></div>
					<div class="calendar-square tooltips color4" title="'.t('userpanel.profile.activity_calendar.up_to_activities', ['activities' => 80]).'"></div>
				</div>
			</div>
		</div>
		<div class="panel-body">'.$calender.$logs.'</div>
	</div>';

        return $this->html;
    }

    protected function buildCalendar(): string
    {
        $now = Date::mktime(23, 59, 59) + 1;
        $firstDayOfWeek = Date::getFirstDayOfWeek();
        $lastyear = Date::mktime(null, null, null, Date::format('n', $now) + 1, Date::format('j', $now), Date::format('Y', $now) - 1);
        $months = '';
        $dates = '';
        $activity = new Log\Activity($this->user->id);
        $activity->from($lastyear);
        $activities = [];
        foreach ($activity->dayByDay() as $item) {
            $activities[Date::format('Y/n/j', strtotime($item['date']))] = $item['activities'];
        }
        for ($first = true, $i = 0, $w = 0; $lastyear < $now; $first = false, $lastyear += 86400) {
            $date = intval(Date::format('j', $lastyear));
            $month = intval(Date::format('n', $lastyear));
            $year = intval(Date::format('Y', $lastyear));
            $day = intval(Date::format('w', $lastyear));
            if ($day == $firstDayOfWeek and !$first) {
                $dates .= '</div>';
            }
            if ($day == $firstDayOfWeek or $first) {
                $dates .= '<div class="column">';
            }
            if ($first) {
                for ($x = 0; $x < Date::getWeekDay($day); ++$x) {
                    $dates .= '<div class="calendar-square calendar-square-empty"></div>';
                }
            }
            if (($dates or 0 == $day or 1 == $date) and $i != $month) {
                if (0 == $day) {
                    $i = $month;
                    $months .= '<div class="month month-margin-'.$w.'">'.Date::format('jdate' == Date::getCanlenderName() ? 'F' : 'M', $lastyear).'</div>';
                    $w = 0;
                } else {
                    ++$w;
                }
            }
            $count = $activities[$year.'/'.$month.'/'.$date] ?? 0;
            $color = min(ceil($count / 20), 4);
            $tooltip = $count ? t('userpanel.logs.activities.ondate', [
                'count' => $count,
                'date' => Date::format('QQ', $lastyear),
            ]) : t('userpanel.logs.activities.ondate.empty', [
                'date' => Date::format('QQ', $lastyear),
            ]);
            $dates .= '<div class="calendar-square tooltips color'.$color.'" title="'.$tooltip.'"';
            if ($count) {
                $dates .= ' data-from="'.$year.'/'.$month.'/'.$date.'"';
                $dates .= ' data-until="'.Date::format('Y/n/j', $lastyear + 86400).'"';
            }
            $dates .= '></div>';
        }
        $dates .= '</div>';
        $this->totalActivities = array_sum($activities);
        --$now;
        $day = intval(Date::format('w', $now));

        return '<div class="calender">
			<div class="days">
				<div class="day">'.Date::format('l', $now + (($firstDayOfWeek - $day + 1) * 86400)).'</div>
				<div class="day">'.Date::format('l', $now + (($firstDayOfWeek - $day + 3) * 86400)).'</div>
				<div class="day">'.Date::format('l', $now + (($firstDayOfWeek - $day + 5) * 86400)).'</div>
		 	</div>
			<div class="months">'.$months.'</div>
			<div class="dates">'.$dates.'</div>
	  </div>';
    }

    protected function buildLogs(): string
    {
        $logsData = $this->getLogs();

        [$logs, $cursor] = [$logsData['logs'], $logsData['cursor']];

        $html = '<div class="panel-scroll">';
        $html .= '<ul class="activities" data-cursor_name="'.$cursor->getCursorName().'" data-next_page_cursor="'.$cursor->getNextPageCursor().'">';
        $canView = Authorization::is_accessed('logs_view');
        foreach ($logs as $log) {
            $lHandler = $log->getHandler();
            $html .= '<li>';
            $html .= '<a class="activity" href="'.($canView ? userpanel\url("logs/view/{$log->id}") : '#').'">';
            $html .= '<i class="circle-icon '.$lHandler->getColor().' '.$lHandler->getIcon().'"></i>';
            $html .= ' <span class="desc">'.$log->title.'</span>';
            $html .= '<div class="time tooltips" title="'.Date::relativeTime($log->time).'">';
            $html .= '<i class="fa fa-time bigger-110"></i>'.Date::format('QTS Q', $log->time);
            $html .= '</div>';
            $html .= '</a>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @return array{"logs":Log[],"cursor":Log}
     */
    protected function getLogs($limit = 50): array
    {
        $log = new Log();
        $log->where('user', $this->user->id);
        $logs = $log->cursorPaginate('DESC', $limit);

        return [
            'logs' => $logs,
            'cursor' => $log,
        ];
    }
}
