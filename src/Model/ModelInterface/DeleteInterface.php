<?php
namespace NYPL\Starter\Model\ModelInterface;

use NYPL\Starter\Filter;

interface DeleteInterface
{
    /**
     * @param Filter[] $filters
     *
     * @return int
     */
    public function delete(array $filters);
}
