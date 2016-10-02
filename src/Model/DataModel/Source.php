<?php
namespace NYPL\API\Model\DataModel;

use NYPL\API\Model;
use NYPL\API\Model\ModelTrait\CreateTrait;
use NYPL\API\Model\ModelTrait\TranslateTrait;

class Source extends Model
{
    use TranslateTrait, CreateTrait;

    public $id = '';

    public $sourceCode = '';

    public $sourceId = '';

    /**
     * @var \DateTime
     */
    private $createdDate;

    public function getIdName()
    {
        return 'id';
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

    /**
     * @param string $id
     */
    public function translateId($id = '')
    {
        $this->setSourceCode('s');
        $this->setSourceId($id);
    }

    /**
     * @return string
     */
    public function getSourceCode()
    {
        return $this->sourceCode;
    }

    /**
     * @param string $sourceCode
     */
    public function setSourceCode($sourceCode)
    {
        $this->sourceCode = $sourceCode;
    }

    /**
     * @return string
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @param string $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }
}
