<?php

namespace App\Http\Controllers;

use App\Models\FormPemeriksaan;
use App\Models\FormPerawatan;
use App\Models\PdfTemplate;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportPdfController extends Controller
{
    public function pemeriksaan(int $id)
    {
        $form = FormPemeriksaan::with(['teknisi', 'pengguna', 'asset', 'site', 'items', 'approvals.user'])
            ->findOrFail($id);

        $viewName = $this->resolveView('pemeriksaan');

        $pdf = Pdf::loadView($viewName, compact('form'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("form-pemeriksaan-" . str_replace('/', '-', $form->nomor_form) . ".pdf");
    }

    public function perawatan(int $id)
    {
        $form = FormPerawatan::with(['teknisi', 'pengguna', 'asset', 'site', 'items', 'approvals.user'])
            ->findOrFail($id);

        $viewName = $this->resolveView('perawatan');

        $pdf = Pdf::loadView($viewName, compact('form'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("form-perawatan-" . str_replace('/', '-', $form->nomor_form) . ".pdf");
    }

    private function resolveView(string $slug): string
    {
        $template = PdfTemplate::where('slug', $slug)->where('is_active', true)->first();

        if ($template) {
            $path = storage_path("app/templates/{$slug}.blade.php");
            $dir = dirname($path);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($path, $template->html_content);

            return "pdf-templates.{$slug}";
        }

        return "pdf.{$slug}";
    }
}
