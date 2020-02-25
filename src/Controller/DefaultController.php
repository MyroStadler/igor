<?php


namespace App\Controller;


use App\Entity\Store;
use App\Repository\StoreRepository;
use App\Service\MediumService;
use App\Service\StoreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{

    public function __construct(
    ) {
    }

    public function home(): Response {
        $quotes = explode("\n", trim("
            My grandfather use to work for your grandfather. Of course the rates have gone up.
            Not the third switch!
            Well, why isn't it \"Froaderick Frokensteen\"?
            What hump?
            Had a bat stuck in the belfry if you know what I mean.
            I'm sick of being treated like just another Igor. 
            Are you actually trying to hypnotise me, Brain?
            Walk this way.
            Could be raining. *starts raining*
            [Crosses arms] No.
            Call it... a hunch. Ba-dum SHI!
            Wait Master, it might be dangerous... you go first.
        "));
        $quote = trim($quotes[array_rand($quotes)]);
        return new Response($this->renderView('default/home.html.twig', ['quote' => $quote]));
    }
}