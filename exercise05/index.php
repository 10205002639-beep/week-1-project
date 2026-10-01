<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Challenge: reusable search that returns an array of matching profiles.
function searchProfiles(array $profiles, string $searchTerm): array
{
    $matches = [];
    $term = trim($searchTerm);

    // Decision: an empty term would match everything, so return no results.
    if ($term === "") {
        return $matches;
    }

    foreach ($profiles as $profile) {
        // stripos() is the case-insensitive version of strpos().
        if (stripos($profile["name"], $term) !== false) {
            $matches[] = $profile;
        }
    }
    return $matches;
}

function renderProfileTable(array $profiles): void
{
    echo '<table><tr><th>Name</th><th>Dzongkhag</th><th>Age</th><th>Status</th></tr>';
    foreach ($profiles as $profile) {
        echo '<tr><td>' . e($profile["name"]) . '</td><td>' . e($profile["dzongkhag"])
            . '</td><td>' . $profile["age"] . '</td><td>' . e($profile["status"]) . '</td></tr>';
    }
    echo '</table>';
}

$profiles = [
    ["name" => "Pema Dorji",     "dzongkhag" => "Thimphu",  "age" => 24, "status" => "Active"],
    ["name" => "Tashi Dorji",    "dzongkhag" => "Paro",     "age" => 31, "status" => "Active"],
    ["name" => "Sonam Choden",   "dzongkhag" => "Punakha",  "age" => 45, "status" => "Inactive"],
    ["name" => "Karma Wangmo",   "dzongkhag" => "Thimphu",  "age" => 19, "status" => "Active"],
    ["name" => "Dorji Tenzin",   "dzongkhag" => "Wangdue",  "age" => 62, "status" => "Inactive"],
    ["name" => "Kinley Lham",    "dzongkhag" => "Bumthang", "age" => 28, "status" => "Active"],
    ["name" => "Choki Wangchuk", "dzongkhag" => "Trashigang", "age" => 37, "status" => "Active"],
    ["name" => "Dechen Pelden",  "dzongkhag" => "Haa",      "age" => 52, "status" => "Inactive"],
];

// Three scenarios: several matches (mixed case), one match, no match.
$searchTerms = ["DORJI", "choden", "xyz"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 5 - B-PIS Record Search</title>
<style>
body{font-family:system-ui,sans-serif;max-width:900px;margin:2rem auto;padding:0 1rem;line-height:1.5}
table{border-collapse:collapse;width:100%;margin:1rem 0}
th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}
th{background:#f2f2f2}
.warn{color:#b00020;font-weight:bold}
.ok{color:#0a7d2c;font-weight:bold}
section{border-top:2px solid #ddd;margin-top:1.5rem;padding-top:.5rem}
</style>
</head>
<body>
<h1>Exercise 5: B-PIS Record Search</h1>

<h2>All profiles (<?= count($profiles) ?>)</h2>
<?php renderProfileTable($profiles); ?>

<?php foreach ($searchTerms as $searchTerm): ?>
    <?php $results = searchProfiles($profiles, $searchTerm); ?>
    <section>
        <h2>Search: "<?= e($searchTerm) ?>"</h2>
        <p>Matching profiles: <strong><?= count($results) ?></strong></p>
        <?php if (count($results) === 0): ?>
            <p class="warn">No matching records found</p>
        <?php else: ?>
            <?php renderProfileTable($results); ?>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</body>
</html>
