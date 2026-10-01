<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

const PROCESSING_CHARGE = 20.00;
const DISCOUNT_RATE = 0.10;
const DISCOUNT_MIN_QUANTITY = 5;

$serviceFees = [
    "Certificate"  => 100,
    "Verification" => 50,
    "Replacement"  => 200,
    "Correction"   => 75,
];

// Challenge: return every part of the calculation as an associative array.
function calculateServiceFee(array $serviceFees, string $service, int $quantity): array
{
    // Decision: validate first and return an error key instead of printing here,
    // so the calculation stays separate from the display.
    if (!array_key_exists($service, $serviceFees)) {
        return ["error" => "Unknown service: $service."];
    }
    if ($quantity < 1) {
        return ["error" => "Quantity must be at least 1 (received $quantity)."];
    }

    $unitFee = (float) $serviceFees[$service];
    $cost = $unitFee * $quantity;
    $discount = $quantity >= DISCOUNT_MIN_QUANTITY ? $cost * DISCOUNT_RATE : 0.0;
    $finalAmount = $cost - $discount + PROCESSING_CHARGE;

    return [
        "service"    => $service,
        "unitFee"    => $unitFee,
        "quantity"   => $quantity,
        "cost"       => $cost,
        "discount"   => $discount,
        "processing" => PROCESSING_CHARGE,
        "final"      => $finalAmount,
    ];
}

function money(float $amount): string
{
    return "Nu. " . number_format($amount, 2);
}

$orders = [
    ["service" => "Certificate",  "quantity" => 2],
    ["service" => "Verification", "quantity" => 5],
    ["service" => "Replacement",  "quantity" => 10],
    ["service" => "Passport",     "quantity" => 1],
    ["service" => "Correction",   "quantity" => 0],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 7 - Service Fee Calculator</title>
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
<h1>Exercise 7: Service Fee Calculator</h1>
<?php foreach ($orders as $order): ?>
    <?php $bill = calculateServiceFee($serviceFees, $order["service"], $order["quantity"]); ?>
    <section>
        <h2><?= e($order["service"]) ?> x <?= $order["quantity"] ?></h2>
        <?php if (isset($bill["error"])): ?>
            <p class="warn">Error: <?= e($bill["error"]) ?></p>
        <?php else: ?>
            <table>
                <tr><th>Service</th><td><?= e($bill["service"]) ?></td></tr>
                <tr><th>Unit fee</th><td><?= money($bill["unitFee"]) ?></td></tr>
                <tr><th>Quantity</th><td><?= $bill["quantity"] ?></td></tr>
                <tr><th>Cost before discount</th><td><?= money($bill["cost"]) ?></td></tr>
                <tr><th>Discount</th><td>- <?= money($bill["discount"]) ?></td></tr>
                <tr><th>Processing charge</th><td><?= money($bill["processing"]) ?></td></tr>
                <tr><th>Final amount</th><td><strong><?= money($bill["final"]) ?></strong></td></tr>
            </table>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</body>
</html>
