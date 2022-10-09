<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SessionRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FeedbackSheetExcelExportController extends AbstractController
{
    /**
     * @Route("/export/feedback_sheet.xls", name="export_feedback_sheet_xls", methods={"GET"})
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    public function generateSheet(SessionRepository $sessionRepository)
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $sessions = $sessionRepository->findFullyAccepted(false);

        $spreadsheet = new Spreadsheet();

        $lastSeenDate = null;
        $sheet = null;
        $row = null;

        foreach ($sessions as $session) {
            $formattedDate = $session->getStart()->format('d.m.');

            if ($sheet === null || $lastSeenDate !== $formattedDate) {
                $sheet = $this->createSheet($spreadsheet, $session->getStart());
                $lastSeenDate = $formattedDate;
                $row = 2;
            }

            $sheet->setCellValue('A' . $row, $formattedDate);
            $sheet->setCellValue('B' . $row, $session->getStart()->format('H:i'));

            if ($session->getStop() !== null) {
                $sheet->setCellValue('C' . $row, $session->getStop()->format('H:i'));
            }

            $sheet->setCellValue(
                'D' . $row,
                $session
                    ->getOrganization()
                    ->getAcceptedOrganizationDetails()
                    ->getContactName()
            );
            $sheet->setCellValue(
                'E' . $row,
                $session
                    ->getOrganization()
                    ->getOwner()
                    ->getEmail()
            );
            $sheet->setCellValue('G' . $row, $session->getAcceptedDetails()->getTitle());
            $sheet->setCellValue(
                'H' . $row,
                $session
                    ->getOrganization()
                    ->getAcceptedOrganizationDetails()
                    ->getTitle()
            );
            $sheet->setCellValue('I' . $row, $session->getAcceptedDetails()->getOnlineOnly() ? 'online' : 'live');

            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="feedback_sheet.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    private function createSheet(Spreadsheet $spreadsheet, \DateTimeInterface $start)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($start->format('l, d.m.'));

        $sheet->setCellValue('A1', 'Datum');
        $sheet->setCellValue('B1', 'Uhrzeit von');
        $sheet->setCellValue('C1', 'Uhrzeit bis');
        $sheet->setCellValue('D1', 'Ansprechpartner');
        $sheet->setCellValue('E1', 'E-Mail');
        $sheet->setCellValue('F1', 'angeschrieben');
        $sheet->setCellValue('G1', 'Titel');
        $sheet->setCellValue('H1', 'Veranstalter');
        $sheet->setCellValue('I1', 'Live/Virtuell');
        $sheet->setCellValue('J1', 'Angemeldet');
        $sheet->setCellValue('K1', 'Tatsächlich da');
        $sheet->setCellValue('L1', 'live');
        $sheet->setCellValue('M1', 'online');
        $sheet->setCellValue('N1', 'Feedback');

        return $sheet;
    }
}
