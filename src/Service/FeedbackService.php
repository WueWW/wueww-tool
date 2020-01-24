<?php

namespace App\Service;

use App\Entity\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FeedbackService
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function generatePdf(Session $session): string
    {
        $pdf = new \TCPDF();
        $pdf->SetCreator('Utes Helferlein (https://github.com/WueWW/wueww-tool/)');
        $pdf->SetTitle('Feedback QR-Code');

        $style = [
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1, // height of a single module in points
        ];

        $pdf->SetAutoPageBreak(false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        $pdf->Image(__DIR__ . '/../../assets/images/wueww-logo-2020.jpg', 10, 10, 100);

        $pdf->SetFont('dejavusans', 'B', 24);
        $pdf->SetXY(20, 50);
        $pdf->Cell(170, 0, 'Feedback', 0, 0, 'C');

        $pdf->SetFont('dejavusans', '', 16);
        $pdf->SetXY(20, 70);
        $pdf->Cell(170, 0, $session->getProposedDetails()->getTitle(), 0, 0, 'C');

        $url = $this->router->generate(
            'feedback_post',
            ['id' => $session->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $pdf->write2DBarcode($url, 'QRCODE,Q', 45, 88, 120, 120, $style, 'N');

        $pdf->SetFont('dejavusans', '', 16);
        $pdf->SetXY(20, 230);
        $pdf->MultiCell(
            170,
            0,
            'Mit deinem Feedback hilfst du dem Veranstalter und uns die Web Week noch besser zu machen.',
            0,
            'C'
        );
        $pdf->SetXY(20, 260);
        $pdf->Cell(170, 0, 'Vielen Dank :)', 0, 0, 'C');

        return $pdf->Output('feedback.pdf', 'S');
    }
}
