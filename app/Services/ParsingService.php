<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class ParsingService
{
    public function parseDownDistance(string $downAndDistance): array
    {
        $downDistancePattern = '/(\d+)(?:st|nd|rd|th)\s*&\s*(\d+|Goal)\s*at\s*([A-Z]{2,3})\s*(\d+)/i';
        preg_match($downDistancePattern, $downAndDistance, $matches);

        return [
            'down' => $matches[1] ?? null,
            'distance' => $matches[2] ?? null,
            'yard_line' => $matches[4] ?? null,
        ];
    }

    public function parsePlayType(string $playDescription): string
    {
        $playTypePatterns = Config::get('nfl.playTypePatterns');
        $penaltyPatterns = Config::get('nfl.penaltyPatterns');

        foreach ($penaltyPatterns as $pattern) {
            if (str_contains(strtolower($playDescription), $pattern)) {
                return 'Penalty';
            }
        }

        if (str_contains(strtolower($playDescription), 'touchback')) {
            return 'Touchback';
        }

        if (str_contains(strtolower($playDescription), 'touchdown')) {
            return 'Touchdown';
        }

        foreach ($playTypePatterns as $pattern => $type) {
            if (str_contains(strtolower($playDescription), $pattern)) {
                return $type;
            }
        }

        return 'Unknown';
    }

    public function parseFieldPositionFromDownAndDistance(string $downAndDistance): int
    {
        $pattern = '/at [A-Z]{2,3} (\d+)/';
        preg_match($pattern, $downAndDistance, $matches);

        return isset($matches[1]) ? (int)$matches[1] : 0;
    }

    public function parseEndFieldPositionFromPlay(string $playDescription, int $startFieldPosition, string $playType): int
    {
        $endFieldPositionPattern = '/pushed ob at [A-Z]{2,3} (\d+)/i';
        preg_match($endFieldPositionPattern, $playDescription, $matches);

        if (isset($matches[1])) {
            return (int)$matches[1];
        }

        $endFieldPositionPatternFallback = '/at [A-Z]{2,3} (\d+)/i';
        preg_match($endFieldPositionPatternFallback, $playDescription, $matches);

        if (isset($matches[1])) {
            return (int)$matches[1];
        }

        if ($playType === 'Touchback') {
            return 25;
        }

        if (preg_match('/for (\d+) yards/', $playDescription, $yardMatches)) {
            return max($startFieldPosition - (int)$yardMatches[1], 0);
        }

        return 0;
    }
}