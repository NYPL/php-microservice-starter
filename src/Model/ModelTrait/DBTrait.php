<?php
namespace NYPL\Starter\Model\ModelTrait;

use FaaPz\PDO\AdvancedStatementInterface;
use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\ConditionalInterface;
use FaaPz\PDO\Clause\Grouping;
use FaaPz\PDO\Clause\Raw;
use NYPL\Starter\APIException;
use NYPL\Starter\Filter\OrFilter;
use NYPL\Starter\Slim\DB;
use NYPL\Starter\Filter;

trait DBTrait
{
    use TranslateTrait;

    /**
     * Translate object property name (camel case) to DB column name (underscored)
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->translateDbName($this->getObjectName());
    }

    /**
     * @return Filter[]
     */
    public function getIdFilters(): array
    {
        $filters = [];

        foreach ($this->getIdFields() as $idField) {
            $getterName = 'get' . $idField;

            $filters[] = new Filter($idField, $this->$getterName());
        }

        return $filters;
    }

    /**
     * Add each filter to the SQL where clause.
     *
     * @param Filter[] $filters
     * @param AdvancedStatementInterface $sqlStatement
     * @throws APIException
     */
    public function applyFilters(array $filters, AdvancedStatementInterface $sqlStatement): void
    {
        $conditionals = [];
        foreach ($filters as $filter) {
            $conditionals[] = $this->getFilterConditional($filter);
        }

        $sqlStatement->where(new Grouping('AND', ...$conditionals));
    }

    /**
     * @return int
     * @throws APIException
     */
    protected function checkExistingDb(): int
    {
        $selectStatement = DB::getDatabase()->select()
            ->from($this->translateDbName($this->getTableName()));

        $this->applyFilters(
            $this->getIdFilters(),
            $selectStatement
        );

        $selectStatement = $selectStatement->execute();

        return $selectStatement->rowCount();
    }

    /**
     * Get the appropriate where clause conditional for given filter.
     *
     * @param Filter $filter
     *
     * @return ConditionalInterface
     * @throws APIException
     */
    protected function getFilterConditional(Filter $filter): ConditionalInterface
    {

        if ($filter instanceof OrFilter) {
            return $this->getOrWhereConditional($filter);
        }

        if ($filter->isJsonColumn()) {
            return $this->getJsonWhereConditional($filter);
        }

        if ($filter->getFilterValue() === null) {
            return $this->getNullWhereConditional($filter);
        }

        if ($filter->isRangeFilter()) {
            return $this->getRangeConditional($filter);
        }

        if ($filter->isInArrayFilter()) {
            return $this->getInArrayConditional($filter);
        }

        return new Conditional(
            $this->translateDbName($filter->getFilterColumn()),
            $filter->getOperator() ?: '=',
            $filter->getFilterValue()
        );
    }

    /**
     * Get a where clause parenthetical for a group or filters separated by "OR"s.
     *
     * @param OrFilter $orFilter
     * @return Grouping
     * @throws APIException
     */
    protected function getOrWhereConditional(OrFilter $orFilter): Grouping
    {
        $operand = $orFilter->isAndFilters() ? 'AND' : 'OR';
        $clauses = [];
        foreach ($orFilter->getFilters() as $filter) {
            $clauses[] = $this->getFilterConditional($filter);
        }

        return new Grouping($operand, ...$clauses);
    }

    /**
     * Get a where clause conditional for a value of NULL.
     *
     * @param Filter $filter
     * @return Conditional
     */
    protected function getNullWhereConditional(Filter $filter): Conditional
    {
        return new Conditional(
            $this->translateDbName($filter->getFilterColumn()),
            "IS",
            new Raw("NULL")
        );
    }

    /**
     * Get a where clause conditional for matches in an array of values. Filter value can be either array, or
     * comma-separated string.
     *
     * @param Filter $filter
     * @return Conditional
     */
    protected function getInArrayConditional(Filter $filter): Conditional
    {
        $array = $filter->getFilterValue();
        if (is_string($array) && str_contains($array, ',')) {
            $array = explode(',', $filter->getFilterValue());
        }

        return new Conditional(
            $this->translateDbName($filter->getFilterColumn()),
            "IN",
            $array,
        );
    }

    /**
     * Get a where clause conditional for a value in a json string.
     *
     * @param Filter $filter
     *
     * @return Conditional
     * @throws APIException
     */
    protected function getJsonWhereConditional(Filter $filter): Conditional
    {
        $valueString = '';
        $values = explode(',', $filter->getFilterValue());
        foreach ($values as $value) {
            $valueString .= DB::getDatabase()->quote($value) . ',';
        }

        return new Conditional(
            'jsonb_contains_or(' . $this->translateDbName($filter->getFilterColumn()) . ', array[' .
            substr($valueString, 0, -1) . '])',
            '=',
            true
        );
    }

    /**
     * Get a where clause conditional for a range.
     * @param Filter $filter
     *
     * @return Conditional
     * @throws APIException
     */
    protected function getRangeConditional(Filter $filter): Conditional
    {
        $this->setOrderBy($this->translateDbName($filter->getFilterColumn()));

        $range = explode(',', substr($filter->getFilterValue(), 1, -1));

        if (!isset($range[1])) {
            $range[1] = null;
        }

        if (!$range[0] && !$range[1]) {
            throw new APIException(
                'No end date was specified for ' . $filter->getFilterColumn() . ' range',
                null,
                0,
                null,
                400
            );
        }

        if (!$range[0]) {
            return new Conditional(
                $this->translateDbName($filter->getFilterColumn()),
                '<',
                $range[1]
            );
        }

        if (!$range[1]) {
            return new Conditional(
                $this->translateDbName($filter->getFilterColumn()),
                '>=',
                $range[0]
            );
        }

        return new Conditional(
            $this->translateDbName($filter->getFilterColumn()),
            'BETWEEN',
            $range
        );
    }

}
