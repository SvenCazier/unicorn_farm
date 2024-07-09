<?php

// src/State/PurchaseUnicornProcessor.php

declare(strict_types=1);

namespace App\State;

use \RuntimeException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Unicorn;
use App\Repository\MessageRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, ConflictHttpException, NotFoundHttpException};

/**
 * Processor for handling unicorn purchase operations.
 *
 * @implements ProcessorInterface<Unicorn, Unicorn|void>
 */
final class PurchaseUnicornProcessor implements ProcessorInterface
{
    /**
     * Constructor.
     *
     * @param ProcessorInterface $persistProcessor The processor for persisting data.
     * @param MessageRepository $messageRepository The repository for message entities.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param EmailService $emailService The email service.
     */
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private MessageRepository $messageRepository,
        private EntityManagerInterface $entityManager,
        private EmailService $emailService
    ) {
    }

    /**
     * Processes the purchase of a unicorn.
     *
     * @param mixed $data The data to process.
     * @param Operation $operation The operation being performed.
     * @param array $uriVariables The URI variables.
     * @param array $context The context for the operation.
     *
     * @return Unicorn|void The processed unicorn or void.
     *
     * @throws BadRequestHttpException If the data is not an instance of Unicorn.
     * @throws NotFoundHttpException If the unicorn is not found.
     * @throws ConflictHttpException If the unicorn is already purchased.
     * @throws RuntimeException If there is an error sending the email.
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // If not of type Unicorn throw error
        if (!$data instanceof Unicorn) throw new BadRequestHttpException("Expected instance of Unicorn");


        // If not valid Unicorn throw error
        if (is_null($data->getId())) throw new NotFoundHttpException("Unicorn not found");

        // If Unicorn already purchased throw error
        if ($data->isPurchased()) throw new ConflictHttpException("Unicorn already purchased");

        // Get email from request body
        $requestData = json_decode($context["request"]->getContent(), true);
        $email = $requestData["email"] ?? null;

        // If email address is empty throw error
        if (!$email) throw new BadRequestHttpException("Email address is required");

        // If not valid email address throw error
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new BadRequestHttpException("Invalid email address");

        // Send email
        try {
            $this->emailService->sendPurchaseEmail($email, $data);
        } catch (RuntimeException $e) {
            throw $e;
        }

        // Delete posts related to purchased unicorn
        foreach ($data->getMessages() as $message) {
            $this->entityManager->remove($message);
        }

        // Set unicorn to purchased
        $data->setPurchased(true);

        // Make changes to database
        $this->entityManager->flush();

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
