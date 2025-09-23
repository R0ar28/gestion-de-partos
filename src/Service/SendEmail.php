<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class SendEmail
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail(string $correo, string $subject, string $htmlContents): void
    {
        $email = (new Email())
            ->from(new Address('notificaciones@canaldenuncias.com', 'Canal Denuncias'))
            ->to($correo)
            ->subject($subject)
            ->html($htmlContents);

        $this->mailer->send($email);
    }

    public function sendNotificacion(string $email, string $subject, string $messageText): void
    {
        $this->sendEmail($email, $subject, $messageText);
    }

    public function sendGenericEmail(
        string $to, string $subject, ?string $recipientName,
        string $mainMessage, ?string $additionalInfo = null, ?string $link = null
    ): void {
        $greetingName = $recipientName ? htmlspecialchars($recipientName, ENT_QUOTES, 'UTF-8') : 'usuario/a';

        $linkHtml = '';
        if ($link) {
            $safeLink = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
            $linkHtml = sprintf(
                '<p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
                Puede revisar los detalles de la denuncia en el siguiente enlace:
            </p>
            <p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
                <a href="%s" style="color:#0b5394;">Ver denuncia</a>
            </p>',
                $safeLink
            );
        }

        $content = sprintf(
            '<p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
            Estimado/a <strong>%s</strong>,
            </p>
            <p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
                %s
            </p>
            %s
            %s
            <p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
                Agradecemos su confianza en nuestro sistema.
            </p>',
            $greetingName,
            htmlspecialchars($mainMessage, ENT_QUOTES, 'UTF-8'),
            $additionalInfo
                ? sprintf(
                '<p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
                    Información adicional:<br>
                    <em>%s</em>
                </p>',
                nl2br(htmlspecialchars($additionalInfo, ENT_QUOTES, 'UTF-8'))
            )
                : '',
            $linkHtml
        );

        $htmlContent = $this->twig->render('security/email.html.twig', [
            'title' => $subject,
            'messages' => $content,
        ]);

        $this->sendEmail($to, $subject, $htmlContent);
    }


    public function sendGenericEmailUser(
        string $to,
        string $subject,
        ?string $recipientName,
        string $mainMessage
    ): void {

        $greetingName = $recipientName
            ? htmlspecialchars($recipientName, ENT_QUOTES, 'UTF-8')
            : 'usuario/a';

        $content = sprintf(
            '<p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
            Estimado/a <strong>%s</strong>,
        </p>
        <p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
            %s
        </p>
        <p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
            Agradecemos su confianza en nuestro sistema.
        </p>',
            $greetingName,
            nl2br(htmlspecialchars($mainMessage, ENT_QUOTES, 'UTF-8'))
        );

        $htmlBody = $this->twig->render('security/email.html.twig', [
            'title'    => $subject,
            'messages' => $content,
        ]);

        $this->sendEmail($to, $subject, $htmlBody);
    }


}
