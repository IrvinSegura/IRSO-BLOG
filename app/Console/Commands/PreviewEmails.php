<?php

namespace App\Console\Commands;

use App\Emails\EmailPreviews;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PreviewEmails extends Command
{
    protected $signature = 'emails:preview {--path= : Folder to write to (default: storage/app/email-previews)}';

    protected $description = 'Save every email design to a folder to open it in a browser; nothing is sent';

    public function handle(): int
    {
        $path = $this->option('path') ?: storage_path('app/email-previews');
        File::ensureDirectoryExists($path);

        $sections = '';

        foreach (EmailPreviews::all() as $reason => $draft) {
            $html = $draft->html();
            File::put("{$path}/{$reason}.html", $html);
            File::put("{$path}/{$reason}.txt", $draft->text());
            $sections .= $this->section($reason, $draft->subject, $html);
        }

        File::put("{$path}/index.html", $this->gallery($sections));

        $this->info("Vistas previas en {$path}/index.html");

        return self::SUCCESS;
    }

    /**
     * One email as computers and phones show it. The email goes inside the page so that index.html works on its own.
     */
    private function section(string $reason, string $subject, string $html): string
    {
        $frame = fn (int $width) => '<iframe title="'.e($reason)." {$width}px\" srcdoc=\"".e($html)."\" style=\"width:{$width}px\"></iframe>";

        return '<section><h2>'.e($subject).'</h2><p><a href="'.e($reason).'.html">HTML</a> · <a href="'.e($reason).'.txt">Texto plano</a></p><div class="frames">'
            .$frame(680).$frame(375).'</div></section>';
    }

    private function gallery(string $sections): string
    {
        return <<<HTML
            <!doctype html>
            <html lang="es">
            <meta charset="utf-8">
            <title>Correos de IRSO BLOG</title>
            <style>
              body { margin: 0; padding: 32px; background: #e5e7eb; font-family: system-ui, sans-serif; color: #111827; }
              h1 { margin: 0 0 24px; font-family: Georgia, serif; font-weight: 400; }
              h2 { margin: 0; font-size: 16px; }
              p { margin: 4px 0 12px; font-size: 13px; }
              section { margin-bottom: 40px; }
              .frames { display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start; }
              iframe { max-width: 100%; height: 1000px; border: 0; border-radius: 8px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.2); }
            </style>
            <h1>Correos de IRSO BLOG</h1>
            {$sections}
            </html>
            HTML;
    }
}
