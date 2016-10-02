<?php
namespace NYPL\API\Model\ModelInterface;

use NYPL\API\Model;
use NYPL\API\Model\DataModel\Schema;

interface MessageInterface
{
    /**
     * @return Schema
     */
    public function getSchema();
}
