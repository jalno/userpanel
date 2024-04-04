<?php
namespace packages\userpanel\Authentication;

use InvalidArgumentException;
use packages\base\{Cache, view\Error, HTTP, Json, Options, Session};
use packages\userpanel\Date;


/**
 * @phpstan-type OptionsType array{
 * 		'cache-name'?:string,
 * 		'ignore-ips'?:string[],
 * 	}
 */
class BruteForceThrottle {

	protected static function getClientIP(): string {
		return $_SERVER["HTTP_X_REAL_IP"] ?? $_SERVER["HTTP_X_FORWARDED_FOR"] ?? $_SERVER['REMOTE_ADDR'] ?? HTTP::$client['ip'] ?? '0.0.0.0';
	}

	/**
	 * @var OptionsType
	 */
	protected static $defaultOptions = array(
		'cache-name' => null,
		'ignore-ips' => [],
	);

	protected string $channel;

	protected int $period;

	protected int $totalLimit;

	protected ?int $sessionLimit = null;

	/** @var callable */
	protected $onMissAllChances;

	/** @var callable|null */
	protected $onMissOneChance;

	protected ?array $options = null;

	protected ?string $sessionID = null;

	/**
	 * @param null|callable(int $totalChances, int $period, ?int $sessionBasedChances = null),:void $onMissAllChances
	 * @param null|callable(int $failedCount):void $onMissOneChance
	 * @param OptionsType $options
	 */
	public function __construct(
		string $channel,
		int $period,
		int $totalLimit,
		?int $sessionLimit = null,
		?callable $onMissAllChances = null,
		?callable $onMissOneChance = null,
		?array $options = null
	) {
		$this->channel = $channel;
		$this->period = $period;

		$this->totalLimit = $totalLimit;
		$this->sessionLimit = $sessionLimit;
		if ($sessionLimit !== null and $sessionLimit > $totalLimit) {
			throw new InvalidArgumentException(
				"the session based limit should not bigger than total limit, total limit: {$totalLimit} session limit: {$sessionLimit}"
			);
		}

		$this->options = $options ? array_replace_recursive(self::$defaultOptions, $options) : self::$defaultOptions;

		if (!isset($this->options['cache-name'])) {
			$this->options['cache-name'] = 'packages.userpanel.brute_force_throttle.channel.' . $channel;
		} else if (!is_string($this->options['cache-name'])) {
			throw new InvalidArgumentException(
				'the cache-name option should be a non-empty string!, given: ' . Json\encode($this->options['cache-name'])
			);
		}

		if (isset($this->options['ignore-ips']) and !is_array($this->options['ignore-ips'])) {
			throw new InvalidArgumentException(
				'the ignore-ips option should be a array of string!, given: ' . Json\encode($this->options['ignore-ips'])
			);
		}

		$this->onMissOneChance = $onMissOneChance;

		$this->onMissAllChances = $onMissAllChances ?: static function (int $totalChances, int $period, ?int $sessionBasedChances = null): void {
			$error = new Error('packages.userpanel.bruteforce_throttle.miss_all_chances');
			$error->setData(false, 'closeable');
			$error->setMessage(t('error.packages.userpanel.bruteforce_throttle.miss_all_chances', [
				'limit' => is_null($sessionBasedChances) ? $totalChances : $sessionBasedChances,
				'expire_at_relative' => Date::relativeTime(Date::time() + $period),
				'expire_at_formated' => Date::format('Q QTS', Date::time() + $period),
			]));
			throw $error;
		};

	}

	public function mustHasChance(bool $preventStartSession = false): void {
		if (!$this->hasChance($preventStartSession)) {
			call_user_func($this->onMissAllChances, $this->totalLimit, $this->period, ($this->isSessionBeingUsed() ? $this->sessionLimit : null));
		}
	}

	public function loseOneChance(): void {
		$sessionBasedTriesCount = null;
		if ($this->isSessionBeingUsed()) {
			$cacheName = $this->getSessionBasedCacheName(false);
			if ($cacheName) {
				$sessionBasedTriesCount = Cache::get($cacheName) ?: 0;
				Cache::set($this->getSessionBasedCacheName(), $sessionBasedTriesCount + 1, $this->period);
			}
		}
		$totalTriesCount = Cache::get($this->getCacheName()) ?: 0;
		Cache::set($this->getCacheName(), $totalTriesCount + 1, $this->period);

		if ($this->onMissOneChance) {
			call_user_func(
				$this->onMissOneChance,
				($sessionBasedTriesCount !== null ? $sessionBasedTriesCount + 1 : $totalTriesCount + 1)
			);
		}
	}

	public function hasChance(bool $preventStartSession = false): bool {
		$hasChance = $this->hasTotalChance();
		if ($hasChance and $this->isSessionBeingUsed()) {
			$hasChance = $this->hasSessionChance($preventStartSession);
		}
		return is_null($hasChance) ?: $hasChance;
	}

	public function hasSessionChance(bool $preventStartSession = false): ?bool {
		if (!$this->isSessionBeingUsed()) {
			return null;
		}
		$cacheName = $this->getSessionBasedCacheName($preventStartSession);
		if (is_null($cacheName)) {
			return null;
		}
		$sessionBasedTries = Cache::get($cacheName) ?? 0;
		return $sessionBasedTries < $this->sessionLimit;
	}

	public function hasTotalChance(): bool {
		if (
			isset($this->options['ignore-ips']) and
			$this->options['ignore-ips'] and
			in_array(self::getClientIP(), $this->options['ignore-ips'])
		) {
			return true;
		}
		$totalTries = Cache::get($this->getCacheName()) ?? 0;
		return $totalTries < $this->totalLimit;
	}

	private function getCacheName(): string {
		return $this->options['cache-name'] . '.ip-' . self::getClientIP();
	}

	private function getSessionBasedCacheName(bool $preventStartSession = false): ?string {
		if (!$this->isSessionBeingUsed()) {
			return null;
		}
		if (!$this->sessionID) {
			$this->sessionID = Session::getID();
		}
		if (!$this->sessionID and !$preventStartSession) {
			Session::start();
			$this->sessionID = Session::getID();
		}
		return $this->sessionID ?
			$this->options['cache-name'] . '.ip-' . self::getClientIP() . '.session-' . $this->sessionID :
			null;
	}

	private function isSessionBeingUsed(): bool {
		return boolval($this->sessionLimit);
	}
}
