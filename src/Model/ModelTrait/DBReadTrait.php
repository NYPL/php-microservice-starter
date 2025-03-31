<?php
namespace NYPL\Starter\Model\ModelTrait;

use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Grouping;
use NYPL\Starter\APIException;
use NYPL\Starter\Slim\DB;
use NYPL\Starter\Filter;
use NYPL\Starter\Filter\OrFilter;
use NYPL\Starter\Model;
use NYPL\Starter\ModelSet;
use NYPL\Starter\OrderBy;
use NYPL\Starter\Slim\ExtendedSelectOrUpdateInterface;
use FaaPz\PDO\Clause\Limit;
use FaaPz\PDO\Clause\Raw;

trait DBReadTrait
{
    /**
     * @throws APIException
     * @return ExtendedSelectOrUpdateInterface
     */
    protected function getSingleSelect()
    {
        $selectStatement = DB::getDatabase()->select()
            ->from($this->getTableName());
        $this->applyFilters($this->getFilters(), $selectStatement);

        return $selectStatement;
    }

    /**
     * @param bool $ignoreNoRecord
     *
     * @return bool
     * @throws APIException
     */
    protected function setSingle($ignoreNoRecord = false)
    {
        $selectStatement = $this->getSingleSelect();

        $selectStatement = $selectStatement->execute();

        if ($selectStatement->rowCount() || !$ignoreNoRecord) {
            if (!$selectStatement->rowCount()) {
                throw new APIException('No record found', [], 0, null, 404);
            }

            if ($selectStatement->rowCount() > 1) {
                throw new APIException('Multiple records were returned');
            }

            $this->translate($selectStatement->fetch());

            return true;
        }

        return false;
    }

    /**
     * @param Filter $filter
     *
     * @return string
     */
    protected function getOperator(Filter $filter)
    {
        if ($filter->getOperator()) {
            return $filter->getOperator();
        }

        return '=';
    }

    /**
     * @param int $count
     * @param Filter $filter
     * @param ExtendedSelectOrUpdateInterface $selectStatement
     *
     * @return bool
     */
    protected function applyOrWhere($count, Filter $filter, ExtendedSelectOrUpdateInterface $selectStatement)
    {
        if (!$count) {
            $this->addWhere($filter, $selectStatement);

            return true;
        }

        $conditional = new Conditional(
            $this->translateDbName($filter->getFilterColumn()),
            $this->getOperator($filter),
            $filter->getFilterValue()
        );

        // If a where clause exists, augment it with this conditional using "OR", otherwise add it as the first where.
        if (method_exists($selectStatement, 'getWhere') && $existingWhere = $selectStatement->getWhere()) {
            $grouping = new Grouping('OR', $conditional, $existingWhere);
        } else {
            $grouping = $conditional;
        }
        $selectStatement->where($grouping);

        return true;
    }

    /**
     * @param int $count
     * @param Filter $filter
     * @param ExtendedSelectOrUpdateInterface $selectStatement
     *
     * @return bool
     */
    protected function applyAndWhere($count, Filter $filter, ExtendedSelectOrUpdateInterface $selectStatement)
    {
        if (!$count) {
            $selectStatement->where(new Conditional(
                $this->translateDbName($filter->getFilterColumn()),
                $this->getOperator($filter),
                $filter->getFilterValue()
            ));
            return true;
        }

        $this->addWhere($filter, $selectStatement);

        return true;
    }

    /**
     * @param OrFilter $filter
     * @param ExtendedSelectOrUpdateInterface $selectStatement
     */
    protected function addOrWhere(OrFilter $orFilter, ExtendedSelectOrUpdateInterface $selectStatement)
    {
        $selectStatement->addParenthesis();

        foreach ($orFilter->getFilters() as $count => $filter) {
            if ($orFilter->isAndFilters()) {
                $this->applyAndWhere($count, $filter, $selectStatement);
            } else {
                $this->applyOrWhere($count, $filter, $selectStatement);
            }
        }

        $selectStatement->closeParenthesis();
    }

    /**
     * @param Filter $filter
     * @param ExtendedSelectOrUpdateInterface $selectStatement
     *
     * @return bool
     * @throws APIException
     */
    protected function addWhere(Filter $filter, ExtendedSelectOrUpdateInterface $selectStatement)
    {
        if ($filter instanceof OrFilter) {
            $this->addOrWhere($filter, $selectStatement);

            return true;
        }

        if ($filter->isJsonColumn()) {
            $this->addJsonWhere($filter, $selectStatement);

            return true;
        }

        if ($filter->getFilterValue() === null) {
            $selectStatement->where(new Conditional(
                $this->translateDbName($filter->getFilterColumn()),
                "IS",
                new Raw("NULL")
            ));

            return true;
        }

        if (is_array($filter->getFilterValue())) {
            $selectStatement->where(new Conditional(
                $filter->getFilterColumn(),
                "IN",
                $filter->getFilterValue(),
            ));

            return true;
        }

        if ($filter->isRangeFilter()) {
            $this->applyRange($filter, $selectStatement);

            return true;
        }

        $this->applyWhere($filter, $selectStatement);

        return true;
    }

    /**
     * @param Filter $filter
     * @param ExtendedSelectOrUpdateInterface $selectStatement
     *
     * @return bool
     * @throws APIException
     */
    protected function addJsonWhere(Filter $filter, ExtendedSelectOrUpdateInterface $selectStatement)
    {
        $values = explode(',', $filter->getFilterValue());

        $valueString = '';

        foreach ($values as $value) {
            $valueString .= DB::getDatabase()->quote($value) . ',';
        }

        $selectStatement->where(new Conditional(
            'jsonb_contains_or(' . $this->translateDbName($filter->getFilterColumn()) . ', array[' .
              substr($valueString, 0, -1) . '])',
            '=',
            true
        ));

        return true;
    }

