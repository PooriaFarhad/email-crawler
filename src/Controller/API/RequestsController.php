<?php

namespace App\Controller\API;

use App\CrawlerService\RequestManager;
use App\Repository\EmailRepository;
use App\Repository\RequestRepository;
use App\Lib\Pager;
use App\RequestValidation\StoreRequestValidation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class RequestsController extends APIAbstractController
{
    private $repo;
    private $requestManager;

    public function __construct(RequestRepository $requestRepository, RequestManager $requestManager)
    {
        $this->repo = $requestRepository;
        $this->requestManager = $requestManager;
    }

    /**
     * @Route("/api/requests", methods={"GET"})
     */
    public function index(Request $request)
    {
        $pager = new Pager($request->query->get('page'));
        return $this->getResult($this->repo->findAllPaginated($pager->getLimit(), $pager->getOffset()), $pager);
    }

    /**
     * @Route("/api/requests/{id}", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(int $id)
    {
        return $this->getResult($this->repo->findByIdAsArray($id));
    }

    /**
     * @Route("/api/requests", methods={"POST"})
     */
    public function store(Request $request, StoreRequestValidation $storeRequestValidation)
    {
        try {
            $request = $this->requestManager->createNewRequest($storeRequestValidation->validate($request));
            return $this->getResult($request);
        } catch (HttpException $exception) {
            return $this->json($exception->getMessage(), $exception->getStatusCode());
        }
    }

    /**
     * @Route("/api/requests/{id}/emails", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getEmails(int $id, Request $request, EmailRepository $emailRepository)
    {
        $pager = new Pager($request->query->get('page'));
        $emails = $emailRepository->findPaginatedByRequest($id, $pager->getLimit(), $pager->getOffset());
        return $this->getResult($emails, $pager);
    }
}