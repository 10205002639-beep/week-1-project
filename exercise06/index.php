<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Challenge: returns ["isValid" => bool, "errors" => string[]].
function validateProfile(array $profile): array
{
    $errors = [];

    // Decision: no early return, so the user sees EVERY problem at once.
    $name = trim((string) ($profile["name"] ?? ""));
    if ($name === "" || mb_strlen($name) < 3) {
        $errors[] = "Name is required and must contain at least 3 characters.";
    }

    $age = $profile["age"] ?? null;
    if (!is_int($age) || $age < 0 || $age > 120) {
        $errors[] = "Age must be a whole number between 0 and 120.";
    }

    $dzongkhag = trim((string) ($profile["dzongkhag"] ?? ""));
    if ($dzongkhag === "") {
        $errors[] = "Dzongkhag is required.";
    }

    $cid = (string) ($profile["cid"] ?? "");
    if (strlen($cid) !== 11) {
        $errors[] = "CID must contain exactly 11 characters.";
    }

    $status = $profile["status"] ?? "";
    if (!in_array($status, ["Active", "Inactive"], true)) {
        $errors[] = "Status must be Active or Inactive.";
    }

    return ["isValid" => count($errors) === 0, "errors" => $errors];
}

$testProfiles = [
    "Valid profile" => [
        "name" => "Pema Dorji", "age" => 24, "dzongkhag" => "Thimphu",
        "cid" => "10000000001", "status" => "Active",
    ],
    "Invalid profile A (several problems)" => [
        "name" => "Al", "age" => 150, "dzongkhag" => "",
        "cid" => "123", "status" => "Pending",
    ],
    "Invalid profile B (missing name, text age)" => [
        "name" => "", "age" => "twenty", "dzongkhag" => "Paro",
        "cid" => "10000000002", "status" => "inactive",
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 6 - Profile Validation Engine</title>
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
<h1>Exercise 6: Profile Validation Engine</h1>
<?php foreach ($testProfiles as $label => $profile): ?>
    <?php $result = validateProfile($profile); ?>
    <section>
        <h2><?= e($label) ?></h2>
        <?php if ($result["isValid"]): ?>
            <p class="ok">Profile is valid</p>
        <?php else: ?>
            <p class="warn"><?= count($result["errors"]) ?> error(s) found:</p>
            <ul>
                <?php foreach ($result["errors"] as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</body>
</html>
