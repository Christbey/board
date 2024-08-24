<?php

return [
    /*
     * The webhook URLs that we'll use to send a message to Discord.
     */

    'default_channel' => ('1273829151568166912'),
    'nfl_score_channel' => ('1275646968559046727'),
    'nfl_news_channel' => ('1273455512586813522'),
    'nfl_injury_channel' => ('1275651916449321064'),
    'rating_channel' => ('1276027912637648906'),
    'nfl_odds_channel' => ('1273829151568166912'),
    'cfb_odds_channel' => ('1276780576468238348'),

    'webhook_urls' => [
        'nfl-news' => ('https://discord.com/api/webhooks/1273455619122139136/81SqIWBNQl6kwswyLO4b3rJ4f6zGBZ289k_HhuHxJlsF6-vr8xFkxRxq1S0PS6JBQrSY'),
        'nfl-odds' => ('https://discord.com/api/webhooks/1273829380967239694/3GglCIYOkPYtbgMmQWWpho_ZTNB0E7JC8fLXKhZnviarnCg_gs8X8xBMJZF0XgPD4X1i'),
        'cfb-events' => ('https://discord.com/api/webhooks/1274248813938217082/Vmc_j5vxT1yCnYFX1XISk9FUtIzxj5aEdBW-WdoPeBkKPX9HN-nstP4AuHkU3Pheva9Q')

    ],

    /*
     * This job will send the message to Discord. You can extend this
     * job to set timeouts, retries, etc...
     */
];
