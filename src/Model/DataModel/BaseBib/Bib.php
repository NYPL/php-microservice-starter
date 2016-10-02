<?php
namespace NYPL\API\Model\DataModel\BaseBib;

use NYPL\API\Model\DataModel\BaseBib;
use NYPL\API\Model\DataModel\Schema;
use NYPL\API\Model\ModelInterface\MessageInterface;
use NYPL\API\Model\ModelInterface\ReadInterface;
use NYPL\API\Model\ModelTrait\CreateTrait;
use NYPL\API\Model\ModelTrait\DBReadTrait;

/**
 * @SWG\Definition(title="Bib", type="object", required={"id"})
 */
class Bib extends BaseBib implements MessageInterface, ReadInterface
{
    use CreateTrait, DBReadTrait;

    /**
     * @SWG\Property(example="s17746307")
     * @var string
     */
    public $id;

    public function getSchema()
    {
        return new Schema(
            1,
            [
                "name" => "Bib",
                "type" => "record",
                "fields" => [
                    ["name" => "id", "type" => "string"],
                    ["name" => "updatedDate", "type" => "string"],
                    ["name" => "createdDate", "type" => "string"],
                    ["name" => "deleted", "type" => "boolean"],
                    ["name" => "suppressed", "type" => "boolean"],
                    ["name" => "lang", "type" => "string"],
                    ["name" => "title", "type" => "string"],
                    ["name" => "author", "type" => "string"],
                    ["name" => "materialType" , "type" => [
                        ["name" => "materialType", "type" => "record", "fields" => [
                            ["name" => "code", "type" => "string"],
                            ["name" => "value", "type" => "string"],
                        ]],
                    ]],
                    ["name" => "bibLevel" , "type" => [
                        ["name" => "bibLevel", "type" => "record", "fields" => [
                            ["name" => "code", "type" => "string"],
                            ["name" => "value", "type" => "string"],
                        ]],
                    ]],
                    ["name" => "publishYear", "type" => "int"],
                    ["name" => "catalogDate", "type" => "string"],
                    ["name" => "country", "type" => "string"],
                    ["name" => "fixedFields" , "type" => [
                        ["type" => "array", "items" => [
                            ["name" => "fixedField", "type" => "record", "fields" => [
                                ["name" => "label", "type" => ["string", "null"]],
                                ["name" => "value", "type" => ["string", "null"]],
                                ["name" => "display", "type" => ["string", "null"]],
                            ]]
                        ]],
                    ]],
                    ["name" => "varFields" , "type" => [
                        ["type" => "array", "items" => [
                            ["name" => "varField", "type" => "record", "fields" => [
                                ["name" => "fieldTag", "type" => ["string", "null"]],
                                ["name" => "marcTag", "type" => ["string", "null"]],
                                ["name" => "ind1", "type" => ["string", "null"]],
                                ["name" => "ind2", "type" => ["string", "null"]],
                                ["name" => "content", "type" => ["string", "null"]],
                                ["name" => "subFields" , "type" => [
                                    "null",
                                    ["type" => "array", "items" => [
                                        ["name" => "subField", "type" => "record", "fields" => [
                                            ["name" => "tag", "type" => ["string", "null"]],
                                            ["name" => "content", "type" => ["string", "null"]],
                                        ]]
                                    ]],
                                ]],
                            ]]
                        ]],
                    ]],
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public function getIdName()
    {
        return "id";
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
