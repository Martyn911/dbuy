<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Datatables\DomainDatatable;

class DefaultController extends Controller
{
    /**
     * Lists all Domain entities.
     *
     * @param Request $request
     *
     * @Route("/", name="home_page")
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        /** @var DatatableInterface $datatable */
        $datatable = $this->get('sg_datatables.factory')->create(DomainDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->get('sg_datatables.response');
            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();

            $datatableQueryBuilder->useQueryCache(true);            // (1)
            $datatableQueryBuilder->useCountQueryCache(true);       // (2)
            $datatableQueryBuilder->useResultCache(true, 60);       // (3)
            $datatableQueryBuilder->useCountResultCache(true, 60);  // (4)

            return $responseService->getResponse();
        }

        return $this->render('default/index.html.twig', array(
            'datatable' => $datatable,
        ));
    }
}
