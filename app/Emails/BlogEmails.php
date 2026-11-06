<?php

namespace App\Emails;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * One design per reason the blog writes to someone. The wording is in lang/es/emails.php under the name of the
 * reason; it is always Spanish because the whole application is, whatever APP_LOCALE says.
 *
 * The icons are typographic marks instead of emoji, which every system draws differently and some barely show.
 */
final class BlogEmails
{
    private const LOCALE = 'es';

    public static function resetPassword(?string $name, string $url, int $minutes): EmailDraft
    {
        $draft = self::start('reset_password', EmailTone::Warning, '•••', $name);
        $draft->detail(self::text('label.link_expires'), self::text('label.minutes', ['minutes' => $minutes]));
        $draft->action = ['label' => self::text('reset_password.action'), 'url' => $url];
        $draft->linkHint = self::text('link_hint');

        return $draft;
    }

    public static function socialLoginCode(string $code, int $minutes): EmailDraft
    {
        $draft = self::start('social_login_code', EmailTone::Info, '#', null, ['code' => $code], 'footer.requested');
        $draft->highlightLabel = self::text('social_login_code.highlight');
        $draft->highlight = $code;
        $draft->detail(self::text('label.code_expires'), self::text('label.minutes', ['minutes' => $minutes]));

        return $draft;
    }

    public static function welcome(?string $name, string $email): EmailDraft
    {
        $draft = self::start('welcome', EmailTone::Success, 'Aa', $name);
        $draft->steps = trans('emails.welcome.steps', [], self::LOCALE);
        $draft->detail(self::text('label.account'), $email);
        $draft->action = ['label' => self::text('welcome.action'), 'url' => route('blog.index')];
        $draft->secondaryAction = ['label' => self::text('welcome.secondary'), 'url' => route('admin.publications.create')];

        return $draft;
    }

    public static function passwordChanged(?string $name, string $email, CarbonInterface $at): EmailDraft
    {
        $draft = self::start('password_changed', EmailTone::Warning, '✓', $name);
        $draft->detail(self::text('label.account'), $email);
        $draft->detail(self::text('label.date'), self::dateTime($at));
        $draft->action = ['label' => self::text('password_changed.action'), 'url' => route('profile.show')];
        $draft->secondaryAction = ['label' => self::text('password_changed.secondary'), 'url' => route('password.request')];

        return $draft;
    }

    public static function commentReceived(?string $name, string $publication, string $commenter, string $comment, CarbonInterface $at): EmailDraft
    {
        $draft = self::start('comment_received', EmailTone::Info, '“', $name, [
            'commenter' => Str::limit($commenter, 60),
            'publication' => Str::limit($publication, 80),
        ], 'footer.author');
        $draft->quote = $comment;
        $draft->quoteAuthor = Str::limit($commenter, 60);
        $draft->quoteInitials = self::initials($commenter);
        $draft->detail(self::text('label.publication'), Str::limit($publication, 80));
        $draft->detail(self::text('label.date'), self::dateTime($at));
        $draft->action = ['label' => self::text('comment_received.action'), 'url' => route('admin.publications.mine')];

        return $draft;
    }

    /**
     * Names are free text and registering doesn't prove the address is yours, so a full name would let anyone use the
     * form to deliver a sentence or a link to someone else's inbox from our address. Only a plain first word gets in.
     */
    public static function greeting(?string $name): string
    {
        $first = preg_split('/\s+/u', trim((string) $name))[0];

        return preg_match("/^\p{L}[\p{L}\p{M}'’-]{0,29}$/u", $first) === 1
            ? self::text('greeting', ['name' => $first])
            : self::text('greeting_anonymous');
    }

    /**
     * Up to two letters for the avatar, from the words of a name that start with one.
     */
    private static function initials(string $name): string
    {
        preg_match_all('/(?<![\p{L}\p{M}])\p{L}/u', $name, $letters);

        return mb_strtoupper(implode('', array_slice($letters[0], 0, 2))) ?: '·';
    }

    /**
     * @param  array<string, string>  $values
     */
    private static function start(string $reason, EmailTone $tone, string $icon, ?string $name, array $values = [], string $footerReason = 'footer.account'): EmailDraft
    {
        $draft = new EmailDraft(
            $tone,
            $icon,
            self::text($reason.'.kicker'),
            self::text('brand'),
            self::text('tagline'),
            route('blog.index'),
            self::text('footer.visit'),
            self::text($reason.'.subject', $values),
            self::text($reason.'.preheader', $values),
            self::text($reason.'.title', $values),
            self::greeting($name),
            self::text('sign_off'),
        );

        $draft->paragraphs = self::lines($reason.'.body', $values);
        $draft->calloutTitle = self::optional($reason.'.callout_title', $values);
        $draft->callout = self::optional($reason.'.callout', $values);
        $draft->footer = [self::text($footerReason), self::text('footer.security')];

        return $draft;
    }

    /**
     * @param  array<string, string|int>  $values
     */
    private static function text(string $key, array $values = []): string
    {
        return trans('emails.'.$key, $values, self::LOCALE);
    }

    /**
     * @param  array<string, string>  $values
     * @return list<string>
     */
    private static function lines(string $key, array $values): array
    {
        return trans('emails.'.$key, $values, self::LOCALE);
    }

    /**
     * @param  array<string, string>  $values
     */
    private static function optional(string $key, array $values): ?string
    {
        return Lang::has('emails.'.$key, self::LOCALE, false) ? self::text($key, $values) : null;
    }

    private static function dateTime(CarbonInterface $at): string
    {
        return $at->copy()->locale(self::LOCALE)->translatedFormat('j \d\e F \d\e Y, H:i T');
    }
}
