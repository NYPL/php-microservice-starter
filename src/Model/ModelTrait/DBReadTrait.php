<?php
namespace NYPL\Starter\Model\ModelTrait;


use NYPL\Starter\APIException;
use NYPL\Starter\Slim\DB;
use NYPL\Starter\Model;
use NYPL\Starter\ModelSet;
use NYPL\Starter\OrderBy;
use NYPL\Starter\Slim\ExtendedSelectOrUpdateInterface;
use FaaPz\PDO\Clause\Limit;

trait DBReadTrait
{
    use DBTrait;

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
