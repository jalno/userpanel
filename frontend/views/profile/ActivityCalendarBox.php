<?php
namespace themes\clipone\views\Profile;

use packages\base\{db, Date as BaseDate};
use packages\userpanel;
use packages\userpanel\{User, Date, Log, Authorization};
use themes\clipone\views\Dashboard\Box;

class ActivityCalendarBox extends Box {
	/** @var User */
	protected $user;

	/** @var int */
	protected $totalActivities = 0;
	
	public function __construct(User $user, $name = "ActivityCalendar"){
		parent::__construct($name);
		$this->user = $user;
	}

	/**
	 * Get the value of user
	 * 
	 * @return User
	 */ 
	public function getUser(): User {
		return $this->user;
	}

	/**
	 * Set the value of user
	 *
	 * @param User $user
	 * @return void
	 */ 
	public function setUser(User $user): void {
		$this->user = $user;
	}


	public function getHTML(){
		$calender = $this->buildCalendar();
		$logs = $this->buildLogs();
		$this->html = '<div class="panel panel-white panel-activity" data-user="' . $this->user->id . '">
		<div class="panel-heading">
			<i class="clip-calendar-3"></i> ' . t("userpanel.profile.activity_calendar.title", ['activities' => $this->totalActivities]) . '
			<div class="panel-tools">
				<div class="calendar-guide">
					<div class="calendar-square tooltips color0" title="' . t("userpanel.profile.activity_calendar.no_activity") . '"></div>
					<div class="calendar-square tooltips color1" title="' . t("userpanel.profile.activity_calendar.up_to_activities", ['activities' => 20]) . '"></div>
					<div class="calendar-square tooltips color2" title="' . t("userpanel.profile.activity_calendar.up_to_activities", ['activities' => 40]) . '"></div>
					<div class="calendar-square tooltips color3" title="' . t("userpanel.profile.activity_calendar.up_to_activities", ['activities' => 60]) . '"></div>
					<div class="calendar-square tooltips color4" title="' . t("userpanel.profile.activity_calendar.up_to_activities", ['activities' => 80]) . '"></div>
				</div>
			</div>
		</div>
		<div class="panel-body">' . $calender . $logs .  '</div>
	</div>';
		return $this->html;
	}

	protected function buildCalendar(): string {
		$now = Date::time();
		$lastyear = date::mktime(null, null, null, Date::format('n') + 1, Date::format('j'), Date::format('Y') - 1);
		$months = "";
		$dates = "";
		$activity = new Log\Activity($this->user->id);
		$activity->from($lastyear);
		$activities = [];
		foreach ($activity->dayByDay() as $item) {
			$activities[Date::format("Y/n/j", strtotime($item['date']))] = $item['activities'];
		}
		for ($first = true, $i = 0, $w = 0; $lastyear <= $now; $first = false, $lastyear += 86400) {
			$date = intval(Date::format('j', $lastyear));
			$month = intval(Date::format('n', $lastyear));
			$year = intval(Date::format('Y', $lastyear));
			$day = intval(Date::format('w', $lastyear));
			if ($day == 0 and !$first) {
				$dates .= '</div>';
			}
			if ($day == 0 or $first) {
				$dates .= '<div class="column">';
			}
			if ($first) {
				for ($x = 0;$x < $day; $x++) {
					$dates .= '<div class="calendar-square calendar-square-empty"></div>';
				}
			}
			if (($dates or $day == 0 or $date == 1) and $i != $month) {
				if ($day == 0) {
					$i = $month;
					$months .= '<div class="month month-margin-' . $w . '">' . date::format("F", $lastyear) . '</div>';
					$w = 0;
				} else {
					$w++;
				}
			}
			$count = $activities[$year .'/' . $month . '/' . $date] ?? 0;
			$color = min(ceil($count / 20), 4);
			$dates .= '<div class="calendar-square tooltips color' . $color . '" title="' . ($count ? $count . " فعالیت" : "هیچ فعالیتی") . " در " .date::format("d F Y", $lastyear) . '"';
			if ($count) {
				$dates .= ' data-from="'.$year .'/' . $month . '/' . $date . '"';
				$dates .= ' data-until="' . Date::format("Y/n/j", $lastyear + 86400) . '"';
			}
			$dates .= '></div>';
		}
		$dates .= '</div>';
		$this->totalActivities = array_sum($activities);
		return '<div class="calender">
			<div class="days">
				<div class="day">' . t("userpanel.profile.activity_calendar.sunday") . '</div>
				<div class="day">' . t("userpanel.profile.activity_calendar.tuesday") . '</div>
				<div class="day">' . t("userpanel.profile.activity_calendar.thursday") . '</div>
		 	</div>
			<div class="months">' . $months . '</div>
			<div class="dates">' . $dates . '</div>
	  </div>';
	}
	protected function buildLogs(): string {
		$html = '<div class="panel-scroll" style="height:300px">';
		$html .= '<ul class="activities">';
		$canView = Authorization::is_accessed('logs_view');
		foreach ($this->getLogs() as $log) {
			$lHandler = $log->getHandler();
			$html .= '<li>';
			$html .= '<a class="activity" href="' . ($canView ? userpanel\url("logs/view/{$log->id}") : "#") . '">';
			$html .= '<i class="circle-icon '. $lHandler->getColor() . ' ' . $lHandler->getIcon() . '"></i>';
			$html .= ' <span class="desc">' . $log->title . '</span>';
			$html .= '<div class="time">';
			$html .= '<i class="fa fa-time bigger-110"></i>' . Date::relativeTime($log->time);
			$html .= '</div>';
			$html .= '</a>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}
	protected function getLogs($limit = 50) {
		return (new Log())
			->where("user", $this->user->id)
			->orderBy("time", "DESC")
			->get($limit);
	}

}