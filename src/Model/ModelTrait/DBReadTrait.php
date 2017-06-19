<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\DB;
use NYPL\Starter\Filter;
use NYPL\Starter\Filter\OrFilter;
use NYPL\Starter\Model;
use NYPL\Starter\ModelSet;
use NYPL\Starter\OrderBy;
use Slim\PDO\Statement\SelectStatement;
use Slim\PDO\Statement\StatementContainer;

trait DBReadTrait
{
    /**
     * @return SelectStatement
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
                throw new APIException("No record found", [], 0, null, 404);
            }

            if ($selectStatement->rowCount() > 1) {
                throw new APIException("Multiple records were returned");
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
     * @param StatementContainer $sqlStatement
     *
     * @return bool
     */
    protected function applyOrWhere($count, Filter $filter, StatementContainer $sqlStatement)
    {
        if (!$count) {
            $sqlStatement->where(
                $this->translateDbName($filter->getFilterColumn()),
                $this->getOperator($filter),
                $filter->getFilterValue()
            );

            return true;
        }

        $sqlStatement->orWhere(
            $this->translateDbName($filter->getFilterColumn()),
            $this->getOperator($filter),
            $filter->getFilterValue()
        );

        return true;
    }

    /**
     * @param OrFilter $filter
     * @param StatementContainer $sqlStatement
     */
    protected function addOrWhere(OrFilter $filter, StatementContainer $sqlStatement)
    {
        foreach ($filter->getFilters() as $count => $filter) {
            $this->applyOrWhere($count, $filter, $sqlStatement);
        }
    }

    /**
     * @param Filter $filter
     * @param StatementContainer $sqlStatement
     *
     * @return bool
     */
    protected function addWhere(Filter $filter, StatementContainer $sqlStatement)
    {
        if ($filter instanceof OrFilter) {
            $this->addOrWhere($filter, $sqlStatement);

            return true;
        }

        if ($filter->isJsonColumn()) {
            // See: https://dba.stackexchange.com/questions/90002/postgresql-operator-uses-index-but-underlying-function-does-not
            $sqlStatement->where(
                'jsonb_contains(' . $this->translateDbName($filter->getFilterColumn()) . ', \'' . $filter->getFilterValue() . '\')',
                '=',
                'true'
            );

            return true;
        }

        if ($filter->getFilterValue() === null) {
            $sqlStatement->whereNull(
                $this->translateDbName($filter->getFilterColumn())
            );

            return true;
        }

        $this->applyWhere($filter, $sqlStatement);

        return true;
    }

    /**
     * @param Filter $filter
     * @param StatementContainer $sqlStatement
     *
     * @return bool
     */
    protected function applyWhere(Filter $filter, StatementContainer $sqlStatement)
    {
        if (strpos($filter->getFilterValue(), ',') !== false) {
            $sqlStatement->whereIn(
                $this->translateDbName($filter->getFilterColumn()),
                explode(',', $filter->getFilterValue())
            );

            return true;
        }

        $sqlStatement->where(
            $this->translateDbName($filter->getFilterColumn()),
            $this->getOperator($filter),
            $filter->getFilterValue()
        );

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

        if ($this->getOffset()) {
            $selectStatement->offset($this->getOffset());
        }

        $selectStatement->limit($this->getLimit());

        if ($this->getOrderBy()) {
            $this->addOrderBy($selectStatement);
        }

        if ($this->getFilters()) {
            $this->applyFilters($this->getFilters(), $selectStatement);
        }

        $selectStatement = $selectStatement->execute();

        if (!$selectStatement->rowCount() && !$ignoreNoRecord) {
            throw new APIException("No records found", [], 0, null, 404);
        }

        if ($selectStatement->rowCount()) {
            foreach ($selectStatement->fetchAll() as $result) {
                /**
                 * @var Model|TranslateTrait $model
                 */
                $model = clone $this->getBaseModel();
                $model->translate($result);

                $this->addModel($model);
            }

            return true;
        }

        return false;
    }

    /**
     * @param SelectStatement $selectStatement
     *
     * @return bool
     */
    protected function addOrderBy(SelectStatement $selectStatement)
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
     * @param bool $ignoreNoRecord
     *
     * @return bool
     */
    public function read($ignoreNoRecord = false)
    {
        if ($this instanceof ModelSet) {
            return $this->setSet($ignoreNoRecord);
        }

        return $this->setSingle($ignoreNoRecord);
    }
}
