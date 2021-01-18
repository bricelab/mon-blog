<?php


namespace App\Service;


use App\Entity\Member;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;

class MailerService
{
    private MailerInterface $mailer;
    private string $fromEmail;
    private string $displayName;

    /**
     * MailerService constructor.
     * @param MailerInterface $mailer
     * @param string $fromEmail
     * @param string $displayName
     */
    public function __construct(MailerInterface $mailer, string $fromEmail, string $displayName)
    {
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
        $this->displayName = $displayName;
    }

    public function sendEmailConfirmation(UserInterface $user, VerifyEmailSignatureComponents $signatureComponents): void
    {
        /** @var Member $user */
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->displayName))
            ->to($user->getEmail())
            ->subject('Confirmation de votre adresse mail')
            ->htmlTemplate('registration/confirmation_email.html.twig')
        ;

        $context = $email->getContext();
        $context['pseudo'] = $user->getPseudo();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    public function sendWelcomeEmail(Member $user): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->displayName))
            ->to($user->getEmail())
            ->subject('Bienvenue sur BriceLab Blog')
            ->htmlTemplate('registration/welcome.html.twig')
        ;

        $context = $email->getContext();

        $email->context($context);
        $context['pseudo'] = $user->getPseudo();

        $this->mailer->send($email);
    }
}
