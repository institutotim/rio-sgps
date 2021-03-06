<?php
/**
 * rio-sgps
 * Hydrators.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2018
 *
 * @author Aryel Tupinamba <aryel.tupinamba@lqdi.net>
 *
 * Created at: 10/10/2018, 19:07
 */

namespace SGPS\Utils;


use Carbon\Carbon;

class Hydrators {

	public static function getDeadlineDate(Carbon $baseDate, int $deadlineInDays) : Carbon {
		return $baseDate->copy()->addDays($deadlineInDays);
	}

}