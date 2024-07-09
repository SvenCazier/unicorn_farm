<?php

// src/Service/EmailService.php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Unicorn;
use RuntimeException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Throwable;
use Twig\Environment;

/**
 * Service class for handling email-related operations.
 */
class EmailService
{
    /**
     * Constructor.
     *
     * @param string $senderEmail The sender's email address.
     * @param MailerInterface $mailer The mailer interface.
     * @param Environment $twig The Twig environment.
     */
    public function __construct(
        private string $senderEmail,
        private MailerInterface $mailer,
        private Environment $twig
    ) {
    }

    /**
     * Sends a purchase email with the posts related to the purchased unicorn.
     *
     * @param string $recipientEmail The recipient's email address.
     * @param Unicorn $unicorn The purchased unicorn entity.
     *
     * @throws RuntimeException if the email fails to send.
     */
    public function sendPurchaseEmail(string $recipientEmail, Unicorn $unicorn)
    {
        try {
            $unicornName = $unicorn->getName();
            $messages = $unicorn->getMessages();

            $htmlContent = $this->twig->render("email/purchaseEmailTemplate.twig", [
                "unicornName" => $unicornName,
                "messages" => $messages,
            ]);

            $textContent = "Listing of all posts related to {$unicornName}:\n\n";
            foreach ($messages as $message) {
                $textContent .= "Author: {$message->getAuthor()}\n";
                $textContent .= "Post: {$message->getMessage()}\n";
                $textContent .= "Date: {$message->getCreatedAtValue()->format('Y-m-d')}\n";
                $textContent .= "Time: {$message->getCreatedAtValue()->format('H:i')} \n\n";
            }

            $email = (new Email())
                ->from($this->senderEmail)
                ->to($recipientEmail)
                ->subject("A listing of all posts related to your purchased unicorn")
                ->html($htmlContent)
                ->text($textContent);

            $this->mailer->send($email);
        } catch (Throwable $e) {
            throw new RuntimeException("Failed to send email", 0, $e);
        }
    }
}
