<?php
namespace NYPL\Starter\Model\ModelTrait\SierraTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\Model;

trait SierraCreateTrait
{
    use Model\ModelTrait\SierraTrait;

    /**
     * @var string
     */
    private $body = '';

    /**
     * @param bool $ignoreNoRecord
     *
     * @throws APIException
     * @return string
     */
    protected function getSierraResponse($ignoreNoRecord = false)
    {
        if (!$this->getBody()) {
            $this->setBody(json_encode($this));
        }

        return $this->sendRequest(
            $this->getSierraPath(),
            $ignoreNoRecord,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * @param bool $ignoreNoRecord
     *
     * @return array
     */
    public function create($ignoreNoRecord = false)
    {
        $response = $this->getSierraResponse($ignoreNoRecord);

        return json_decode($response, true);
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body = '')
    {
        $this->body = $body;
    }
}
