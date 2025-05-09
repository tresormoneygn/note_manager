<?php

namespace App\Helpers;

class Constant
{
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
        );
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
            default:
                return '';
        }
    }
}