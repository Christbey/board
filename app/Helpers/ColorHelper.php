<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Convert hex color to RGB.
     *
     * @param string $hex
     * @return string
     */
    public static function hex2rgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "$r, $g, $b";
    }

    /**
     * Get the primary color for a team.
     *
     * @param string $teamName
     * @return string
     */
    public static function getPrimaryColor($teamName)
    {
        // Example logic for determining team colors
        $teamColors = [
            'Team A' => '#ff0000', // Red
            'Team B' => '#00ff00', // Green
            // Add more teams and their primary colors here
        ];

        return $teamColors[$teamName] ?? '#ffffff'; // Default to white if the team color is not found
    }
}
