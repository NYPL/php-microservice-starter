<?php
namespace NYPL\Starter;

use NYPL\Starter\Filter\QueryFilter;
use NYPL\Starter\Model\Source;
use NYPL\Starter\Model\IdentityHeader;
use NYPL\Starter\Model\Response\SuccessResponse;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Stream;

abstract class Controller
{
    const ACCEPT_HEADER = 'Accept';

    const CONTENT_TYPE_HEADER = 'Content-type';

    const TEXT_CONTENT_TYPE = 'text/plain';

    const HTML_CONTENT_TYPE = 'text/html';

    const JSON_CONTENT_TYPE = 'application/json';

    const IDENTITY_HEADER = 'X-NYPL-Identity';

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var string
     */
    public $contentType = '';

    /**
     * @var IdentityHeader
     */
    public $identityHeader;

    /**
     * @param Request $request
     * @param Response $response
     * @param int $cacheSeconds
     */
    public function __construct(Request $request, Response $response, int $cacheSeconds = 0)
    {
        $this->setRequest($request);
        $this->setResponse($response);

        $this->addCacheHeader($cacheSeconds);

        $this->initializeContentType();

        $this->initializeIdentityHeader();
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param $data
     * @return MessageInterface
     */
    public function getJsonResponse($data): MessageInterface
    {
        $json = json_encode($data);
        $streamBody = fopen('data://text/plain,' . urlencode($json), 'r');
        return $this->getResponse()->withBody(new Stream($streamBody));
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        if ($contentType) {
            $this->setResponse(
                $this->getResponse()->withHeader(self::CONTENT_TYPE_HEADER, $contentType)
            );
        }

        $this->contentType = $contentType;
    }

    /**
     * @return IdentityHeader
     */
    public function getIdentityHeader()
    {
        return $this->identityHeader;
    }

    /**
     * @param IdentityHeader $identityHeader
     */
    public function setIdentityHeader(IdentityHeader $identityHeader)
    {
        $this->identityHeader = $identityHeader;
    }

    /**
     * @return string
     */
    public function determineContentType(): string
    {
        $acceptedContentTypes = $this->getRequest()->getHeaderLine(self::ACCEPT_HEADER);

        if (strpos($acceptedContentTypes, self::HTML_CONTENT_TYPE) !== false) {
            return self::HTML_CONTENT_TYPE;
        }

        if (strpos($acceptedContentTypes, self::TEXT_CONTENT_TYPE) !== false) {
            return self::TEXT_CONTENT_TYPE;
        }

        if (strpos($acceptedContentTypes, self::JSON_CONTENT_TYPE) !== false) {
            return self::JSON_CONTENT_TYPE;
        }

        return self::JSON_CONTENT_TYPE;
    }

    public function initializeContentType()
    {
        $this->setContentType($this->determineContentType());

        $this->setResponse(
            $this->getResponse()->withHeader(self::CONTENT_TYPE_HEADER, $this->getContentType())
        );
    }

    /**
     * @param callable $bufferedFunction
     *
     * @return string
     */
    public function bufferOutput(callable $bufferedFunction): string
    {
        ob_start();
        $bufferedFunction();
        $output = ob_get_contents();
        ob_clean();

        return $output;
    }

    /**
     * @return bool
     */
    public function initializeIdentityHeader(): bool
    {
        if ($this->getRequest()->hasHeader(self::IDENTITY_HEADER)) {
            $this->setIdentityHeader(new IdentityHeader(
                $this->getRequest()->getHeaderLine(self::IDENTITY_HEADER)
            ));

            return true;
        }

        $this->setIdentityHeader(new IdentityHeader());

        return false;
    }


    /**
     * @param Source $source
     *
     * @return string
     */
    protected function generateId(Source $source)
    {
        if ($source->getSourceId()) {
            return $source->getSourceCode() . $source->getSourceId();
        }

        return $source->getSourceCode() . $source->getId();
    }

    /**
     * @param ModelSet $model
     * @param string $queryParameterName
     *
     * @return bool
     */
    protected function addQueryFilter(ModelSet $model, string $queryParameterName = ''): bool
    {
        if ($this->getQueryParam($queryParameterName)) {
            $filter = new QueryFilter(
                $queryParameterName,
                $this->getQueryParam($queryParameterName)
            );
            $model->addFilter($filter);

            return true;
        }

        return false;
    }

    /**
     * @param Model $model
     * @param SuccessResponse $response
     * @param Filter|null $filter
     * @param array $queryParameters
     *
     * @return MessageInterface
     * @throws APIException
     */
    protected function getDefaultReadResponse(
        Model $model,
        SuccessResponse $response,
        Filter $filter = null,
        array $queryParameters = []
    ): MessageInterface {
        if ($model instanceof ModelSet) {
            $model->setOffset($this->getQueryParam('offset'));

            $model->setLimit($this->getQueryParam('limit'));

            $includeTotalCount = ($this->getQueryParam('includeTotalCount') === 'true');

            if ($includeTotalCount) {
                $model->setIncludeTotalCount($includeTotalCount);
            }

            if ($filter) {
                $model->addFilter($filter);
            }

            if ($queryParameters) {
                foreach ($queryParameters as $queryParameterName) {
                    $this->addQueryFilter($model, $queryParameterName);
                }
            }

            $model->read();

            $response->initializeResponse($model);
        } else {
            if ($filter) {
                if ($filter->getId()) {
                    $filter->setFilterColumn('id');
                    $filter->setFilterValue($filter->getId());
                }

                $model->addFilter($filter);
            }

            $model->read();

            $response->initializeResponse($model);
        }

        return $this->getJsonResponse($response);
    }

    /**
     * @param int $numberSeconds
     *
     * @return bool
     */
    public function addCacheHeader(int $numberSeconds = 0): bool
    {
        if ($numberSeconds && $this->getRequest()->getMethod() == 'GET') {
            $this->setResponse(
                $this->getResponse()->withHeader(
                    "Cache-Control",
                    "public, max-age=" . $numberSeconds
                )
            );

            return true;
        }

        return true;
    }

    /**
     * @param string $patronId
     *
     * @return bool
     */
    public function isAllowed(string $patronId = ''): bool
    {
        if (!$this->getIdentityHeader()->isExists()) {
            return true;
        }

        if ($this->getIdentityHeader()->getSubject() == $patronId) {
            return true;
        }

        return false;
    }

    /**
     * @param array $allowedScopes
     * @return true|void
     * @throws APIException
     */
    public function checkScopes(array $allowedScopes = [])
    {
        if (!$this->getIdentityHeader()->isExists()) {
            return true;
        }

        if (array_intersect($allowedScopes, $this->getIdentityHeader()->getScopes())) {
            return true;
        }

        $this->denyAccess(
            'Does not contain required scope (' . implode(', ', $allowedScopes) . ')'
        );
    }

    /**
     * @param string $message
     *
     * @throws APIException
     */
    public function denyAccess(string $message = '')
    {
        throw new APIException(
            'Insufficient access for endpoint: ' . $message,
            null,
            null,
            null,
            403
        );
    }

    /**
     * Get query parameters from request as array.
     *
     * @return null|array
     */
    public function getQueryParams(): ?array
    {
        parse_str($this->getRequest()->getUri()->getQuery(), $queryParameters);
        return $queryParameters;
    }

    /**
     * Get query parameter value from request.
     *
     * @param  string  $var
     * @return null|string
     */
    public function getQueryParam(string $var): ?string
    {
        $params = $this->getQueryParams();
        $val = null;
        if (isset($params[$var])) {
            $val = $params[$var];
        }
        return $val;
    }
}
