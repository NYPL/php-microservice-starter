<?php
namespace NYPL\Starter\Model\ModelTrait\SierraTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\Model;

trait SierraReadTrait
{
    use Model\ModelTrait\SierraTrait;

    public function read($id = '')
    {
        /**
         * @var Model\ModelTrait\TranslateTrait $this
         */
        $response = $this->getCurl($this->getSierraPath($id));

        $data = json_decode($response, true);

        if (isset($data['httpStatus'])) {
            throw new APIException($data['name'], $data);
        }

        $this->translate($data);

        return true;
    }

    public function applyCurlOptions($curl)
    {
    }
}
