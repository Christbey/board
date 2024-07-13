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
    'expectedPointsTable' => [
        1 => 6.0, 2 => 5.8, 3 => 5.6, 4 => 5.4, 5 => 5.2,
        6 => 5.0, 7 => 4.8, 8 => 4.6, 9 => 4.4, 10 => 4.2,
        11 => 4.0, 12 => 3.8, 13 => 3.6, 14 => 3.4, 15 => 3.2,
        16 => 3.0, 17 => 2.8, 18 => 2.6, 19 => 2.4, 20 => 2.2,
        21 => 2.0, 22 => 1.8, 23 => 1.6, 24 => 1.4, 25 => 1.2,
        26 => 1.0, 27 => 0.8, 28 => 0.6, 29 => 0.4, 30 => 0.2,
        31 => 0.0, 32 => -0.2, 33 => -0.4, 34 => -0.6, 35 => -0.8,
        36 => -1.0, 37 => -1.2, 38 => -1.4, 39 => -1.6, 40 => -1.8,
        41 => -2.0, 42 => -2.2, 43 => -2.4, 44 => -2.6, 45 => -2.8,
        46 => -3.0, 47 => -3.2, 48 => -3.4, 49 => -3.6, 50 => -3.8,
        51 => -4.0, 52 => -4.2, 53 => -4.4, 54 => -4.6, 55 => -4.8,
        56 => -5.0, 57 => -5.2, 58 => -5.4, 59 => -5.6, 60 => -5.8,
        61 => -6.0, 62 => -6.2, 63 => -6.4, 64 => -6.6, 65 => -6.8,
        66 => -7.0, 67 => -7.2, 68 => -7.4, 69 => -7.6, 70 => -7.8,
        71 => -8.0, 72 => -8.2, 73 => -8.4, 74 => -8.6, 75 => -8.8,
        76 => -9.0, 77 => -9.2, 78 => -9.4, 79 => -9.6, 80 => -9.8,
        81 => -10.0, 82 => -10.2, 83 => -10.4, 84 => -10.6, 85 => -10.8,
        86 => -11.0, 87 => -11.2, 88 => -11.4, 89 => -11.6, 90 => -11.8,
        91 => -12.0, 92 => -12.2, 93 => -12.4, 94 => -12.6, 95 => -12.8,
        96 => -13.0, 97 => -13.2, 98 => -13.4, 99 => -13.6
    ],
    'playTypePatterns' => [
        'pass short left' => 'Pass short left',
        'pass short middle' => 'Pass short middle',
        'pass short right' => 'Pass short right',
        'pass deep left' => 'Pass deep left',
        'pass deep middle' => 'Pass deep middle',
        'pass deep right' => 'Pass deep right',
        'scrambles' => 'Scrambles',
        'up the middle' => 'Up the middle',
        'left guard' => 'Left guard',
        'right guard' => 'Right guard',
        'left tackle' => 'Left tackle',
        'right tackle' => 'Right tackle',
        'left end' => 'Left end',
        'right end' => 'Right end',
        'punts' => 'Punt',
        'incomplete' => 'Incomplete',
        'kicks' => 'Kickoff',
        'kneels' => 'Kneel',
    ],
];
