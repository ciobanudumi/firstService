<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct( private EntityManagerInterface $entityManager, private HttpClientInterface $client,private SerializerInterface $serializer )
    {
    }

    public function process(mixed $data, Operation $operation,array $uriVariables = [], array $context = [])
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush($data);

        $jsonData = $this->serializer->serialize($data, 'json');

        $emailData = json_decode($jsonData, true);

        $emailResponse = $this->client->request(
            'POST',
            'http://127.0.0.1:8003/send-email-new-user',[
                'json'=>$emailData
            ]
        );

        return $data;
    }
}