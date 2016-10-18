<?php
namespace NYPL\Starter\Model\ModelTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\DB;
use NYPL\Starter\Filter;
use NYPL\Starter\Model;
use Slim\PDO\Statement\SelectStatement;

trait DBReadTrait
{
    protected function setSingle($id)
    {
        $selectStatement = DB::getDatabase()->select()
            ->from($this->getTableName())
            ->where($this->getIdName(), '=', $id);

        $selectStatement = $selectStatement->execute();

        if (!$selectStatement->rowCount()) {
            throw new APIException("No record found", [], 0, null, 404);
        }

        $this->translate($selectStatement->fetch());
    }

    /**
     * @param SelectStatement $selectStatement
     */
    protected function setFilter(SelectStatement $selectStatement)
    {
        /**
         * @var Filter $filter
         */
        foreach ($this->getFilters() as $filter) {
            if ($filter->isJsonColumn()) {
                $selectStatement->whereLike(
                    $this->translateDbName($filter->getFilterColumn()),
                    '%"' . $filter->getFilterValue() . '"%'
                );
            } else {
                $selectStatement->where(
                    $this->translateDbName($filter->getFilterColumn()),
                    '=',
                    $filter->getFilterValue()
                );
            }
        }
    }

    protected function setSet()
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
            $selectStatement->orderBy($this->translateDbName($this->getOrderBy()), $this->getOrderDirection());
        }

        if ($this->getFilters()) {
            $this->setFilter($selectStatement);
        }

        $selectStatement = $selectStatement->execute();

        if (!$selectStatement->rowCount()) {
            throw new APIException("No records found", [], 0, null, 404);
        }

        foreach ($selectStatement->fetchAll() as $result) {
            /**
             * @var Model|TranslateTrait $model
             */
            $model = clone $this->getBaseModel();
            $model->translate($result);

            $this->addModel($model);
        }
    }

    public function read($id = "")
    {
        if ($id) {
            $this->setSingle($id);
            return true;
        }

        $this->setSet();
        return true;
    }
}
