<?php
namespace NYPL\ServiceSample\Controller;

use NYPL\Starter\Controller;
use NYPL\Starter\Filter;
use NYPL\ServiceSample\Model\DataModel\BaseBib\Bib;
use NYPL\ServiceSample\Model\DataModel\BaseBib\NewBib;
use NYPL\ServiceSample\Model\Response\SuccessResponse\BibsResponse;
use NYPL\ServiceSample\Model\Response\SuccessResponse\BibResponse;
use NYPL\Starter\ModelSet;
use NYPL\Starter\Model\Source;

final class BibController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/v0.1/bibs",
     *     summary="Create a new Bib",
     *     tags={"bibs"},
     *     operationId="createBib",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="NewBib",
     *         in="body",
     *         description="",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/NewBib"),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/BibResponse")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Error",
     *         @SWG\Schema(
     *            type="array",
     *            @SWG\Items(ref="#/definitions/ErrorResponse")
     *         ),
     *     ),
     *     security={
     *         {
     *             "api_auth": {"openid api"}
     *         }
     *     }
     * )
     */
    public function createBib()
    {
        $source = new Source($this->getRequest()->getParsedBody());
        $source->create();

        $newBib = new NewBib($this->getRequest()->getParsedBody(), false, true);

        $bib = new Bib();
        $bib->translateFromNewModel($newBib);
        $bib->setId($this->generateId($source));
        $bib->create(true);

        return $this->getResponse()->withJson(new BibResponse($bib));
    }

    /**
     * @SWG\Get(
     *     path="/v0.1/bibs",
     *     summary="Get a list of Bibs",
     *     tags={"bibs"},
     *     operationId="getBibs",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="offset",
     *         in="query",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/BibsResponse")
    *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Error",
     *         @SWG\Schema(
     *            type="array",
     *            @SWG\Items(ref="#/definitions/ErrorResponse")
     *         ),
     *     ),
     *     security={
     *         {
     *             "api_auth": {"openid api"}
     *         }
     *     }
     * )
     */
    public function getBibs()
    {
        return $this->getDefaultReadResponse(
            new ModelSet(new Bib()), new BibsResponse()
        );
    }

    /**
     * @SWG\Get(
     *     path="/v0.1/bibs/{id}",
     *     summary="Get a Bib",
     *     tags={"bibs"},
     *     operationId="getBib",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of Bib",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *         format="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/BibResponse")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Error",
     *         @SWG\Schema(
     *            type="array",
     *            @SWG\Items(ref="#/definitions/ErrorResponse")
     *         ),
     *     ),
     *     security={
     *         {
     *             "api_auth": {"openid api"}
     *         }
     *     }
     * )
     */
    public function getBib($id)
    {
        return $this->getDefaultReadResponse(new Bib(), new BibResponse(), new Filter(null, null, false, $id));
    }
}
