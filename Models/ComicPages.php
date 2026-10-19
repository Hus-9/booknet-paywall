<?php

require_once __DIR__ . '/Database.php';

// Provides comic metadata lookup plus freemium-friendly page generation/loading.
class ComicPages
{
    /**
     * Return a PDO handle that matches the comics listing (legacy Database::getInstance()).
     *
     * @return PDO
     */
    protected static function db()
    {
        return Database::getInstance()->getdbConnection();
    }

    /**
     * Fetch a comic row by id from the comicss table.
     *
     * @param int $comicId
     * @return array|null
     */
    public static function getComicById($comicId)
    {
        $stmt = self::db()->prepare('SELECT * FROM comics WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', (int)$comicId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row;
        }
        return null;
    }

    /**
     * Ensure the comic_pages table exists and seed pages for a comic when missing.
     *
     * @param array $comic
     * @return void
     */
    public static function ensurePagesExist($comic)
    {
        $db = self::db();
        self::createTableIfMissing($db);

        $comicId = (int)$comic['id'];

        $countStmt = $db->prepare('SELECT COUNT(*) AS total FROM comic_pages WHERE comic_id = :id');
        $countStmt->bindValue(':id', $comicId, PDO::PARAM_INT);
        $countStmt->execute();
        $existing = $countStmt->fetchColumn();

        if ($existing === false) {
            $existing = 0;
        }

        $existing = (int)$existing;

        if ($existing > 0) {
            return;
        }

        // Around 10–20 pages per comic; use mt_rand for older PHP compatibility.
        $pageTarget = mt_rand(10, 20);
        $pages = self::generatePages($comic, $pageTarget);

        $insert = $db->prepare(
            'INSERT OR IGNORE INTO comic_pages (comic_id, page_number, content) VALUES (:comic_id, :page_number, :content)'
        );

        foreach ($pages as $number => $content) {
            $insert->bindValue(':comic_id', $comicId, PDO::PARAM_INT);
            $insert->bindValue(':page_number', (int)$number, PDO::PARAM_INT);
            $insert->bindValue(':content', $content, PDO::PARAM_STR);
            $insert->execute();
        }
    }

    /**
     * Return the total page count for a comic.
     *
     * @param int $comicId
     * @return int
     */
    public static function getPageCount($comicId)
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM comic_pages WHERE comic_id = :id');
        $stmt->bindValue(':id', (int)$comicId, PDO::PARAM_INT);
        $stmt->execute();
        $value = $stmt->fetchColumn();

        if ($value === false) {
            $value = 0;
        }

