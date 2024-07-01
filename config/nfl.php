<?php

// config/nfl.php

return [
    'scalingFactor' => 0.35,
    'homeAdjustment' => 2.5,
    'spreadAdjustment' => 0.5,
    'powerRankingInfluence' => 0.3,
    'homePtsMax' => 28,
    'awayPtsMax' => 24,
    'powerRankings' => [
        1 => 25,  // Arizona Cardinals
        2 => 14,  // Atlanta Falcons
        3 => 4,   // Baltimore Ravens
        4 => 8,   // Buffalo Bills
        5 => 32,  // Carolina Panthers
        6 => 15,  // Chicago Bears
        7 => 5,   // Cincinnati Bengals
        8 => 13,  // Cleveland Browns
        9 => 9,   // Dallas Cowboys
        10 => 31, // Denver Broncos
        11 => 3,  // Detroit Lions
        12 => 12, // Green Bay Packers
        13 => 7,  // Houston Texans
        14 => 18, // Indianapolis Colts
        15 => 19, // Jacksonville Jaguars
        16 => 2,  // Kansas City Chiefs
        17 => 11, // Miami Dolphins
        18 => 6,  // Minnesota Vikings
        19 => 29, // New England Patriots
        20 => 24, // New Orleans Saints
        21 => 30, // New York Giants
        22 => 20, // New York Jets
        23 => 27, // Las Vegas Raiders
        24 => 10,  // Philadelphia Eagles
        25 => 17, // Pittsburgh Steelers
        26 => 21, // Los Angeles Chargers
        27 => 1, // San Francisco 49ers
        28 => 28, // Seattle Seahawks
        29 => 16, // Los Angeles Rams
        30 => 26, // Tampa Bay Buccaneers
        31 => 22, // Tennessee Titans
        32 => 23  // Washington Commanders
    ],
    'qbrScalingFactor' => 0.1,  // Scaling factor for QBR influence
];
