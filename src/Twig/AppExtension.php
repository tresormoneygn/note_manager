<?php

namespace App\Twig;

use App\Repository\AnneeRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private AnneeRepository $anneeRepository;

    public function __construct(AnneeRepository $anneeRepository)
    {
        $this->anneeRepository = $anneeRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_annees', [$this, 'getAnnees']),
            // Fonction pour recuperer l'annee en cours
            new TwigFunction('get_annee_en_cours', [$this,'getAnneeEnCours']),
        ];
    }

    public function getAnnees()
    {
        return $this->anneeRepository->findAll();
    }

    // Recuperer l'annee en cours avec le annee repostitory
    public function getAnneeEnCours()
    {
        return $this->anneeRepository->findOneBy(['is_progress' => True]);
    }
}