    /**
     * @param Filter $filter
     * @param ExtendedSelectOrUpdateInterface $selectStatement
     *
     * @return bool
     * @throws APIException
     */
    protected function applyRange(Filter $filter, ExtendedSelectOrUpdateInterface $selectStatement)
    {
        $this->setOrderBy($this->translateDbName($filter->getFilterColumn()));

        $range = explode(',', substr($filter->getFilterValue(), 1, -1));

        if (!isset($range[1])) {
            $range[1] = null;
        }

        if (!$range[0]) {
            if (!$range[1]) {
                throw new APIException(
                    'No end date was specified for ' . $filter->getFilterColumn() . ' range',
                    null,
                    0,
                    null,
                    400
                );
            }

            $selectStatement->where(new Conditional(
                $this->translateDbName($filter->getFilterColumn()),
                '<',
                $range[1]
            ));

            return true;
        }

        if (!$range[1]) {
            $selectStatement->where(new Conditional(
                $this->translateDbName($filter->getFilterColumn()),
                '>=',
                $range[0]
            ));

            return true;
        }

        // Using two comparison operators instead of BETWEEN due to bug in FaaPz/PDO.
        // @see https://github.com/FaaPz/PDO/issues/175#issuecomment-2766528827
        $selectStatement->where(
            new Grouping('AND',
                new Conditional(
                    $this->translateDbName($filter->getFilterColumn()),
                    '>=',
                    $range[0]
                ), new Conditional(
                    $this->translateDbName($filter->getFilterColumn()),
                    '<=',
                    $range[1]
                )
            )
        );

        return true;
    }

    /**
     * @param Filter $filter
     * @param ExtendedSelectOrUpdateInterface $selectStatement
     *
     * @return bool
     */
    protected function applyWhere(Filter $filter, ExtendedSelectOrUpdateInterface $selectStatement)
    {
        if (strpos($filter->getFilterValue(), ',') !== false) {
            $selectStatement->where(new Conditional(
                $this->translateDbName($filter->getFilterColumn()),
                "IN",
                explode(',', $filter->getFilterValue())
            ));
            return true;
        }

        $conditional = new Conditional(
            $this->translateDbName($filter->getFilterColumn()),
            $this->getOperator($filter),
            $filter->getFilterValue()
        );

        if (method_exists($selectStatement, 'getWhere') && $existingWhere = $selectStatement->getWhere()) {
            $grouping = new Grouping('AND', $conditional, $existingWhere);
        } else {
            $grouping = $conditional;
        }
        $selectStatement->where($grouping);

        return true;
    }

    /**
     * @param bool $ignoreNoRecord
     *
     * @return bool
     * @throws APIException
     */
    protected function setSet($ignoreNoRecord = false)
    {
        /**
         * @var DBTrait $baseModel
         */
        $baseModel = $this->getBaseModel();

        $selectStatement = DB::getDatabase()->select()
            ->from($baseModel->translateDbName($baseModel->getTableName()));

        if ($this->getFilters()) {
            $this->applyFilters($this->getFilters(), $selectStatement);
        }

        if ($this->getOrderBy()) {
            $this->applyOrderBy($selectStatement);
        }

        if ($this->getLimit()) {
            $selectStatement->limit(new Limit($this->getLimit(), $this->getOffset() ?? null));
        }

        $selectStatement = $selectStatement->execute();

        if (!$selectStatement->rowCount() && !$ignoreNoRecord) {
            throw new APIException('No records found', [], 0, null, 404);
        }

        if ($selectStatement->rowCount()) {
            $className = get_class($this->getBaseModel());

            if ($this->isIncludeTotalCount()) {
                $this->obtainTotalCount();
            }

            foreach ($selectStatement->fetchAll() as $result) {
                /**
                 * @var Model|TranslateTrait $model
                 */
                $model = new $className;
                $model->translate($result);

                $this->addModel($model);
            }

            return true;
        }

        return false;
    }

    /**
     * @param ExtendedSelectOrUpdateInterface $selectStatement
     *
     * @return bool
     */
    protected function applyOrderBy(ExtendedSelectOrUpdateInterface $selectStatement)
    {
        if (is_array($this->getOrderBy())) {
            /**
             * @var OrderBy $orderBy
             */
            foreach ($this->getOrderBy() as $orderBy) {
                $selectStatement->orderBy(
                    $this->translateDbName($orderBy->getColumn()),
                    $orderBy->getDirection()
                );
            }

            return true;
        }

        $selectStatement->orderBy(
            $this->translateDbName($this->getOrderBy()),
            $this->getOrderDirection()
        );

        return true;
    }

    /**
     * @return bool
     * @throws APIException
     */
    protected function obtainTotalCount()
    {
        /**
         * @var DBTrait $baseModel
         */
        $baseModel = $this->getBaseModel();

        $selectStatement = DB::getDatabase()->select(['COUNT(*)'])
            ->from($baseModel->translateDbName($baseModel->getTableName()));

        if ($this->getFilters()) {
            $this->applyFilters($this->getFilters(), $selectStatement);
        }

        $selectStatement = $selectStatement->execute();

        $this->setTotalCount($selectStatement->fetchColumn(0));

        return true;
    }

    /**
     * @param bool $ignoreNoRecord
     *
     * @return bool
     * @throws APIException
     */
    public function read($ignoreNoRecord = false)
    {
        if ($this instanceof ModelSet) {
            return $this->setSet($ignoreNoRecord);
        }

        return $this->setSingle($ignoreNoRecord);
    }
}
