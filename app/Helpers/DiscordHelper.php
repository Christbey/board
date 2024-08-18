<?php

namespace App\Helpers;

use App\Models\NflTeam;
use NotificationChannels\Discord\DiscordMessage;
use App\Models\NflEspnTeam;

class DiscordHelper
{
    protected $embed = [];

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
        // If color is not provided, fallback to the team's primary color if available
        if (!$color && $team) {
            $color = $team->primary_color;
        }

        // If still no color, use a default color
        $color = $color ?: '#7289da';

        // Convert the color to a hex value, remove the # if present
        $this->embed['color'] = hexdec(str_replace('#', '', $color));

        return $this;
    }


    public function setTimestamp($timestamp = null): self
    {
        $this->embed['timestamp'] = $timestamp ?? now()->toIso8601String();
        return $this;
    }

    public function addField(string $name, string $value, bool $inline = false): self
    {
        $this->embed['fields'][] = [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];
        return $this;
    }

    public function presetNewsEmbed(string $headline, string $description, string $url, string $author, string $category, string $color): self
    {
        return $this->setTitle($headline)
            ->setDescription($description)
            ->setUrl($url)
            ->setColor($color)
            ->setTimestamp(now())
            ->addField('Author', $author, true)
            ->addField('Category', $category, true);
    }

    public function build(): DiscordMessage
    {
        return DiscordMessage::create('')
            ->embed($this->embed);
    }

    public function reset(): self
    {
        $this->embed = [];
        return $this;
    }
}
