<?php


namespace App\Service;


use App\Entity\Store;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;

class StoreService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var StoreRepository */
    private $storeRepo;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->storeRepo = $this->em->getRepository(Store::class);
    }

    public function getEntityManager(): EntityManagerInterface {
        return $this->em;
    }

    public function createOrUpdateStore(string $name, $value, bool $flush = false): Store {
        $store = $this->storeRepo->findOrCreateByName($name)->setValue($value);
        $this->em->persist($store);
        if ($flush) {
            $this->em->flush();
        }
        return $store;
    }

    public function getStore(string $name, bool $throw = false): ?Store {
        return $this->storeRepo->findByName($name, $throw);
    }

    public function getStoreValue(string $name, $default = null, bool $throw = false) {
        if ($throw) {
            return $this->storeRepo->findByName($name, true)->getValue();
        }
        if ($store = $this->storeRepo->findByName($name)) {
            return $store->getValue();
        }
        return $default;
    }

    public function remove(string $name, bool $flush = false): void {
        if ($store = $this->getStore($name)) {
            $this->em->remove($store);
        }
        if ($flush) {
            $this->em->flush();
        }
    }

}