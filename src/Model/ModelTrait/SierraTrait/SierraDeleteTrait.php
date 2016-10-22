<?php
namespace NYPL\Starter\Model\ModelTrait\SierraTrait;

use NYPL\Starter\Model;

trait SierraDeleteTrait
{
    use Model\ModelTrait\SierraTrait;

    public function delete($id = '')
    {
        /**
         * @var Model\ModelTrait\TranslateTrait $this
         */
        $response = $this->getCurl($this->getSierraPath($id));

        return true;
    }

    public function applyCurlOptions($curl)
    {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
}
