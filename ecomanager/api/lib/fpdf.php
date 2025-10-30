\<?php
// FPDF super simples só pra esse projeto
// NÃO é o oficial completo, mas já aceita SetXY, Cell, Output

class FPDF
{
    protected array $pages = [];
    protected int $page = 0;
    protected float $x = 10;
    protected float $y = 10;

    public function __construct()
    {
    }

    public function AddPage(): void
    {
        $this->page++;
        $this->pages[$this->page] = '';
        // posição inicial
        $this->x = 20;
        $this->y = 780; // vamos trabalhar de cima pra baixo
    }

    public function SetFont($family, $style = '', $size = 0): void
    {
        // neste mock não vamos realmente mudar fonte
    }

    public function SetXY(float $x, float $y): void
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function Ln(float $h = 6): void
    {
        $this->y -= $h;
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = ''): void
    {
        // vamos escrever na posição atual
        // PDF usa 72dpi -> 1pt = 1, mas nossa página está 612x792
        $txt = $this->escape($txt);
        $stream = "BT /F1 12 Tf {$this->x} {$this->y} Td ({$txt}) Tj ET\n";
        $this->pages[$this->page] .= $stream;

        if ($ln > 0) {
            $this->Ln($h > 0 ? $h : 6);
        } else {
            // se não quebra linha, anda um pouco na horizontal
            $this->x += ($w > 0 ? $w : 40);
        }
    }

    public function Output($dest = 'I', $name = 'doc.pdf')
    {
        // monta PDF básico
        $content = $this->pages[1] ?? '';

        $pdf  = "%PDF-1.3\n";
        $pdf .= "1 0 obj <</Type /Catalog /Pages 2 0 R>> endobj\n";
        $pdf .= "2 0 obj <</Type /Pages /Count 1 /Kids [3 0 R]>> endobj\n";
        $pdf .= "3 0 obj <</Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources <</Font <</F1 5 0 R>>>>>> endobj\n";
        $pdf .= "4 0 obj <</Length ".strlen($content).">> stream\n";
        $pdf .= $content;
        $pdf .= "endstream endobj\n";
        $pdf .= "5 0 obj <</Type /Font /Subtype /Type1 /BaseFont /Helvetica>> endobj\n";
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        $pdf .= "trailer <</Size 6 /Root 1 0 R>>\nstartxref\n0\n%%EOF";

        if (!headers_sent()) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="'.$name.'"');
        }

        echo $pdf;
        exit;
    }

    protected function escape(string $s): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }
}
