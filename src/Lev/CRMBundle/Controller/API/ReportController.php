<?php

namespace App\Lev\CRMBundle\Controller\API;

use App\Lev\APIBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Lev\CRMBundle\Service\ReportService;
use Swagger\Annotations as SWG;

class ReportController extends AbstractController
{

    /**
     * @var ReportService
     */
    protected $reportService;

    /**
     * @return ReportService
     */
    protected function getReportService()
    {
        if (null === $this->reportService) {
            $this->reportService = $this->get('lev_crm.service.report');
        }
        return $this->reportService;
    }

    /**
     * Return the user office
     *
     * @return \Lev\CRMBundle\Entity\Office|null
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
     * @Get("/report/marketer", name="report_marketer")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Report")
     * @SWG\Response(
     *     response=200,
     *     description="Get report of marketer")
     */
    public function marketer(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole(array('ADMIN','FINMAN', 'MKTMAN', 'MKTREP'), 'You don\'t have staff role access to this page');

      $user = (
                $this->getUser()->hasStaffRole('ADMIN')
                || $this->getUser()->hasStaffRole('FINMAN')
                || $this->getUser()->hasStaffRole('MKTMAN')
            )
            ? null : $this->getUser();
      $data = $this->getReportService()->marketerReport($request, $this->getOffice($request), $user);

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/report/marketerpayroll", name="report_marketer_payroll")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Report")
     * @SWG\Response(
     *     response=200,
     *     description="Get report of makerter paroll")
     */
    public function marketerPayroll(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');
      $this->denyAccessUnlessHasStaffrole(array('ADMIN','FINMAN', 'MKTMAN', 'MKTREP'), 'You don\'t have staff role access to this page');
      $user = (
                $this->getUser()->hasStaffRole('ADMIN')
                || $this->getUser()->hasStaffRole('FINMAN')
                || $this->getUser()->hasStaffRole('MKTMAN')
            )
            ? null : $this->getUser();
      $data = $this->getReportService()->marketerPayrollReport($request, $this->getOffice($request), $user);

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/report/salescommissions", name="report_sales_commissions")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Report")
     * @SWG\Response(
     *     response=200,
     *     description="Get report of sales commission")
     */
    public function salesCommissions(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');

      $user = (
                $this->getUser()->hasStaffRole('ADMIN')
                || $this->getUser()->hasStaffRole('FINMAN')
                || $this->getUser()->hasStaffRole('SALESMAN')
            )
            ? null : $this->getUser();
      $data = $this->getReportService()->salesCommissionsReport($request, $this->getOffice($request), $user);

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Get("/report/sales", name="report_sales")
     * @param Request $request
     * @return Response
     * @SWG\Tag(name="Report")
     * @SWG\Response(
     *     response=200,
     *     description="Get report of sales")
     */
    public function sales(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'You don\'t have access to this page');

      $user = (
                $this->getUser()->hasStaffRole('ADMIN')
                || $this->getUser()->hasStaffRole('FINMAN')
                || $this->getUser()->hasStaffRole('SALESMAN')
            )
            ? null : $this->getUser();
      $data = $this->getReportService()->salesReport($request, $this->getOffice($request), $user);

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }



}
