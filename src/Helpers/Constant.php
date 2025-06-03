<?php

namespace App\Helpers;

class Constant
{
    public const STRING_EMPTY = '';

    public static function roles(): array
    {
        return array(
            'DG' => 'ROLE_DG',
            'DGA/E' => 'ROLE_DGA_E',
            'DGA/R' => 'ROLE_DGA_R',
            'SF' => 'ROLE_SF',
            'CD' => 'ROLE_CD',
            'DP' => 'ROLE_DP',
            'P' => 'ROLE_P',
            'E' => 'ROLE_ETUDIANT',
            'S' => 'ROLE_SCOLARITE',
        );
    }

    public static function semestreToClassId($semestre)
    {
        // Tableau associatif pour le mapping
        $mapping = [
            'Semestre 1' => 1,
            'Semestre 2' => 1,
            'Semestre 3' => 2,
            'Semestre 4' => 2,
            'Semestre 5' => 3,
            'Semestre 6' => 3,
        ];

        // Vérifie si la clé existe dans le tableau
        if (array_key_exists($semestre, $mapping)) {
            return $mapping[$semestre];
        }

        // Retourne null ou une erreur si non trouvé
        return null;
    }


    public static function role(string $role): string{
        return self::roles()[$role];
    }

    public static function litteraRole(String $role): string
    {
        switch ($role) {
            case 'DG':
                return 'Directeur General';
            case 'DGA/E':
                return 'Directeur General Adjoint des Etudes';
            case 'DGA/R':
                return 'Directeur General Adjoint de la Recherche';
            case 'SF':
                return 'Sécretaire de Faculté';
            case 'CD':
                return 'Chef de Département';
            case 'DP':
                return 'Directeur de Programme';
            case 'P':
                return 'Professeur';
            case 'E':
                return 'Etudiant';
            case 'S':
                return 'Scolarité';
            default:
                return '';
        }
    }
}