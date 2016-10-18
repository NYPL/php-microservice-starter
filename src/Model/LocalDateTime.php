<?php
namespace NYPL\Starter\Model;

use NYPL\Starter\APIException;

class LocalDateTime
{
    const FORMAT_DATE_TIME = 1;
    const FORMAT_DATE = 2;
    const FORMAT_DATE_TIME_RFC = 3;

    /**
     * @var int
     */
    public $format;

    /**
     * @var \DateTime
     */
    public $dateTime;

    /**
     * @param int $format
     * @param $dateTimeString
     */
    public function __construct($format, $dateTimeString = '')
    {
        $this->setFormat($format);

        if ($dateTimeString) {
            $this->setDateTime(new \DateTime($dateTimeString));
        } else {
            $this->setDateTime(new \DateTime());
        }
    }

    /**
     * @return int
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param int $format
     *
     * @throws APIException
     */
    public function setFormat($format)
    {
        if ($format != self::FORMAT_DATE_TIME &&
            $format != self::FORMAT_DATE &&
            $format != self::FORMAT_DATE_TIME_RFC
        ) {
            throw new APIException('Date format (' . $format . ') specified is not valid');
        }

        $this->format = (int) $format;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }
}
