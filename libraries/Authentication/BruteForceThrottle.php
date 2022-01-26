<?php
namespace packages\userpanel\Authentication;

use InvalidArgumentException;
use packages\base\{Cache, view\Error, HTTP, Json, Options, Session};
use packages\userpanel\Date;


/**
 * @phpstan-type OptionsType array{
 * 		'cache-name'?:string,
 * 	}
 */
class BruteForceThrottle {

	/**
	 * @var OptionsType
	 */
	protected static $defaultOptions = array(
		'cache-name' => null,
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
	 * @param null|array{} $options
	 * @param null|int $period that is the period of watch for brute force
	 * @param null|int $count
	 * @param null|callable(int $totalChances, int $period),:void $onMissAllChances
	 * @param null|callable(int $failedCount):void $onMissOneChance
	 * @param OptionsType $options
	 */
	public function __construct(
		string $channel = 'default',
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

		$this->onMissOneChance = $onMissOneChance;

		$this->onMissAllChances = $onMissAllChances ?: function (int $failedCount): void {
			$error = new Error('packages.userpanel.bruteforce_throttle.miss_all_chances');
			$error->setData(false, 'closeable');
			$error->setMessage(t('error.packages.userpanel.bruteforce_throttle.miss_all_chances', [
				'limit' => $failedCount,
				'expire_at' => Date::relativeTime(Date::time() + $this->period)
			]));
			throw $error;
		};

		if ($this->sessionLimit) {
			Session::start();
		}
	}

	public function mustHasChance(): void {
		if (!$this->hasChance()) {
			call_user_func($this->onMissAllChances, $this->totalLimit, $this->period);
		}
	}

	public function loseOneChance(): void {
		$sessionBasedTriesCount = null;
		if ($this->isSessionBeingUsed()) {
			$sessionBasedTriesCount = Cache::get($this->getSessionBasedCacheName()) ?: 0;
			Cache::set($this->getSessionBasedCacheName(), $sessionBasedTriesCount + 1, $this->period);
		}
		$totalTriesCount = Cache::get($this->getCacheName()) ?: 0;
		Cache::set($this->getCacheName(), $totalTriesCount + 1, $this->period);

		if ($this->onMissOneChance) {
			call_user_func(
				$this->onMissAllChances,
				($sessionBasedTriesCount !== null ? $sessionBasedTriesCount + 1 : $totalTriesCount + 1)
			);
		}
	}

	public function hasChance(): bool {
		$totalTries = Cache::get($this->getCacheName()) ?? 0;
		$hasChance = $totalTries < $this->totalLimit;

		if ($hasChance and $this->isSessionBeingUsed()) {
			$sessionBasedTries = Cache::get($this->getSessionBasedCacheName()) ?? 0;
			$hasChance = $sessionBasedTries < $this->sessionLimit;
		}

		return $hasChance;
	}

	private function getCacheName(): string {
		return $this->options['cache-name'] . '.ip-' . HTTP::$client['ip'];
	}

	private function getSessionBasedCacheName(): ?string {
		if ($this->isSessionBeingUsed()) {
			if (!$this->sessionID) {
				$this->sessionID = Session::getID();
			}
			return $this->options['cache-name'] . '.ip-' . HTTP::$client['ip'] . '.session-' . $this->sessionID;
		}
		return null;
	}

	private function isSessionBeingUsed(): bool {
		return intval($this->sessionLimit);
	}
}
