<?php
namespace NYPL\Starter;

use NYPL\Starter\Filter\QueryFilter;
use NYPL\Starter\Model\Source;
use NYPL\Starter\Model\IdentityHeader;
use NYPL\Starter\Model\Response\SuccessResponse;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class Controller
{
    const ACCEPT_HEADER = 'Accept';

    const CONTENT_TYPE_HEADER = 'Content-type';

    const TEXT_CONTENT_TYPE = 'text/plain';

    const HTML_CONTENT_TYPE = 'text/html';

    const JSON_CONTENT_TYPE = 'application/json';

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response|ResponseInterface
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
     * @param Response|ResponseInterface $response
     * @param int $cacheSeconds
     */
    public function __construct(Request $request, Response $response, $cacheSeconds = 0)
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
    public function getRequest()
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
     * @return Response|ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response|ResponseInterface $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
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
     * @throws APIException
     */
    public function determineContentType()
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
    public function bufferOutput(callable $bufferedFunction)
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
    public function initializeIdentityHeader()
    {
        if ($this->getRequest()->hasHeader(Config::get('IDENTITY_HEADER'))) {
            $this->setIdentityHeader(new IdentityHeader(
                $this->getRequest()->getHeaderLine(Config::get('IDENTITY_HEADER'))
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
    protected function addQueryFilter(ModelSet $model, $queryParameterName = '')
    {
        if ($this->getRequest()->getQueryParam($queryParameterName)) {
            $filter = new QueryFilter(
                $queryParameterName,
                $this->getRequest()->getQueryParam($queryParameterName)
            );

            $model->addFilter($filter);

            return true;
        }
    }

    /**
     * @param Model $model
     * @param SuccessResponse $response
     * @param Filter|null $filter
     * @param array $queryParameters
     *
     * @return Response
     */
    protected function getDefaultReadResponse(
        Model $model,
        SuccessResponse $response,
        Filter $filter = null,
        array $queryParameters = []
    ) {
        if ($model instanceof ModelSet) {
            if (!$model->isNoDefaultSorting()) {
                if (!$model->getOrderBy()) {
                    $model->setOrderBy('updatedDate');
                }

                if (!$model->getOrderDirection()) {
                    $model->setOrderDirection('DESC');
                }
            }

            $model->setOffset($this->getRequest()->getParam('offset'));

            $model->setLimit($this->getRequest()->getParam('limit'));

            if ($filter) {
                $model->addFilter($filter);
            }

            if ($queryParameters) {
                foreach ($queryParameters as $queryParameterName) {
                    $this->addQueryFilter($model, $queryParameterName);
                }
            }

            $model->read();

            $response->initializeResponse($model->getData());
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

        return $this->getResponse()->withJson($response);
    }

    /**
     * @param int $numberSeconds
     *
     * @return bool
     */
    public function addCacheHeader($numberSeconds = 0)
    {
        if ($numberSeconds && $this->getRequest()->isGet()) {
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
     * @throws APIException
     */
    public function checkAccess($patronId = '')
    {
        if (!$this->getIdentityHeader()->isExists()) {
            return true;
        }

        if ($this->getIdentityHeader()->getSubject() == $patronId) {
            return true;
        }

        throw new APIException('Insufficient access for endpoint', null, null, null, 403);
    }
}
