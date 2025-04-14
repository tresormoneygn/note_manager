<?php

namespace App\Helpers;

use Symfony\Component\Uid\Uuid;

class AppHelper
{
    /**
     * Génère un UUID v4 aléatoire.
     */
    public static function generateUuid(): string
    {
        return Uuid::v4()->toRfc4122();
    }

    /**
     * Génère un mot de passe aléatoire.
     *
     * @param int $length
     */
    public static function generateRandomPassword(int $length = 12): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Slugifie une chaîne de caractères.
     *
     * @param string $text
     */
    public static function slugify(string $text): string
    {
        // Remplace les caractères non alphanumériques par des tirets
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Translitération
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Supprime les caractères indésirables
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Supprime les tirets en début/fin
        $text = trim($text, '-');
        // Supprime les doubles tirets
        $text = preg_replace('~-+~', '-', $text);
        // Convertit en minuscule
        return strtolower($text ?: 'n-a');
    }

    /**
     * Formate une date en une chaîne lisible (ex: 14 avril 2025).
     *
     * @param \DateTimeInterface $date
     * @param string $locale
     */
    public static function formatReadableDate(\DateTimeInterface $date, string $locale = 'fr_FR'): string
    {
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
        return $formatter->format($date);
    }

    /**
     * Nettoie une chaîne (trim, supprime les balises HTML, etc.)
     *
     * @param string $value
     */
    public static function cleanString(string $value): string
    {
        return trim(strip_tags($value));
    }
}
