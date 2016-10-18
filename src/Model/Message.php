<?php
namespace NYPL\Starter\Model;

use NYPL\Starter\APIException;

class Message
{
    const PAYLOAD_CONTENT_TYPE = "binary/avro";

    const ACTION_CREATE = 'create';
    const ACTION_READ = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    /**
     * @var string
     */
    public $action;

    /**
     * @var int
     */
    public $payloadSchemaId;

    /**
     * @var string
     */
    public $payloadContentType;

    /**
     * @var string
     */
    public $payload = '';

    protected $allowableActions = [self::ACTION_CREATE, self::ACTION_READ, self::ACTION_UPDATE, self::ACTION_DELETE];

    /**
     * @param string $action
     * @param int $schemaId
     * @param string $payloadBinary
     */
    public function __construct($action, $schemaId, $payloadBinary)
    {
        $this->setAction($action);

        $this->setPayloadSchemaId($schemaId);

        $this->setPayloadContentType(self::PAYLOAD_CONTENT_TYPE);

        $this->setPayload($payloadBinary);
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $action
     *
     * @throws APIException
     */
    public function setAction($action)
    {
        if (!in_array($action, $this->allowableActions)) {
            throw new APIException('Message action specified (' . $action . ') is not valid');
        }

        $this->action = $action;
    }

    /**
     * @return int
     */
    public function getPayloadSchemaId()
    {
        return $this->payloadSchemaId;
    }

    /**
     * @param int $payloadSchemaId
     */
    public function setPayloadSchemaId($payloadSchemaId)
    {
        $this->payloadSchemaId = (int) $payloadSchemaId;
    }

    /**
     * @return string
     */
    public function getPayloadContentType()
    {
        return $this->payloadContentType;
    }

    /**
     * @param string $payloadContentType
     */
    public function setPayloadContentType($payloadContentType)
    {
        $this->payloadContentType = $payloadContentType;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param string $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }
}
