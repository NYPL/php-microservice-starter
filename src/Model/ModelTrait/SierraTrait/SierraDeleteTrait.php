<?php
namespace NYPL\Starter\Model\ModelTrait\SierraTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\Filter;
use NYPL\Starter\Model;

trait SierraDeleteTrait
{
    use Model\ModelTrait\SierraTrait;

    /**
     * @param Filter[] $filters
     *
     * @return bool
     * @throws APIException
     */
    public function delete(array $filters = [])
    {
        if (count($filters) > 1) {
            throw new APIException('Multiple filters cannot be provided');
        }

        /**
         * @var Filter $filter
         */
        $filter = current($filters);

        if (!$filter->getId()) {
            throw new APIException('No ID provided for filter');
        }

        /**
         * @var Model\ModelTrait\TranslateTrait $this
         */
        $this->sendRequest(
            $this->getSierraPath($filter->getId())
        );

        return true;
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return 'DELETE';
    }
}
