<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiteCsvController extends Controller
{
    public function export(): StreamedResponse
    {
        $sites = Site::orderBy('id_site')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sites_export_' . now()->format('Y-m-d_His') . '.csv"',
        ];

        $callback = function () use ($sites) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['id_site', 'site', 'buss', 'id_corp', 'country', 'provincy', 'city', 'address', 'url_maps']);

            foreach ($sites as $site) {
                fputcsv($file, [
                    $site->id_site,
                    $site->site,
                    $site->buss ?? '',
                    $site->id_corp ?? '',
                    $site->country ?? '',
                    $site->provincy ?? '',
                    $site->city ?? '',
                    $site->address ?? '',
                    $site->url_maps ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function template(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_sites.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['id_site', 'site', 'buss', 'id_corp', 'country', 'provincy', 'city', 'address', 'url_maps']);

            fputcsv($file, [
                'A01',
                'Taman Anggrek Residence',
                'A',
                'AMA',
                'Indonesia',
                'DKI Jakarta',
                'Jakarta Barat',
                'Jl. Letjen S. Parman No.1, Taman Anggrek, Jakarta Barat',
                '',
            ]);

            fputcsv($file, [
                'B02',
                'Green Garden Office',
                'B',
                'AMG',
                'Indonesia',
                'DKI Jakarta',
                'Jakarta Barat',
                'Jl. Green Garden Raya No. 8, Kedoya, Jakarta Barat',
                '',
            ]);

            fputcsv($file, [
                'C03',
                'Sunter Logistics Hub',
                'C',
                'AMS',
                'Indonesia',
                'DKI Jakarta',
                'Jakarta Utara',
                'Jl. Sunter Permai Raya Blok A No. 12, Sunter, Jakarta Utara',
                '',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
