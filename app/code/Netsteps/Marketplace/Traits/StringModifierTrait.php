<?php
/**
 * StringModifierTrait
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Traits;

/**
 * Trait StringModifierTrait
 * @package Netsteps\Marketplace\Traits
 */
trait StringModifierTrait
{
    /**
     * @param string $Name
     * @param int $useDash
     * @param bool $useForUrl
     * @return string
     */
    protected  function toGreeklish(string $Name,int $useDash = 2, bool $useForUrl = TRUE): string {

        $greek = [
            'α', 'ά', 'Ά', 'Α', 'β', 'Β', 'γ', 'Γ', 'δ', 'Δ', 'ε', 'έ', 'Ε', 'Έ', 'ζ', 'Ζ', 'η', 'ή', 'Η', 'θ', 'Θ', 'ι', 'ί',
            'ϊ', 'ΐ', 'Ϊ', 'Ι', 'Ί', 'κ', 'Κ', 'λ', 'Λ', 'μ', 'Μ', 'ν', 'Ν', 'ξ', 'Ξ', 'ο', 'ό', 'Ο', 'Ό', 'π', 'Π', 'ρ', 'Ρ',
            'σ', 'ς', 'Σ', 'τ', 'Τ', 'υ', 'ύ', 'Υ', 'Ύ', 'φ', 'Φ', 'χ', 'Χ', 'ψ', 'Ψ', 'ω', 'ώ', 'Ω', 'Ώ',
        ];
        $english = [
            'a', 'a', 'A', 'A', 'b', 'B', 'g', 'G', 'd', 'D', 'e', 'e', 'E', 'E', 'z', 'Z', 'i', 'i', 'I', 'th', 'Th', 'i', 'i',
            'i', 'i', 'i', 'I', 'I', 'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'x', 'X', 'o', 'o', 'O', 'O', 'p', 'P', 'r', 'R',
            's', 's', 'S', 't', 'T', 'u', 'u', 'Y', 'Y', 'f', 'F', 'ch', 'Ch', 'ps', 'Ps', 'o', 'o', 'O', 'O',
        ];
        switch ( (int) $useDash ) {
            case 1:
                $greek[]   = ' ';
                $greek[]   = '-';
                $english[] = '-';
                $english[] = '-';
                break;
            case 2:
                $greek[]   = ' ';
                $greek[]   = '-';
                $english[] = '_';
                $english[] = '_';
                break;
            default:
                $greek[]   = ' ';
                $english[] = ' ';
                break;
        }

        $string = strtolower( str_replace( $greek, $english, $Name ) );
        return preg_replace( $useForUrl ? '/[^a-zA-Z0-9_\-\.\/ \s]/' : '/[^a-zA-Z0-9_\- \s]/', '', $string );
    }

    /**
     * Normalize filename
     * @param string $file
     * @return string
     */
    protected function normalizeFilename(string $file): string {
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $replacements = [".",","," ",";","'","\\","\"","/","(",")","?"];
        $filename = str_replace($replacements, '_', $filename);
        return "{$filename}.{$extension}";
    }
}
