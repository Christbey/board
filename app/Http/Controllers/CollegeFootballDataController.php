<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollegeFootballDataController extends Controller
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        // Initialize Guzzle client
        $this->client = new Client([
            'base_uri' => 'https://api.collegefootballdata.com/',
        ]);

        // Set your API key (you should store this in your .env file)
        $this->apiKey = env('COLLEGE_FOOTBALL_DATA_API_KEY');
    }

    /**
     * Fetch game results for a specific year.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGames(Request $request)
    {
        $year = $request->input('year');

        try {
            $response = $this->client->request('GET', 'games', [
                'query' => [
                    'year' => $year,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch season calendar for a specific year.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCalendar(Request $request)
    {
        $year = $request->input('year');

        try {
            $response = $this->client->request('GET', 'calendar', [
                'query' => [
                    'year' => $year,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch game media information for a specific year and week.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGameMedia(Request $request)
    {
        $year = $request->input('year');
        $week = $request->input('week');
        $seasonType = $request->input('seasonType');
        $team = $request->input('team');
        $conference = $request->input('conference');
        $mediaType = $request->input('mediaType');
        $classification = $request->input('classification');

        try {
            $response = $this->client->request('GET', 'games/media', [
                'query' => [
                    'year' => $year,
                    'week' => $week,
                    'seasonType' => $seasonType,
                    'team' => $team,
                    'conference' => $conference,
                    'mediaType' => $mediaType,
                    'classification' => $classification,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch game weather information for a specific game or year and week.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGameWeather(Request $request)
    {
        $gameId = $request->input('gameId');
        $year = $request->input('year');
        $week = $request->input('week');
        $seasonType = $request->input('seasonType');
        $team = $request->input('team');
        $conference = $request->input('conference');
        $classification = $request->input('classification');

        try {
            $response = $this->client->request('GET', 'games/weather', [
                'query' => [
                    'gameId' => $gameId,
                    'year' => $year,
                    'week' => $week,
                    'seasonType' => $seasonType,
                    'team' => $team,
                    'conference' => $conference,
                    'classification' => $classification,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPlayerGameStats(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'games/players', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch team game stats.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTeamGameStats(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'games/teams', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch advanced box score.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAdvancedBoxScore(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'game/box/advanced', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch drive data and results.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDrives(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'drives', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch play by play data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPlays(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'plays', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch live play by play data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLivePlays(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'live/plays', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Fetch play types.
     *
     * @return JsonResponse
     */
    public function getPlayTypes()
    {
        try {
            $response = $this->client->request('GET', 'play/types', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch play stats by play.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPlayStats(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'play/stats', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch conferences.
     *
     * @return JsonResponse
     */
    public function getConferences()
    {
        try {
            $response = $this->client->request('GET', 'conferences', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch teams information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTeams(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'teams', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch FBS teams.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getFbsTeams(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'teams/fbs', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch team rosters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRoster(Request $request)
    {
        try {
            $response = $this->client->request('GET', 'roster', [
                'query' => $request->all(),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


// Add additional methods for other endpoints as needed
}
