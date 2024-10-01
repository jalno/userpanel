<?php
namespace packages\userpanel;

use function packages\base\json\{Encode, Decode};

use packages\base\{Exception, Http};

/**
 * @property bool $recursivelySerialize @see \packages\base\DB\DBObject
 */
trait CursorPaginateTrait {

	private string $primaryKeyName = "";
	private string $cursorName = "cursor";
	private ?string $nextPageCursor = null;
	private ?string $prevPageCursor = null;
	private bool $hasPages = false;
	private bool $_hasNextPage = false;
	private bool $_hasPrevPage = false;
	private int $_count = 0;
	private ?bool $isAscendingSort = null;

	public function setPrimaryKeyName(string $name): void {
		$this->perimaryKeyName = $name;
	}

	/**
	 * Set the query string variable used to store the cursor.
	 */
	public function setCursorName(string $name): void {
		$this->cursorName = $name;
	}

	/**
	 * Determine if there are enough items to get in next page.
	 */
	public function hasNextPage(): bool {
		return $this->_hasNextPage;
	}

	/**
	 * Determine if there are enough items to get in previous page.
	 */
	public function hasPrevPage(): bool {
		return $this->_hasPrevPage;
	}

	/**
	 * Determine if there are enough items to split into multiple pages.
	 */
	public function hasPages(): bool {
		return $this->_hasNextPage > 0 or $this->_hasPrevPage > 0;
	}

	/**
	 * Get the number of items for the current page.
	 */
	public function countItems(): int {
		return $this->_count;
	}
	
	public function getCursorName(): string {
		return $this->cursorName;
	}

	public function getNextPageCursor(): ?string {
		return $this->nextPageCursor;
	}

	public function getPrevPageCursor(): ?string {
		return $this->prevPageCursor;
	}

	public function getCursorValue(int $id, bool $pointToNextPage = true): string {
		return base64_encode(Encode(["id" => $id, "pointToNextPage" => $pointToNextPage]));
	}

	/**
	 * @param string|string[] $columns
	 */
	public function cursorPaginate(string $order, ?int $perPage = null, $columns = '*'): array {
		
		if (!$this->getPrimaryKeyName()) {
			throw new Exception("Can not use cursor pagination when table has not a primary key");
		}

        self::$recursivelySerialize = true;
		$clonedQuery = clone $this;
		$dbSerialized = serialize($clonedQuery->db);
        self::$recursivelySerialize = false;

		$primaryKey = $this->getPrimaryKeyWithTableName();

		$order = strtoupper($order);

		$this->orderBy($primaryKey, $order);

		$this->isAscendingSort = $order == "ASC";

		if (!$perPage) {
			$perPage = $this->pageLimit ?: 25;
		}
		
		$cursor = $this->getCursor();

		$needToSort = false;

		if ($cursor) {

			if (!$cursor["pointToNextPage"]) {
				$this->orderBy($primaryKey, $order == "ASC" ? "DESC" : "ASC");

				$needToSort = true;
			}

			$getFlagBaseOnOrderByAndCursorPoint = function(array $cursor, bool $isAscendingSort): string {
				if ($cursor["pointToNextPage"]) {
					return $isAscendingSort ? ">" : "<";
				} else {
					return $isAscendingSort ? "<" : ">";
				}
			};
			
			$this->where($primaryKey, $cursor["id"], $getFlagBaseOnOrderByAndCursorPoint($cursor, $this->isAscendingSort));
		}

		$data = $this->get($perPage, $columns);

		$getQuery = function() use($clonedQuery, $dbSerialized): self {
			self::$recursivelySerialize = true;
			$clone = clone $clonedQuery;
			self::$recursivelySerialize = false;
			$clone->db = unserialize($dbSerialized);
			$clone->db->connect();

			return $clone;
		};

		$this->_count = count($data);

		if ($this->_count > 0) {

			if ($needToSort) {

				$primaryField = $this->getPrimaryKeyName();

				usort($data, function($a, $b) use($order, $primaryField) {
					return $order == "ASC" ? $a->{$primaryField} <=> $b->{$primaryField} : $b->{$primaryField} <=> $a->{$primaryField};
				});
			}

			$ids = array_column($data, "id");

			$max = max($ids);
			$min = min($ids);

			$query = $getQuery();
			$query->where($primaryKey, $this->isAscendingSort ? $max : $min, $this->isAscendingSort ? ">" : "<");

			$this->_hasNextPage = $query->has($this->dbTable);

			$query = $getQuery();
			$query->where($primaryKey, $this->isAscendingSort ? $min : $max, $this->isAscendingSort ? "<" : ">");

			$this->_hasPrevPage = $query->has($this->dbTable);

			if ($this->_hasNextPage) {
				$this->nextPageCursor = $this->getCursorValue($this->isAscendingSort ? $max : $min);
			}
			if ($this->_hasPrevPage) {
				$this->prevPageCursor = $this->getCursorValue($this->isAscendingSort ? $min : $max, false);
			}
		} else {
			$this->_hasNextPage = false;
			$this->_hasPrevPage = false;
			$this->nextPageCursor = null;
			$this->prevPageCursor = null;
		}

		return $data;
	}

	private function getPrimaryKeyName(): string {
		if (!$this->primaryKeyName and isset($this->primaryKey) and $this->primaryKey) {
			$this->primaryKeyName = $this->primaryKey;
		}

		return $this->primaryKeyName;
	}

	private function getPrimaryKeyWithTableName(): string {
		$primaryKey = $this->getPrimaryKeyName();

		return strpos($primaryKey, ".") > -1 ? $primaryKey : "{$this->dbTable}.{$primaryKey}";
	}

	private function getCursor(): array {
		$after = Http::getData("after");
		if ($after and is_numeric($after)) {
			return ["id" => $after, "pointToNextPage" => true];
		}

		$before = Http::getData("before");
		if ($before and is_numeric($before)) {
			return ["id" => $before, "pointToNextPage" => false];
		}

		$cursor = Http::getData($this->cursorName);
		if ($cursor) {
			try {
				$cursor = Decode(base64_decode($cursor));

				if (isset($cursor["id"], $cursor["pointToNextPage"])) {
					return $cursor;
				}
			} catch (\Exception $e) {}
		}

		return [];
	}
}
