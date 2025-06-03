<?php

namespace App\Constant;

enum FileConstant : string {
    case EXCEL_FILE_NUMERO = "N°";
    case EXCEL_FILE_MATRICULE = 'Matricule';
    case EXCEL_FILE_NOM = 'Nom';
    case EXCEl_FILE_PRENOM = 'Prénom';
    case EXCEL_FILE_NOTE_1 = 'Note 1';
    case EXCEL_FILE_NOTE_2 = 'Note 2';
    case EXCEL_FILE_NOTE_3 = 'Note 3';
    case EXCEL_FILE_MOYENNE = 'Moyenne';
    case EXCEL_FILE_SEXE = 'Sexe';
}