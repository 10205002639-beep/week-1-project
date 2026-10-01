<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Challenge: one function returns every summary value in an associative array.
function summarizeMembers(array $members): array
{
    // Decision: guard against an empty list to avoid division by zero.
    if (count($members) === 0) {
        return ["count" => 0, "shortest" => "", "longest" => "", "average" => 0.0];
    }

    $shortest = $members[0];
    $longest = $members[0];
    $totalLength = 0;

    foreach ($members as $name) {
        $length = strlen($name);
        $totalLength += $length;
        if ($length < strlen($shortest)) {
            $shortest = $name;
        }
        if ($length > strlen($longest)) {
            $longest = $name;
        }
    }

    return [
        "count"    => count($members),
        "shortest" => $shortest,
        "longest"  => $longest,
        "average"  => $totalLength / count($members),
    ];
}

$members = ["Pema Dorji", "Sonam Choden", "Tashi", "Karma Wangmo", "Dechen", "Jigme Namgyel Wangchuk"];
$summary = summarizeMembers($members);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 3 - Household Member Summary</title>
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
<h1>Exercise 3: Household Member Summary</h1>

<h2>Members</h2>
<ol>
    <?php foreach ($members as $name): ?>
        <li><?= e($name) ?></li>
    <?php endforeach; ?>
</ol>

<p>Total members: <strong><?= count($members) ?></strong></p>
<p>First: <strong><?= e($members[0]) ?></strong> | Last: <strong><?= e($members[count($members) - 1]) ?></strong></p>

<h2>Names with character counts</h2>
<table>
    <tr><th>Name</th><th>Characters</th></tr>
    <?php foreach ($members as $name): ?>
        <tr><td><?= e($name) ?></td><td><?= strlen($name) ?></td></tr>
    <?php endforeach; ?>
</table>

<h2>Summary</h2>
<ul>
    <li>Member count: <?= $summary["count"] ?></li>
    <li>Shortest name: <?= e($summary["shortest"]) ?> (<?= strlen($summary["shortest"]) ?> characters)</li>
    <li>Longest name: <?= e($summary["longest"]) ?> (<?= strlen($summary["longest"]) ?> characters)</li>
    <li>Average name length: <?= number_format($summary["average"], 2) ?> characters</li>
</ul>
</body>
</html>
