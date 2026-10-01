<?php
declare(strict_types=1);

// Output helper: escape every value so browser output is safe.
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Challenge: reusable function that builds one labelled table row.
function displayProfileField(string $label, string $value): string
{
    return '<tr><th>' . e($label) . '</th><td>' . e($value) . '</td></tr>';
}

// Decision: an array of profiles lets me test a valid and an invalid case.
$profiles = [
    [
        "fullName"   => "Pema Dorji",
        "cid"        => "10000000001",
        "dob"        => "2001-04-12",
        "dzongkhag"  => "Thimphu",
        "gewog"      => "Chang",
        "occupation" => "Student",
        "isActive"   => true,
    ],
    [
        "fullName"   => "Sonam Choden",
        "cid"        => "12345",          // invalid: too short
        "dob"        => "1985-11-30",
        "dzongkhag"  => "Paro",
        "gewog"      => "Doteng",
        "occupation" => "Farmer",
        "isActive"   => false,
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 1 - Fictional Citizen Profile</title>
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
<h1>Exercise 1: Fictional Citizen Profile</h1>
<?php foreach ($profiles as $number => $profile): ?>
    <?php
    $cidLength = strlen($profile["cid"]);
    $status    = $profile["isActive"] ? "Active" : "Inactive";
    ?>
    <section>
        <h2>Profile <?= $number + 1 ?></h2>
        <table>
            <?= displayProfileField("Full name", $profile["fullName"]) ?>
            <?= displayProfileField("Name (uppercase)", mb_strtoupper($profile["fullName"])) ?>
            <?= displayProfileField("CID", $profile["cid"]) ?>
            <?= displayProfileField("CID length", (string) $cidLength . " characters") ?>
            <?= displayProfileField("Date of birth", $profile["dob"]) ?>
            <?= displayProfileField("Dzongkhag", $profile["dzongkhag"]) ?>
            <?= displayProfileField("Gewog", $profile["gewog"]) ?>
            <?= displayProfileField("Occupation", $profile["occupation"]) ?>
            <?= displayProfileField("Status", $status) ?>
        </table>
        <?php if ($cidLength !== 11): ?>
            <p class="warn">Warning: CID must contain exactly 11 characters (found <?= $cidLength ?>).</p>
        <?php else: ?>
            <p class="ok">CID length is valid.</p>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</body>
</html>
