<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Fonction;
use App\Helpers\Constant;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service pour gérer la synchronisation entre les rôles Symfony et les fonctions
 */
class RoleFonctionManager
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Synchronise les fonctions d'un utilisateur en fonction de ses rôles
     */
    public function synchronizeFonctionsFromRoles(User $user): void
    {
        $roles = $user->getRoles();
        $fonctionRepository = $this->entityManager->getRepository(Fonction::class);
        
        // Tableau de correspondance inversé (rôle => label de fonction)
        $roleToLabel = array_flip(Constant::roles());
        
        foreach ($roles as $role) {
            // Ignorer ROLE_USER qui est attribué à tous les utilisateurs
            if ($role === 'ROLE_USER') {
                continue;
            }
            
            // Chercher le label de fonction correspondant au rôle
            $fonctionLabel = array_key_exists($role, $roleToLabel) ? $roleToLabel[$role] : null;
            
            if ($fonctionLabel) {
                // Vérifier si l'utilisateur a déjà cette fonction
                $fonction = $fonctionRepository->findOneBy(['label' => $fonctionLabel]);
                
                if ($fonction && !$user->getFonctions()->contains($fonction)) {
                    $user->addFonction($fonction);
                }
            }
        }
    }
    
    /**
     * Synchronise les rôles d'un utilisateur en fonction de ses fonctions
     * (Cette fonctionnalité est déjà gérée par la méthode getRoles() de l'entité User)
     */
    public function synchronizeRolesFromFonctions(User $user): void
    {
        // Cette méthode est fournie pour compléter l'API, mais la logique
        // est déjà implémentée dans la méthode getRoles() de l'entité User
        
        // On pourrait éventuellement mettre à jour les rôles stockés en base de données
        // pour qu'ils correspondent aux fonctions, mais ce n'est pas nécessaire
        // car getRoles() génère déjà les rôles dynamiquement
    }
}
