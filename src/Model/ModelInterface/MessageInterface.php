<?php
namespace NYPL\Starter\Model\ModelInterface;

use NYPL\Starter\Model;
use NYPL\Services\Model\DataModel\Schema;

interface MessageInterface
{
    /**
     * @return Schema
     */
    public function getSchema();
}
