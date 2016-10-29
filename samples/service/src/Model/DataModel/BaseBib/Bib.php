<?php
namespace NYPL\ServiceSample\Model\DataModel\BaseBib;

use NYPL\ServiceSample\Model\DataModel\BaseBib;
use NYPL\Starter\Model\Schema;
use NYPL\Starter\Model\ModelInterface\DeleteInterface;
use NYPL\Starter\Model\ModelInterface\MessageInterface;
use NYPL\Starter\Model\ModelInterface\ReadInterface;
use NYPL\Starter\Model\ModelTrait\DBCreateTrait;
use NYPL\Starter\Model\ModelTrait\DBDeleteTrait;
use NYPL\Starter\Model\ModelTrait\DBReadTrait;

/**
 * @SWG\Definition(title="Bib", type="object", required={"id"})
 */
class Bib extends BaseBib implements MessageInterface, ReadInterface, DeleteInterface
{
    use DBCreateTrait, DBReadTrait, DBDeleteTrait;

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
                    ["name" => "deletedDate", "type" => ["string", "null"]],
                    ["name" => "deleted", "type" => "boolean"],
                    ["name" => "locations" , "type" => [
                        "null",
                        ["type" => "array", "items" => [
                            ["name" => "Location", "type" => "record", "fields" => [
                                ["name" => "code", "type" => ["string", "null"]],
                                ["name" => "name", "type" => ["string", "null"]],
                            ]]
                        ]],
                    ]],
                    ["name" => "suppressed", "type" => ["boolean", "null"]],
                    ["name" => "lang" , "type" => [
                        ["name" => "lang", "type" => "record", "fields" => [
                            ["name" => "code", "type" => "string"],
                            ["name" => "name", "type" => "string"],
                        ]],
                    ]],
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
                    ["name" => "publishYear", "type" => ["int", "null"]],
                    ["name" => "catalogDate", "type" => ["string", "null"]],
                    ["name" => "country" , "type" => [
                        ["name" => "country", "type" => "record", "fields" => [
                            ["name" => "code", "type" => "string"],
                            ["name" => "name", "type" => "string"],
                        ]],
                    ]],
                    ["name" => "normTitle", "type" => ["string", "null"]],
                    ["name" => "normAuthor", "type" => ["string", "null"]],
                    ["name" => "fixedFields" , "type" => [
                        "null",
                        ["type" => "array", "items" => [
                            ["name" => "fixedField", "type" => "record", "fields" => [
                                ["name" => "label", "type" => ["string", "null"]],
                                ["name" => "value", "type" => ["string", "null"]],
                                ["name" => "display", "type" => ["string", "null"]],
                            ]]
                        ]],
                    ]],
                    ["name" => "varFields" , "type" => [
                        "null",
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

    public function getSequenceId()
    {
        return "";
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
