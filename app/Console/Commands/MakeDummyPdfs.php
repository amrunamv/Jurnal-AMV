<?php

namespace App\Console\Commands;

use App\Models\Manuscript;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MakeDummyPdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dummy-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy PDF files for all manuscripts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating dummy PDFs for manuscripts...');

        $manuscripts = Manuscript::all();
        $count = 0;

        // Ensure directory exists
        if (!Storage::exists('manuscripts')) {
            Storage::makeDirectory('manuscripts');
        }

        foreach ($manuscripts as $manuscript) {
            $filename = $manuscript->uuid . '.pdf';
            $path = 'manuscripts/' . $filename;

            if (!Storage::exists($path)) {
                // Create a simple text file that mimics a PDF content for testing
                // Real browsers might reject this as invalid PDF, but for download testing it works.
                // For better simulation, we can add a valid PDF header.
                $content = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << >> /Contents 4 0 R >>\nendobj\n4 0 obj\n<< /Length 50 >>\nstream\nBT /F1 24 Tf 100 700 Td (Dummy PDF for " . $manuscript->title . ") Tj ET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000117 00000 n \n0000000224 00000 n \ntrailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n324\n%%EOF";
                
                Storage::put($path, $content);
                $this->line("Created PDF for: {$manuscript->title}");
                $count++;
            }
        }

        $this->info("Successfully generated {$count} dummy PDF files.");
    }
}
