<?php
/**
 * rio-sgps
 * Decorators.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2018
 *
 * @author Aryel Tupinamba <aryel.tupinamba@lqdi.net>
 *
 * Created at: 17/09/2018, 15:52
 */

namespace SGPS\Utils;


use Carbon\Carbon;
use SGPS\Entity\Flag;
use SGPS\Entity\FlagAttribution;

class Decorators {

	public static function getFlagAttributionBackgroundClass(FlagAttribution $attribution) {

		switch($attribution->entity_type) {
			case 'family': return 'text-primary';
			case 'residence': return 'text-success';
			case 'person': return 'text-info';
			default: return '';
		}

	}

	public static function getFlagDeadline(Carbon $deadline) {
		return "{$deadline->toDateString()} ({$deadline->diffForHumans()})";
	}

}