        return (int)$value;
    }

    /**
     * Load a single page of content.
     *
     * @param int $comicId
     * @param int $pageNumber
     * @return string|null
     */
    public static function getPageContent($comicId, $pageNumber)
    {
        $stmt = self::db()->prepare(
            'SELECT content FROM comic_pages WHERE comic_id = :id AND page_number = :page LIMIT 1'
        );
        $stmt->bindValue(':id', (int)$comicId, PDO::PARAM_INT);
        $stmt->bindValue(':page', (int)$pageNumber, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && isset($row['content'])) {
            return $row['content'];
        }
        return null;
    }

    /**
     * Create the comic_pages table if it does not already exist.
     *
     * @param PDO $db
     * @return void
     */
    protected static function createTableIfMissing($db)
    {
        $db->exec(
            'CREATE TABLE IF NOT EXISTS comic_pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                comic_id INTEGER NOT NULL,
                page_number INTEGER NOT NULL,
                content TEXT NOT NULL,
                UNIQUE(comic_id, page_number),
                FOREIGN KEY(comic_id) REFERENCES comics(id) ON DELETE CASCADE
            );'
        );
    }

    /**
     * Generate an array of page_number => content for a comic.
     *
     * @param array $comic
     * @param int   $pageCount
     * @return array
     */
    protected static function generatePages($comic, $pageCount)
    {
        // Replace null-coalescing with isset checks.
        if (isset($comic['title']) && $comic['title'] !== null) {
            $title = trim((string)$comic['title']);
        } else {
            $title = 'Untitled';
        }

        if (isset($comic['genre']) && $comic['genre'] !== null) {
            $genre = trim((string)$comic['genre']);
        } else {
            $genre = 'Fiction';
        }

        if (isset($comic['author']) && $comic['author'] !== null) {
            $author = trim((string)$comic['author']);
        } else {
            $author = '';
        }

        $pages = array();
        for ($i = 1; $i <= $pageCount; $i++) {
            $pages[$i] = self::generatePageContent($title, $genre, $author, $i, $pageCount);
        }

        return $pages;
    }

    /**
     * Generate roughly 300–400 words of original text for a single page.
     *
     * @param string $title
     * @param string $genre
     * @param string $author
     * @param int    $page
     * @param int    $totalPages
     * @return string
     */
    protected static function generatePageContent($title, $genre, $author, $page, $totalPages)
    {
        $hooks = array(
            'mystery' => array(
                'a cryptic letter slipped under a door',
                'fog hiding the last clues',
                'voices speaking in code across the alley',
                'a watch that keeps worse time when secrets grow'
            ),
            'fantasy' => array(
                'old maps that rewrite themselves at dawn',
                'a lantern that glows when courage flickers',
                'cities built on the backs of sleeping giants',
                'rivers that remember every whispered promise'
            ),
            'sci-fi' => array(
                'engines that hum in three languages',
                'a station clock that runs backward for half the day',
                'codes etched into meteor shards',
                'pilots who navigate by taste and memory'
            ),
            'romance' => array(
                'a letter folded into a paper star',
                'coffee that stays warm whenever a promise is honest',
                'dances practiced in empty rooms',
                'sunsets that linger when someone waits'
            ),
            'history' => array(
                'streets paved over older stories',
                'ink that stains fingers and timelines',
                'crowds gathering for speeches that changed almost everything',
                'archives that breathe dust and patience'
            ),
        );

        $themeKey = strtolower($genre);
        if (isset($hooks[$themeKey])) {
            $pool = $hooks[$themeKey];
        } else {
            // Flatten all hook arrays if genre not matched.
            $pool = array();
            foreach ($hooks as $arr) {
                $pool = array_merge($pool, $arr);
            }
        }

        $intro = array(
            "In \"$title\", the rhythm of the day keeps changing shape.",
            "The story of \"$title\" bends quietly, refusing to repeat itself.",
            "Every chapter of \"$title\" moves like a slow tide across an unseen shore.",
            "\"$title\" keeps circling back to the choice no one wanted to name.",
        );

        $tones = array(
            'The voice stays close to the characters, observing with patient curiosity.',
            'Small details become signals: a chipped mug, a window left barely open.',
            'Moments of calm always feel earned, as if the world needs proof of kindness.',
            'There is always a hint of tension, as though the horizon is listening.',
            'Humor shows up in shy places, making the tougher scenes breathe.',
            'Memory keeps interrupting the present, adding weight to every step.',
        );

        $progress = array(
            "Midway through the journey, old plans fray and improvisation feels like a new language.",
            "The narrative trusts the reader to notice how echoes change each time they return.",
            "Every decision ripples outward, pulling background characters into sudden focus.",
            "Time stretches, carrying the cast toward a convergence they do not yet see.",
        );

        $closing = array(
            "What began as a rumor becomes a choice that reshapes the map.",
            "By the end, the setting feels earned, a place built by the characters' honesty.",
            "Resolution arrives more like a sunrise than a door slamming shut.",
            "Nothing ends completely; it simply holds still long enough to understand.",
        );

        $paragraphs = array();
        $paragraphs[] = $intro[$page % count($intro)];
        $paragraphs[] = $pool[$page % count($pool)];
        $paragraphs[] = $tones[$page % count($tones)];

        if ($page > 1 && $page < $totalPages) {
            $paragraphs[] = $progress[$page % count($progress)];
        } elseif ($page === $totalPages) {
            $paragraphs[] = $closing[$page % count($closing)];
        } else {
            $paragraphs[] = $tones[($page + 1) % count($tones)];
        }

        $extraLines = array();
        $baseLine = "On page {$page} of {$totalPages}, {$title} leans into {$genre}, letting each scene breathe.";
        for ($i = 0; $i < 4; $i++) {
            $extraLines[] = self::buildColorSentence($title, $genre, $author, $page, $totalPages, $pool);
        }

        $text = implode(' ', array_merge($paragraphs, array($baseLine), $extraLines));

        // Ensure roughly 300–400 words by padding with reflective observations.
        $wordCount = str_word_count($text);
        $target = mt_rand(300, 400); // mt_rand instead of random_int for compatibility

        while ($wordCount < $target) {
            $text .= ' ' . self::buildColorSentence($title, $genre, $author, $page, $totalPages, $pool);
            $wordCount = str_word_count($text);
        }

        return trim($text);
    }

    /**
     * Generate a single vivid sentence using comic context.
     *
     * @param string $title
     * @param string $genre
     * @param string $author
     * @param int    $page
     * @param int    $totalPages
     * @param array  $pool
     * @return string
     */
    protected static function buildColorSentence($title, $genre, $author, $page, $totalPages, $pool)
    {
        $beats = array(
            'The narrator keeps noticing the way light moves across ordinary rooms.',
            'Footsteps echo like punctuation, marking the beats between choices.',
            'Each conversation is laced with small risks that no one fully names.',
            'The setting adapts, bending to match the temper of its visitors.',
            'A quiet humor threads through the tension, inviting the reader to lean closer.',
            'Every pause feels intentional, the breath before a new turn.',
            'What seemed permanent becomes temporary once imagination interferes.',
            'Shadows stretch, measuring how far the characters have come.',
        );

        $fragment = $pool[array_rand($pool)];
        $beat = $beats[array_rand($beats)];

        if (!empty($author)) {
            $authorNote = "The world feels like something {$author} would sketch in the margins.";
        } else {
            $authorNote = 'The world feels freshly invented, shaped by whoever is paying attention.';
        }

        return $beat . ' ' . $fragment . '. ' . $authorNote .
            " Page {$page} of {$totalPages} keeps widening the frame for \"{$title}\" as a {$genre} tale.";
    }
}