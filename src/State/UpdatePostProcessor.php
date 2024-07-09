<?php

// src/State/UpdatePostProcesser.php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class to process updating a Message entity.
 */
final class UpdatePostProcessor implements ProcessorInterface
{
    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     */
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Processes the update of a Message.
     *
     * @param mixed $data The data to process.
     * @param Operation $operation The current operation.
     * @param array $uriVariables The URI variables.
     * @param array $context The context.
     *
     * @return Message|void The processed data.
     *
     * @throws BadRequestHttpException if the data is not an instance of Message or the message is not valid.
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // If not of type Message throw error
        if (!$data instanceof Message) throw new BadRequestHttpException("Expected instance of Message");

        // Simply reject any dirty input
        if ($data->getMessage() !== htmlspecialchars($data->getMessage())) throw new BadRequestHttpException("Invalid data");

        // Make changes to database
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
