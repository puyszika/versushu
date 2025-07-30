<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class StartCs2Server implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $server;
    public array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $server, array $data)
    {
        $this->server = $server;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $identifier = $this->server['identifier'];
        $map = $this->data['map'] ?? 'de_mirage';
        $players = $this->data['players'] ?? [];

        // Whitelist fájl létrehozása
        $whitelist = implode("\n", $players);
        $whitelistPath = "/mnt/ssd1/cs2server/cs2-multiserver/msm.d/cs2/cfg/{$identifier}/whitelist.txt";
        file_put_contents($whitelistPath, $whitelist);

        // Szerver újraindítása
        $command = "cd /mnt/ssd1/cs2server/cs2-multiserver && ./cs2-server {$identifier} restart";
        exec($command);

        // Logolás (opcionális)
        \Log::info("CS2 szerver elindítva: {$identifier} | Map: {$map} | Játékosok: " . implode(', ', $players));
    }
}
