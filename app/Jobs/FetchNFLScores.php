<?php

 namespace App\Jobs;

 use Illuminate\Bus\Queueable;
 use Illuminate\Contracts\Queue\ShouldQueue;
 use Illuminate\Foundation\Bus\Dispatchable;
 use Illuminate\Queue\InteractsWithQueue;
 use Illuminate\Queue\SerializesModels;
 use Illuminate\Support\Facades\Http;
 use Illuminate\Support\Facades\Log;

 class FetchNFLScores implements ShouldQueue
 {
     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     public $scores;

     public function __construct()
     {
         //
     }

     public function handle()
     {
         try {
             $response = Http::get('https://api.the-odds-api.com/v4/sports/americanfootball_nfl/scores', [
                 'apiKey' => env('ODDS_API_KEY'), // Replace with your actual API key
             ]);

             if ($response->successful()) {
                 $this->scores = $response->json();

                 // Ensure $scores is an array
                 if (!is_array($this->scores)) {
                     $this->scores = [];
                 }
             } else {
                 // Log the error
                 Log::error('Error fetching NFL scores', ['response' => $response->body()]);
                 $this->scores = [];
             }
         } catch (\Exception $e) {
             // Handle any exceptions that occur during the API request
             Log::error('Exception fetching NFL scores', ['exception' => $e->getMessage()]);
             $this->scores = [];
         }
     }
 }
// Compare this snippet from app/Http/Controllers/NflController.php:
