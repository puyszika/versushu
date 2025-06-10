<?php

namespace App\Services;

class  OcrService
{
    public function extractText(string $imagePath): string
    {
        // 1️⃣ Kép előfeldolgozás (új útvonal)
        $preprocessedPath = $this->preprocessImage($imagePath);

        // 2️⃣ Tesseract futtatása
        $outputPath = tempnam(sys_get_temp_dir(), 'ocr_');
        $command = '"C:\Program Files\Tesseract-OCR\tesseract.exe" ' .
            escapeshellarg($preprocessedPath) . ' ' .
            escapeshellarg($outputPath) .
            ' --psm 6 --oem 1 -l eng+osd'; // vagy eng+rus ha kell cirill is

        \Log::info('📸 Tesseract parancs:', ['cmd' => $command]);

        exec($command);
        $text = @file_get_contents($outputPath . '.txt');

        // 3️⃣ Takarítás
        @unlink($outputPath);
        @unlink($outputPath . '.txt');
        @unlink($preprocessedPath);

        return $text ?: '';
    }

    private function preprocessImage(string $originalPath): string
    {

        $mime = mime_content_type($originalPath);

        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($originalPath);
                break;
            case 'image/png':
                $src = imagecreatefrompng($originalPath);
                break;
            default:
                \Log::error("❌ Nem támogatott képtípus: $mime");
                return $originalPath;
        }

        if (!$src) {
            \Log::error('❌ Nem sikerült megnyitni a képet: ' . $originalPath);
            return $originalPath;
        }

        if (!$src) {
            \Log::error('❌ Nem sikerült megnyitni a képet: ' . $originalPath);
            return $originalPath;
        }

        $width = imagesx($src);
        $height = imagesy($src);

        // 1️⃣ Dupla méret
        $scaled = imagecreatetruecolor($width * 2, $height * 2);
        imagecopyresampled($scaled, $src, 0, 0, 0, 0, $width * 2, $height * 2, $width, $height);

        // 2️⃣ Szürkeárnyalatos konvertálás
        imagefilter($scaled, IMG_FILTER_GRAYSCALE);

        // 3️⃣ Kontraszt és élesség
        imagefilter($scaled, IMG_FILTER_CONTRAST, -50);
        imagefilter($scaled, IMG_FILTER_SMOOTH, -6);

        // 4️⃣ Élesítés (unsharp mask imitáció)
        $sharpenMatrix = [
            [-1, -1, -1],
            [-1, 16, -1],
            [-1, -1, -1]
        ];
        imageconvolution($scaled, $sharpenMatrix, 8, 0);

        // 5️⃣ Kép mentés
        $preprocessedPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('pre_') . '.jpg';
        imagejpeg($scaled, $preprocessedPath, 95);

        // Felszabadítás
        imagedestroy($src);
        imagedestroy($scaled);

        return $preprocessedPath;
    }

    // Maradhat benne ez is, ha strukturált adatok kellenek
    public function extractStructuredData(string $ocrText): array
    {
        $text = strtolower($ocrText);

        // 1️⃣ Map neve (pl. Premier Nuke)
        preg_match('/premier\s+([a-z]+)/i', $ocrText, $mapMatch);
        $map = ucfirst($mapMatch[1] ?? 'ismeretlen');

        // 2️⃣ Eredmény: Terrorists vs Counter-Terrorists
        preg_match('/terrorists.*?(\d{1,2})/i', $ocrText, $tMatch);
        preg_match('/counter[- ]terrorists.*?(\d{1,2})/i', $ocrText, $ctMatch);
        $score = [
            'terrorists' => isset($tMatch[1]) ? (int)$tMatch[1] : null,
            'counter_terrorists' => isset($ctMatch[1]) ? (int)$ctMatch[1] : null,
        ];

        // 3️⃣ Játékos stat blokkok (pont, damage alapján keressük)
        // Minta: JaJan   14.055  151   4.00   151   1968
        preg_match_all('/([^\d\s][\S ]{2,30})\s+(\d{1,3}[.,]?\d*)\s+(\d{1,3})\s+\d+\s+[\d.]+\s+[\d.]+\s+(\d{3,4})/', $ocrText, $matches, PREG_SET_ORDER);

        $players = [];
        foreach ($matches as $i => $match) {
            $players[] = [
                'team' => $i < 5 ? 'CT' : 'T', // első 5 CT, utolsó 5 T
                'name' => trim($match[1]),
                'score' => (float)str_replace(',', '.', $match[2]),
                'ud' => (int)$match[3],
                'damage' => (int)$match[4],
            ];
        }

        // 4️⃣ MVP keresés (a legtöbb pontot szerző játékos)
        $mvp = null;
        if (!empty($players)) {
            usort($players, fn($a, $b) => $b['score'] <=> $a['score']);
            $mvp = $players[0]['name'] ?? null;
        }

        return [
            'map' => $map,
            'score' => $score,
            'players' => $players,
            'mvp' => $mvp,
        ];
    }
}
