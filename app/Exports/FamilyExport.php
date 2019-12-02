<?php
/**
 * rio-sgps
 * FamilyExport.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2018
 *
 * @author Aryel Tupinamba <aryel.tupinamba@lqdi.net>
 *
 * Created at: 17/12/2018, 19:13
 */

namespace SGPS\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use SGPS\Entity\Family;
use SGPS\Entity\Question;
use SGPS\Services\FamilySearchService;

class FamilyExport implements FromCollection, WithHeadings, WithMapping {

	private $results = null;

	private $baseHeadings = [
		'ID',
		'Código',
		'Código Residência',
		'Responsável',
		'Setor',
		'Bairro',
		'AP',
		'RA',
		'RP',
		'CAP',
		'CASDH',
		'CMS',
		'CRAS',
		'CRE',
		'ESF',
		'Endereço',
		'Referência',
		'Latitude',
		'Longitude',
	];

	private $questionCodes = [];
	private $headings = [];
	private $bairros = [];

	public function __construct(array $filters = [], FamilySearchService $service) {
		$query = Family::query()
			->with(['sector', 'residence', 'personInCharge', 'answers'])
			->orderBy('created_at', 'desc');

		$query = $service->applyFiltersToQuery($query, collect($filters));

		$families = $query->get();

		$this->results = $families->map(function ($family) { /* @var $family \SGPS\Entity\Family */
			return collect($family->toExportArray(true));
		});
		$queryQuestions = Question::select(\DB::raw("CONCAT(CONCAT(code, '||'),title) AS code"))
			->where('entity_type', 'family')
			->get();

		$this->questionCodes = $queryQuestions
			->pluck('code')
			->toArray();

		$this->headings = array_merge($this->baseHeadings, $this->questionCodes);
		$this->bairros = config('geo_bairros');
	}

	public function collection() {
		return $this->results;
	}

	public function map($family) : array {

		return collect($this->headings)
			->map(function ($key) use ($family) {
				if($key == "Bairro")
					return $this->bairros[$family[$key]]['name'];
				$tmpkey = explode("||", $key)[0];
				return $family[$tmpkey] ?? '';
			})
			->toArray();
	}

	public function headings(): array {
		return $this->headings;
	}

}