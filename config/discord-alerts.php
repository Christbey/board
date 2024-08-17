<?php

return [
    /*
     * The webhook URLs that we'll use to send a message to Discord.
     */
    'webhook_urls' => [
        'default' => ('https://discord.com/api/webhooks/1273455542542794822/RUKGoPVdLqrwHnt-1gJc4B6Ki0schk06VKMcSycAHiihxZUAh3rQeZQSjnHd7cQ1N-a-'),
        'nfl-odds' => ('https://discord.com/api/webhooks/1273829380967239694/3GglCIYOkPYtbgMmQWWpho_ZTNB0E7JC8fLXKhZnviarnCg_gs8X8xBMJZF0XgPD4X1i')
    ],

    /*
     * This job will send the message to Discord. You can extend this
     * job to set timeouts, retries, etc...
     */
    'job' => Spatie\DiscordAlerts\Jobs\SendToDiscordChannelJob::class,
];
