<?php
namespace NYPL\Starter\Model;

/**
 * @SWG\Definition(title="Schema", type="object", required={"id"})
 */
class Schema
{
    /**
     * @SWG\Property(example=1)
     * @var int
     */
    public $id;

    /**
     * @SWG\Property()
     */
    public $schema;

    /**
     * @param int $id
     * @param array $schema
     */
    public function __construct($id, $schema)
    {
        $this->setId($id);
        $this->setSchema($schema);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param array $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }
}
