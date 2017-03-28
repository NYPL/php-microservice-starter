<?php
namespace NYPL\Starter\Model\ModelTrait\SierraTrait;

use NYPL\Starter\APIException;
use NYPL\Starter\Filter;
use NYPL\Starter\Model;
use NYPL\Starter\ModelSet;

trait SierraReadTrait
{
    use Model\ModelTrait\SierraTrait;

    /**
     * @var string
     */
    private $body = '';

    /**
     * @param bool $ignoreNoRecord
     *
     * @return string
     */
    protected function getSierraResponse($ignoreNoRecord = false)
    {
        if ($this->getFilters()) {
            /**
             * @var Filter $filter
             */
            $filter = current($this->getFilters());

            return $this->sendRequest(
                $this->getSierraPath($filter->getId()),
                $ignoreNoRecord
            );
        }

        return $this->sendRequest(
            $this->getSierraPath(),
            $ignoreNoRecord
        );
    }

    /**
     * @param bool $ignoreNoRecord
     *
     * @return bool
     * @throws APIException
     */
    public function read($ignoreNoRecord = false)
    {
        $response = $this->getSierraResponse($ignoreNoRecord);

        $data = json_decode($response, true);

        if ($this instanceof ModelSet) {
            if (!isset($data['entries'])) {
                $data['entries'][] = $data;
            }

            foreach ($data['entries'] as $result) {
                /**
                 * @var Model\ModelTrait\TranslateTrait $model
                 */
                $model = clone $this->getBaseModel();
                $model->translate($result);

                $this->addModel($model);
            }

            return true;
        }

        $this->translate($data);

        return true;
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return 'GET';
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
