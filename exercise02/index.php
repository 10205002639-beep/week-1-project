<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Challenge: return an associative array so callers choose how to display it.
function classifyCitizen(int $age): array
{
    // Decision: reject invalid ages first so later branches can trust the value.
    if ($age < 0 || $age > 120) {
        return [
            "ageGroup"    => "Invalid",
            "eligible"    => false,
            "explanation" => "Age $age is invalid; it must be between 0 and 120.",
        ];
    }

    if ($age < 13) {
        $group = "Child";
    } elseif ($age < 18) {
        $group = "Teenager";
    } elseif ($age < 60) {
        $group = "Adult";
    } else {
        $group = "Senior Citizen";
    }

    $eligible = $age >= 18;
    $explanation = $eligible
        ? "Age $age is 18 or above, so the citizen qualifies for the adult-only service."
        : "Age $age is below 18, so the citizen does not qualify for the adult-only service.";

    return ["ageGroup" => $group, "eligible" => $eligible, "explanation" => $explanation];
}

$citizens = [
    ["name" => "Tashi Wangmo", "age" => 8],
    ["name" => "Karma Tenzin", "age" => 15],
    ["name" => "Dechen Lham", "age" => 18],
    ["name" => "Jigme Norbu", "age" => 59],
    ["name" => "Kinley Dorji", "age" => 60],
    ["name" => "Ugyen Zangmo", "age" => 130],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 2 - Age and Service Eligibility</title>
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
<h1>Exercise 2: Age and Service Eligibility</h1>
<table>
    <tr><th>Name</th><th>Age</th><th>Age group</th><th>Adult-only service</th><th>Explanation</th></tr>
    <?php foreach ($citizens as $citizen): ?>
        <?php $result = classifyCitizen($citizen["age"]); ?>
        <tr>
            <td><?= e($citizen["name"]) ?></td>
            <td><?= $citizen["age"] ?></td>
            <td><?= e($result["ageGroup"]) ?></td>
            <td class="<?= $result["eligible"] ? 'ok' : 'warn' ?>">
                <?= $result["eligible"] ? "Eligible" : "Not eligible" ?>
            </td>
            <td><?= e($result["explanation"]) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
