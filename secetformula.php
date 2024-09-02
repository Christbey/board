public function calculateHypotheticalSpread($homeFpi, $awayFpi, $homeElo, $awayElo, $homeTalent, $awayTalent, $homeSpRating, $awaySpRating): float
{
$fpiSpread = $homeFpi && $awayFpi ? ($homeFpi - $awayFpi) / 2 : 0;
$eloSpread = $homeElo && $awayElo ? ($homeElo - $awayElo) / 25 : 0;
$talentSpread = $homeTalent && $awayTalent ? ($homeTalent - $awayTalent) / 100 : 0;
$spRatingSpread = $homeSpRating && $awaySpRating ? ($homeSpRating - $awaySpRating) / 100 : 0; // Adjust divisor as necessary

return round(($fpiSpread + $eloSpread + $talentSpread + $spRatingSpread) / 1.4, 2);
}