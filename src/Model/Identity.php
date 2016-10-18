<?php
namespace NYPL\Starter\Model;

class Identity
{
    /**
     * @var array
     */
    public $scope;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var array
     */
    public $jwt;

    /**
     * @param string $identityHeader
     */
    public function __construct($identityHeader = '')
    {
        if ($identityHeader) {
            $this->initializeJwt($identityHeader);
        }
    }

    /**
     * @return array
     */
    public function getJwt()
    {
        return $this->jwt;
    }

    /**
     * @param array $jwt
     */
    public function setJwt($jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * @param string $identityHeader
     */
    public function initializeJwt($identityHeader)
    {
        $this->setJwt(json_decode($identityHeader, true));

        $this->setScope($this->getJwt()['scope']);

        $this->setSubject($this->getJwt()['sub']);
    }

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param array|string $scope
     */
    public function setScope($scope)
    {
        if (is_string($scope)) {
            $scope = explode(' ', trim($scope));
        }

        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
}
