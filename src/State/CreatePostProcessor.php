<?php

// src/State/CreatePostProcesser.php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\{Message, Unicorn};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, ConflictHttpException, NotFoundHttpException};

/**
 * Class to process the creation of a new Message entity.
 */
final class CreatePostProcessor implements ProcessorInterface
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
     * Processes the creation of a new Message.
     *
     * @param mixed $data The data to process.
     * @param Operation $operation The current operation.
     * @param array $uriVariables The URI variables.
     * @param array $context The context.
     *
     * @return Message|void The processed data.
     *
     * @throws BadRequestHttpException if the data is not an instance of Message or the unicorn is not valid.
     * @throws NotFoundHttpException if the unicorn is not found.
     * @throws ConflictHttpException if the unicorn has already been purchased.
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // If not of type Message throw error
        if (!$data instanceof Message) throw new BadRequestHttpException("Expected instance of Message");

        // If not of type Unicorn throw error
        if (!$data->getUnicorn() instanceof Unicorn) throw new BadRequestHttpException("Expected instance of Unicorn");

        // If not valid Unicorn throw error
        if (is_null($data->getUnicorn()->getId())) throw new NotFoundHttpException("Unicorn not found");

        // If Unicorn has already been purchased throw error
        if ($data->getUnicorn()->isPurchased()) throw new ConflictHttpException("Unicorn has been purchased");

        // Make changes to database
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
