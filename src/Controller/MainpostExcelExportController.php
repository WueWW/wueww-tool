<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MainpostExcelExportController extends AbstractController
{
    /**
     * @Route("/export/mainpost.xls", name="export_mainpost_xls", methods={"GET"})
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findFullyAccepted(true);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Datum');
        $sheet->setCellValue('B1', 'Start');
        $sheet->setCellValue('C1', 'Ende');
        $sheet->setCellValue('D1', 'Veranstalter');
        $sheet->setCellValue('E1', 'Titel Veranstaltung');
        $sheet->setCellValue('F1', 'ausschließlich online');
        $sheet->setCellValue('G1', 'Location Name');
        $sheet->setCellValue('H1', 'Location Straße');
        $sheet->setCellValue('I1', 'Location PLZ');
        $sheet->setCellValue('J1', 'Location Ort');

        foreach (
            [
                'A' => 12,
                'D' => 24,
                'E' => 24,
                'G' => 24,
                'H' => 18,
                'J' => 18,
            ]
            as $column => $width
        ) {
            $spreadsheet
                ->getActiveSheet()
                ->getColumnDimension($column)
                ->setWidth($width);
        }

        $row = 2;
        foreach ($sessions as $session) {
            $sheet->setCellValue('A' . $row, $session->getStart()->format('d.m.Y'));
            $sheet->setCellValue('B' . $row, $session->getStart()->format('H:i'));

            if ($session->getStop() !== null) {
                $sheet->setCellValue('C' . $row, $session->getStop()->format('H:i'));
            }

            $sheet->setCellValue('D' . $row, $session->getOrganization()->getTitle());
            $sheet->setCellValue('E' . $row, $session->getAcceptedDetails()->getTitle());
            $sheet->setCellValue('F' . $row, $session->getAcceptedDetails()->getOnlineOnly() ? 'ja' : 'nein');

            if (!$session->getAcceptedDetails()->getOnlineOnly()) {
                $loc = $session->getAcceptedDetails()->getLocation();
                $sheet->setCellValue('G' . $row, $loc->getName());
                $sheet->setCellValue('H' . $row, $loc->getStreetNo());
                $sheet->setCellValue('I' . $row, $loc->getZipcode());
                $sheet->setCellValue('J' . $row, $loc->getCity());
            }

            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="MainpostExport.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
