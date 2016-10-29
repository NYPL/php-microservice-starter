<?php
namespace NYPL\ServiceSample\Model\DataModel;

use NYPL\ServiceSample\Model\DataModel;
use NYPL\Starter\Model\LocalDateTime;
use NYPL\Starter\Model\ModelTrait\TranslateTrait;

abstract class BaseBib extends DataModel
{
    use TranslateTrait;

    /**
     * @SWG\Property(example="2016-01-07T02:32:51Z", type="string")
     * @var LocalDateTime
     */
    public $updatedDate;

    /**
     * @SWG\Property(example="2008-12-24T03:16:00Z", type="string")
     * @var LocalDateTime
     */
    public $createdDate;

    /**
     * @SWG\Property(example="2008-12-24", type="string")
     * @var LocalDateTime
     */
    public $deletedDate;

    /**
     * @SWG\Property(example=false)
     * @var bool
     */
    public $deleted;

    /**
     * @SWG\Property()
     * @var Location[]
     */
    public $locations;

    /**
     * @SWG\Property(example=false)
     * @var bool
     */
    public $suppressed;

    /**
     * @SWG\Property
     * @var Language
     */
    public $lang;

    /**
     * @SWG\Property(example="Harry Potter and the Chamber of Secrets")
     * @var string
     */
    public $title;

    /**
     * @SWG\Property(example="Rowling, J. K.")
     * @var string
     */
    public $author;

    /**
     * @SWG\Property()
     * @var MaterialType
     */
    public $materialType;

    /**
     * @SWG\Property()
     * @var BibLevel
     */
    public $bibLevel;

    /**
     * @SWG\Property(example=1999)
     * @var int
     */
    public $publishYear;

    /**
     * @SWG\Property(example="2008-12-24", type="string")
     * @var LocalDateTime
     */
    public $catalogDate;

    /**
     * @SWG\Property
     * @var Country
     */
    public $country;

    /**
     * @SWG\Property()
     * @var string
     */
    public $normTitle;

    /**
     * @SWG\Property()
     * @var string
     */
    public $normAuthor;


    /**
     * @SWG\Property()
     * @var FixedField[]
     */
    public $fixedFields;

    /**
     * @SWG\Property()
     * @var VarField[]
     */
    public $varFields;

    /**
     * @return LocalDateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * @param LocalDateTime $updatedDate
     */
    public function setUpdatedDate(LocalDateTime $updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }

    /**
     * @param string $updatedDate
     *
     * @return LocalDateTime
     */
    public function translateUpdatedDate($updatedDate = '')
    {
        return new LocalDateTime(LocalDateTime::FORMAT_DATE_TIME_RFC, $updatedDate);
    }

    /**
     * @return LocalDateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param LocalDateTime $createdDate
     */
    public function setCreatedDate(LocalDateTime $createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @param string $createdDate
     *
     * @return LocalDateTime
     */
    public function translateCreatedDate($createdDate = '')
    {
        return new LocalDateTime(LocalDateTime::FORMAT_DATE_TIME_RFC, $createdDate);
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = boolval($deleted);
    }

    /**
     * @return boolean
     */
    public function isSuppressed()
    {
        return $this->suppressed;
    }

    /**
     * @param boolean $suppressed
     */
    public function setSuppressed($suppressed)
    {
        $this->suppressed = boolval($suppressed);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return MaterialType
     */
    public function getMaterialType()
    {
        return $this->materialType;
    }

    /**
     * @param MaterialType $materialType
     */
    public function setMaterialType(MaterialType $materialType)
    {
        $this->materialType = $materialType;
    }

    /**
     * @param array|string $data
     *
     * @return MaterialType
     */
    public function translateMaterialType($data)
    {
        return new MaterialType($data, true);
    }

    /**
     * @return BibLevel
     */
    public function getBibLevel()
    {
        return $this->bibLevel;
    }

    /**
     * @param BibLevel $bibLevel
     */
    public function setBibLevel(BibLevel $bibLevel)
    {
        $this->bibLevel = $bibLevel;
    }

    /**
     * @param array|string $data
     *
     * @return BibLevel
     */
    public function translateBibLevel($data)
    {
        return new BibLevel($data, true);
    }

    /**
     * @return int
     */
    public function getPublishYear()
    {
        return $this->publishYear;
    }

    /**
     * @param int $publishYear
     */
    public function setPublishYear($publishYear)
    {
        $this->publishYear = (int) $publishYear;
    }

    /**
     * @return LocalDateTime
     */
    public function getCatalogDate()
    {
        return $this->catalogDate;
    }

    /**
     * @param LocalDateTime $catalogDate
     */
    public function setCatalogDate($catalogDate)
    {
        $this->catalogDate = $catalogDate;
    }

    /**
     * @param string $catalogDate
     *
     * @return LocalDateTime
     */
    public function translateCatalogDate($catalogDate = '')
    {
        return new LocalDateTime(LocalDateTime::FORMAT_DATE, $catalogDate);
    }

    /**
     * @return FixedField[]
     */
    public function getFixedFields()
    {
        return $this->fixedFields;
    }

    /**
     * @param FixedField[] $fixedFields
     */
    public function setFixedFields($fixedFields)
    {
        $this->fixedFields = $fixedFields;
    }

    /**
     * @param array|string $data
     *
     * @return FixedField[]
     */
    public function translateFixedFields($data)
    {
        return $this->translateArray($data, new FixedField(), true);
    }

    /**
     * @return VarField[]
     */
    public function getVarFields()
    {
        return $this->varFields;
    }

    /**
     * @param VarField[] $varFields
     */
    public function setVarFields($varFields)
    {
        $this->varFields = $varFields;
    }

    /**
     * @param array|string $data
     *
     * @return VarField[]
     */
    public function translateVarFields($data)
    {
        return $this->translateArray($data, new VarField(), true);
    }

    /**
     * @return LocalDateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * @param LocalDateTime $deletedDate
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;
    }

    /**
     * @param string $deletedDate
     *
     * @return LocalDateTime
     */
    public function translateDeletedDate($deletedDate = '')
    {
        return new LocalDateTime(LocalDateTime::FORMAT_DATE, $deletedDate);
    }

    /**
     * @return string
     */
    public function getNormTitle()
    {
        return $this->normTitle;
    }

    /**
     * @param string $normTitle
     */
    public function setNormTitle($normTitle)
    {
        $this->normTitle = $normTitle;
    }

    /**
     * @return string
     */
    public function getNormAuthor()
    {
        return $this->normAuthor;
    }

    /**
     * @param string $normAuthor
     */
    public function setNormAuthor($normAuthor)
    {
        $this->normAuthor = $normAuthor;
    }

    /**
     * @return Location[]
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param Location[] $locations
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
    }

    /**
     * @param array|string $data
     *
     * @return Location[]
     */
    public function translateLocations($data)
    {
        return $this->translateArray($data, new Location(), true);
    }

    /**
     * @return Language
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param Language $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param array|string $data
     *
     * @return Language
     */
    public function translateLang($data)
    {
        return new Language($data, true);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @param array|string $data
     *
     * @return Country
     */
    public function translateCountry($data)
    {
        return new Country($data, true);
    }
}
