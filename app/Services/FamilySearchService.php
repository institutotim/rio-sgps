<?php
/**
 * rio-sgps
 * FamilySearchService.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2018
 *
 * @author Aryel Tupinamba <aryel.tupinamba@lqdi.net>
 *
 * Created at: 17/10/2018, 12:35
 */

namespace SGPS\Services;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use SGPS\Entity\Family;
use SGPS\Entity\Flag;
use SGPS\Utils\Sanitizers;

class FamilySearchService {

	public function applyFiltersToQuery(Builder $query, Collection $filters) : Builder {

		if($filters->has('flags') && is_array($filters['flags']) && sizeof($filters['flags']) > 0) {
			$query = $query->where(function ($sq) use ($filters) {
				return $this->filterByFlags($sq, $filters['flags']);
			});
		}
		if($filters->has('status') && strlen($filters['status']) > 0) {
			$query = $query->where(function ($sq) use ($filters) {
				return $this->filterByStatus($sq, $filters['status']);
			});
		}
		if($filters->has('assigned_to') && strlen($filters['assigned_to']) > 0) {
			$query = $query->where(function ($sq) use ($filters) {
				return $this->filterByAssignment($sq, $filters['assigned_to']);
			});
		}

		if($filters->has('q') && strlen($filters['q']) > 0) {
			$searchQuery = Sanitizers::clearForSearch($filters['q']);
			$foundInIndex = $this->buildTextSearch($searchQuery)->get();

			$query = $query->whereIn('id', $foundInIndex->pluck('id'));
		}

		return $query;

	}

	public function filterByFlags(Builder $query, array $flags) : Builder {

		return $query->whereHas('allFlagAttributions', function($sq) use ($flags) {
			return $sq->whereIn('flag_id', $flags);
		});

	}

	public function filterByStatus(Builder $query, string $statusMode) : Builder {

		$filterCompletedOrCancelled = function ($sq) {
			return $sq->where('is_cancelled', false)->where('is_completed', false);
		};

		return ($statusMode === 'archived')
			? $query->whereHas('allFlagAttributions', $filterCompletedOrCancelled, '=', 0)
			: $query->whereHas('allFlagAttributions', $filterCompletedOrCancelled, '>', 0);

	}

	public function filterByAssignment(Builder $query, string $assignmentMode) : Builder {
		if($assignmentMode === 'all') return $query;

		return $query->whereHas('assignments', function ($sq) {
			$sq->where('user_id', auth()->id());
		});
	}

	public function buildTextSearch(string $searchQuery) : \Laravel\Scout\Builder {
		return Family::search($searchQuery);
	}

}