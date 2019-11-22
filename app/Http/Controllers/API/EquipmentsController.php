<?php
/**
 * rio-sgps
 * FamiliesController.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2018
 *
 * @author Aryel Tupinamba <aryel.tupinamba@lqdi.net>
 *
 * Created at: 16/10/2018, 18:46
 */

namespace SGPS\Http\Controllers\API;

use SGPS\Http\Controllers\Controller;

use SGPS\Entity\Equipment;


class EquipmentsController extends Controller {

	public function filter() {

        $groupsIds = ["SMS", "IPLANRIO", "CVL"];
        $rpsIds = ["1.1", "2.1"];

        $equipments = Equipment::query()->select(['equipments.*'])->distinct()
        ->join('sector_equipments', 'sector_equipments.equipment_id', '=', 'equipments.id')
        ->join('sectors', 'sector_equipments.sector_id', '=', 'sectors.id')
        ->whereIn('equipments.group_code', $groupsIds)
        ->whereIn('sectors.cod_rp', $rpsIds)->get();

		return $this->api_success([
			'equipments' => $equipments,
		]);
	}

}
