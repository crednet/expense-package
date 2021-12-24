<?php

namespace Credpal\Expense\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DatatableExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{

    protected $data;
    protected $headings;
    protected $mapping;

    public function __construct($data, $headings, \Closure $mapping)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->mapping = $mapping;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($row): array
    {
        $map = call_user_func_array($this->mapping, [$row]);
        return $map;
    }
}
