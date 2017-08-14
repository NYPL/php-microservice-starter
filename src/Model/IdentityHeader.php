<?php
namespace NYPL\Starter\Model;

class IdentityHeader
{
    /**
     * @var bool
     */
    public $exists = false;

    /**
     * @var string
     */
    public $token = '';

    /**
     * @var array
     */
    public $scopes;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var array
     */
    public $identity;

    /**
     * @param string $identityHeader
     */
    public function __construct($identityHeader = '')
    {
        if ($identityHeader) {
            $this->initializeIdentityHeader($identityHeader);
        }
    }

    /**
     * @return array
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param array $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    /**
     * @param string $identityHeader
     */
    public function initializeIdentityHeader($identityHeader)
    {
        $decodedIdentityHeader = json_decode($identityHeader, true);

        $this->setToken($decodedIdentityHeader['token']);

        $this->setIdentity($decodedIdentityHeader['identity']);

        $this->setScopes($this->getIdentity()['scope']);

        $this->setSubject($this->getIdentity()['sub']);

        $this->setExists(true);
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param array|string $scopes
     */
    public function setScopes($scopes)
    {
        if (is_string($scopes)) {
            $scopes = explode(' ', trim($scopes));
        }

        $this->scopes = $scopes;
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

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return bool
     */
    public function isExists(): bool
    {
        return $this->exists;
    }

    /**
     * @param bool $exists
     */
    public function setExists(bool $exists)
    {
        $this->exists = $exists;
    }

    /**
     * @param string $scope
     *
     * @return bool
     */
    public function isAllowableScope($scope = '')
    {
        if (in_array($scope, $this->getScopes(), true)) {
            return true;
        }

        return false;
    }
}
