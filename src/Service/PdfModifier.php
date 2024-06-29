<?php
// src/Service/PdfModifier.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use setasign\Fpdi\Fpdi;

class PdfModifier
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function modifyPdf(string $sourceFilePath, string $outputFilePath): void
    {
        if (!file_exists($sourceFilePath)) {
            $this->logger->error("Source file does not exist: " . $sourceFilePath);
            throw new \Exception("Source file does not exist: $sourceFilePath");
        }

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($sourceFilePath);
        $this->logger->info("Number of pages in the source PDF: " . $pageCount);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Add any modifications here, e.g., adding text
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(10, 10);
            $pdf->Write(0, 'Modified Text');
            $this->logger->info("Modified page " . $pageNo);
        }

        $pdf->Output($outputFilePath, 'F');
        $this->logger->info("PDF saved to: " . $outputFilePath);

        if (!file_exists($outputFilePath)) {
            $this->logger->error("Output file was not created: " . $outputFilePath);
            throw new \Exception("Output file was not created: $outputFilePath");
        }
    }
}