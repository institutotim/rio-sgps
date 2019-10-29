<?php
/**
 * rio-sgps
 * ResidenceExport.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2018
 *
 * @author Aryel Tupinamba <aryel.tupinamba@lqdi.net>
 *
 * Created at: 18/12/2018, 15:28
 */

namespace SGPS\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use SGPS\Entity\Person;
use SGPS\Entity\Question;
use SGPS\Entity\Residence;
use SGPS\Services\FamilySearchService;

class PersonExport implements FromCollection, WithHeadings, WithMapping {

	private $results = null;

	private $baseHeadings = [
		'ID',
		'Código',
		'Código Família',
		'Código Residência',
		'Nome',
		'Data de Nascimento',
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

	public function __construct(array $filters = [], FamilySearchService $service) {
		$query = Person::query()
			->with(['sector', 'family', 'residence', 'answers'])
			->whereHas('family', function ($sq) use ($service, $filters) {
				$service->applyFiltersToQuery($sq, collect($filters));
				return $sq;
			})
			->orderBy('created_at', 'desc');

		$persons = $query->get();

		$this->results = $persons->map(function ($person) { /* @var $person \SGPS\Entity\Person */
			return collect($person->toExportArray(true));
		});

		$this->questionCodes = Question::select(\DB::raw("CONCAT(CONCAT(code, '||'),title) AS code"))
			->where('entity_type', 'person')
			->get(['code'])
			->pluck('code')
			->toArray();

		$this->headings = array_merge($this->baseHeadings, $this->questionCodes);
	}

	public function collection() {
		return $this->results;
	}

	public function map($person) : array {

		return collect($this->headings)
			->map(function ($key) use ($person) {
				$tmpkey = explode("||", $key)[0];
				return $person[$tmpkey] ?? '';
			})
			->toArray();
	}

	public function headings(): array {
		return $this->headings;
	}

}