<?php

namespace App\Lev\CRMBundle\Controller\API;

use App\Lev\APIBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Lev\CRMBundle\Service\DashboardService;
use Swagger\Annotations as SWG;

class DashboardController extends AbstractController
{

    /**
     * @var DashboardService
     */
    protected $dashboardService;

    /**
     * @return DashboardService
     */
    protected function getDashboardService()
    {
        if (null === $this->dashboardService) {
            $this->dashboardService = $this->get('lev_crm.service.dashboard');
        }
        return $this->dashboardService;
    }


    /**
     * Return the user office
     *
     * @return \App\Lev\CRMBundle\Entity\Office|null
     */
    protected function getOffice(Request $request)
    {
        return $request->get('office', false)
            ? $request->get('office', false)
            : null === $this->getUser()
                ? null
                : $this->getUser()->getOffice();
    }

    /**
     * @Get("/dashboard/opperations-man", name="dashboard_opperations_man")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of opration manager")
     */
    public function loadOpperationsManager(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('OPMAN', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcOpperationsMan($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/contractor", name="dashboard_contractor")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of contractor")
     */
    public function loadContractor(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('CONTRACTOR', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcContractor($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/marketing-man", name="dashboard_marketing_man")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of marketing man")
     */
    public function loadMarketingMan(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('MKTMAN', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcMarketingMan($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/marketing-rep", name="dashboard_marketing_rep")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of marketing representatives")
     */
    public function loadMarketingRep(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('MKTREP', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcMarketingRep($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/sales-man", name="dashboard_sales_man")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of sales man")
     */
    public function loadSalesMan(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('SALESMAN', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcSalesMan($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/sales-rep", name="dashboard_sales_rep")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of sales representatives")
     */
    public function loadSalesRep(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('SALESREP', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcSalesRep($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/callcenter-rep", name="dashboard_callcenter_rep")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of call representatives")
     */
    public function loadCallcenterRep(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('CCREP', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcCallcenterRep($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/callcenter-man", name="dashboard_callcenter_man")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of call representatives")
     */
    public function loadCallcenterMan(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('CCMAN', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcCallcenterMan($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/project-man", name="dashboard_project_man")
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of project manager")
     * @param Request $request
     * @return Response
     */
    public function loadProjectMan(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('PROJMAN', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcProjectMan($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/finance-man", name="dashboard_finance_man")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of finance manager")
     */
    public function loadFinanceManager(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole('FINMAN', 'You don\'t have staff role access to this page');
      $data = $this->getDashboardService()->calcFinanceMan($request, $this->getOffice($request), $this->getUser());

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/dashboard/all", name="dashboard_all")
     * @SWG\Tag(name="Dashboard")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of representatives")
     */
    public function loadAll(Request $request)
    {
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
            , null
            , 'You don\'t have access to this page'
        );
        $data = array();
        if ($this->getUser()->hasStaffrole('OPMAN')) {
          $data['opperationsMan'] = $this->getDashboardService()->calcOpperationsMan($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('MKTMAN')) {
          $data['marketingMan'] = $this->getDashboardService()->calcMarketingMan($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('MKTREP')) {
          $data['marketingRep'] = $this->getDashboardService()->calcMarketingRep($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('CONTRACTOR')) {
          $data['contractor'] = $this->getDashboardService()->calcContractor($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('SALESMAN')) {
          $data['salesMan'] = $this->getDashboardService()->calcSalesMan($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('SALESREP')) {
          $data['salesRep'] = $this->getDashboardService()->calcSalesRep($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('CCREP')) {
          $data['callcenterRep'] = $this->getDashboardService()->calcCallcenterRep($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('CCMAN')) {
          $data['callcenterMan'] = $this->getDashboardService()->calcCallcenterMan($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('PROJMAN')) {
          $data['projectMan'] = $this->getDashboardService()->calcProjectMan($request, $this->getOffice($request), $this->getUser());
        }
        if ($this->getUser()->hasStaffrole('FINMAN')) {
          $data['financeMan'] = $this->getDashboardService()->calcFinanceMan($request, $this->getOffice($request), $this->getUser());
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

}
