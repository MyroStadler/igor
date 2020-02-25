<?php


namespace App\Controller;


use App\Entity\Store;
use App\Repository\StoreRepository;
use App\Service\MediumService;
use App\Service\StoreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends AbstractController
{
    /** @var StoreService */
    private $storeService;

    public function __construct(
        StoreService $storeService
    ) {
        $this->storeService = $storeService;
    }

    public function storeSet(): Response
    {
        $datetime = new \DateTime();
        $text = 'abc';
        $int = 42;
        $float = 3.1416;
        $array = [$text, $int, $float];
        $assocArray = ['text' => $text, 'int' => $int, 'float' => $float, 'array' => $array];

        $this->storeService->createOrUpdateStore('datetime', $datetime);
        $this->storeService->createOrUpdateStore('text', $text);
        $this->storeService->createOrUpdateStore('int', $int);
        $this->storeService->createOrUpdateStore('float', $float);
        $this->storeService->createOrUpdateStore('array', $array);
        $this->storeService->createOrUpdateStore('assoc_array', $assocArray);
        $this->storeService->getEntityManager()->flush();
        return new Response('done - check DB');
    }

    public function setString(string $name, string $value): Response {
        $this->storeService->createOrUpdateStore($name, $value, true);
        return new Response('done - check DB');
    }

    public function storeGet(): Response {
        $pieces = [];
        $pieces[] = $this->getStoreValue('datetime')->format('Y-m-d H:i:s');
        $pieces[] = $this->getStoreValue('text');
        $pieces[] = $this->getStoreValue('int');
        $pieces[] = $this->getStoreValue('float');
        $pieces[] = print_r($this->getStoreValue('array'), true);
        $pieces[] = print_r($this->getStoreValue('assoc_array'), true);
        return new Response('<pre>' . implode("\n", $pieces) . '</pre>');
    }

    private function getStoreValue(string $name, $default = null, bool $throw = true) {
        return $this->storeService->getStoreValue($name, $default, $throw);
    }
}