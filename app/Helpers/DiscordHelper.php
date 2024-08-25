<?php

namespace App\Helpers;

use App\Models\NflTeam;
use NotificationChannels\Discord\DiscordMessage;
use App\Models\NflEspnTeam;

class DiscordHelper
{
    protected array $embed = [];

    public function build(): DiscordMessage
    {
        // Optionally add a sleep here before building the message
        $this->sleep();

        return DiscordMessage::create('')
            ->embed($this->embed);
    }

    public function setTitle(string $title): self
    {
        $this->embed['title'] = $title;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->embed['description'] = $description;
        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->embed['url'] = $url;
        return $this;
    }

    public function setColor(string $color = null, ?NflTeam $team = null): self
    {
        if (!$color && $team) {
            $color = $team->primary_color;
        }

        $color = $color ?: '#7289da';
        $color = ltrim($color, '#');

        if (strlen($color) !== 6) {
            $color = '7289da';
        }

        $this->embed['color'] = hexdec($color);

        return $this;
    }

    public function setTimestamp($timestamp = null): self
    {
        $this->embed['timestamp'] = $timestamp ?? now()->toIso8601String();
        return $this;
    }

    public function addField(string $name, $value, bool $inline = false): self
    {
        $value = (string)$value;

        $this->embed['fields'][] = [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];
        return $this;
    }

    public function setFooter(string $text, string $iconUrl = null): self
    {
        $this->embed['footer'] = [
            'text' => $text,
        ];

        if ($iconUrl) {
            $this->embed['footer']['icon_url'] = $iconUrl;
        }

        return $this;
    }

    public function presetEmbed(string $headline, string $description, string $url, string $author, string $category, string $color, string $published): self
    {
        return $this->reset()
            ->setTitle($headline)
            ->setDescription($description)
            ->setUrl($url)
            ->setColor($color)
            ->setTimestamp($published)
            ->setFooter("$author | $category");
    }

    public function reset(): self
    {
        $this->embed = [];
        return $this;
    }

    // Add the sleep function
    public function sleep(int $seconds = 1): self
    {
        sleep($seconds); // Sleep for the specified number of seconds
        return $this;
    }
}
