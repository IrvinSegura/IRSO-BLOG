<?php

namespace App\Emails;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * What one email says, before it gets its HTML and plain-text form. Every value is plain text, including whatever
 * users wrote such as names and comments: resources/views/emails/layout.blade.php escapes it.
 */
final class EmailDraft
{
    public const HTML_VIEW = 'emails.layout';

    public const TEXT_VIEW = 'emails.layout-text';

    /** @var list<string> */
    public array $paragraphs = [];

    /** @var list<array{title: string, text: string}> */
    public array $steps = [];

    /** @var list<array{label: string, value: string}> */
    public array $details = [];

    /** @var list<string> */
    public array $footer = [];

    public ?string $quote = null;

    public ?string $quoteAuthor = null;

    public ?string $quoteInitials = null;

    public ?string $highlightLabel = null;

    public ?string $highlight = null;

    /** @var array{label: string, url: string}|null */
    public ?array $action = null;

    public ?string $linkHint = null;

    /** @var array{label: string, url: string}|null */
    public ?array $secondaryAction = null;

    public ?string $calloutTitle = null;

    public ?string $callout = null;

    public function __construct(
        public readonly EmailTone $tone,
        public readonly string $icon,
        public readonly string $kicker,
        public readonly string $brand,
        public readonly string $tagline,
        public readonly string $homeUrl,
        public readonly string $visitLabel,
        public readonly string $subject,
        public readonly string $preheader,
        public readonly string $title,
        public readonly string $greeting,
        public readonly string $signOff,
    ) {}

    public function detail(string $label, string $value): self
    {
        $this->details[] = ['label' => $label, 'value' => $value];

        return $this;
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view([self::HTML_VIEW, self::TEXT_VIEW], ['draft' => $this]);
    }

    public function html(): string
    {
        return view(self::HTML_VIEW, ['draft' => $this])->render();
    }

    public function text(): string
    {
        $blocks = [$this->greeting, ...$this->paragraphs];

        if ($this->quote !== null) {
            $blocks[] = $this->quoteAuthor.":\n\"".$this->quote.'"';
        }

        if ($this->steps !== []) {
            $blocks[] = implode("\n", array_map(
                fn (int $number, array $step) => ($number + 1).'. '.$step['title'].' '.$step['text'],
                array_keys($this->steps),
                $this->steps,
            ));
        }

        if ($this->highlight !== null) {
            $blocks[] = $this->highlightLabel.': '.$this->highlight;
        }

        if ($this->details !== []) {
            $blocks[] = implode("\n", array_map(fn (array $detail) => $detail['label'].': '.$detail['value'], $this->details));
        }

        if ($this->action !== null) {
            $blocks[] = $this->action['label'].":\n".$this->action['url'];
        }

        if ($this->callout !== null) {
            $blocks[] = trim($this->calloutTitle.' '.$this->callout);
        }

        if ($this->secondaryAction !== null) {
            $blocks[] = $this->secondaryAction['label'].":\n".$this->secondaryAction['url'];
        }

        $blocks[] = $this->signOff;
        $blocks[] = "--\n".$this->brand.' · '.$this->tagline."\n".$this->homeUrl."\n".implode("\n", $this->footer);

        return implode("\n\n", $blocks)."\n";
    }
}
