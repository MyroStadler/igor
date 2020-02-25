<?php


namespace App\Controller;


use MyroStadler\Maya\Analysers\AbstractAnalysis;
use MyroStadler\Maya\Analysers\Uri\UriAnalyser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MayaController extends AbstractController
{
    public function __construct(
    ) {
    }

    public function test(UriAnalyser $analyser): Response {
        $options = [
            AbstractAnalysis::OPT_ALL => 1,
        ];
        $urls = [
            "https://docs.google.com/spreadsheets/d/1jcUtg_pGv7krZbjArAKyZgCN22WkIT_LZUtSJMibruY/edit?usp=sharing",
            "https://docs.google.com/document/d/1Sj8Ew2rp5y0eQjGtsoRO-qHGYyVIzSBww44yB0QNhaY/edit?usp=sharing",
            "https://docs.google.com/presentation/d/1QNafokpKZk-H_nN31vnUakvQ2KXafo_-dj9hp6frAT0/edit?usp=sharing",
            "https://docs.google.com/drawings/d/1kI0cWy6fT38GV6RbbhnmQYvdfxD9HMPXlOrbDnc1PKY/edit?usp=sharing",
            "https://drive.google.com/file/d/1NvxNseaNImLUcei-z72svH8izhQmE4Dq/view?usp=sharing",
        ];
        return new JsonResponse($analyser->analyseGoogleShareUri($urls[1])->toArray($options));
    }
}