<?php
namespace NYPL\API\Model\ModelTrait;

use NYPL\API\APIException;
use NYPL\API\DB;
use NYPL\API\Filter;
use NYPL\API\Model;
use Slim\PDO\Statement\SelectStatement;

trait DBReadTrait
{
    protected function setSingle($id)
    {
        $selectStatement = DB::getDatabase()->select()
            ->from($this->getTableName())
            ->where($this->getIdName(), "=", $id);

        $selectStatement = $selectStatement->execute();

        if (!$selectStatement->rowCount()) {
            throw new APIException("No record found");
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
                $selectStatement->whereLike($filter->getFilterColumn(), '%"' . $filter->getFilterValue() . '"%');
            } else {
                $selectStatement->where($filter->getFilterColumn(), "=", $filter->getFilterValue());
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
            ->from($baseModel->getTableName());

        if ($this->getOffset()) {
            $selectStatement->limit($this->getLimit(), $this->getOffset());
        } else {
            $selectStatement->limit($this->getLimit());
        }

        if ($this->getOrderBy()) {
            $selectStatement->orderBy($this->getOrderBy(), $this->getOrderDirection());
        }

        if ($this->getFilters()) {
            $this->setFilter($selectStatement);
        }

        $selectStatement = $selectStatement->execute();

        if (!$selectStatement->rowCount()) {
            throw new APIException('No records found');
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

    /**
     * @param string $id
     *
     * @return bool
     */
    public function read($id = '')
    {
        if ($id) {
            $this->setSingle($id);
            return true;
        }

        $this->setSet();
        return true;
    }
}
