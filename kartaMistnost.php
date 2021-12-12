<?php
require_once ("include/db_connect.php");

$state = "OK";

$roomId = filter_input(INPUT_GET, "room_id", FILTER_VALIDATE_INT);

if ($roomId === null) {
    http_response_code(400);
    $state = "BadRequest";
} else {
    $pdo = DB::connect();
    $query = "SELECT * FROM room WHERE room_id=:roomId";
    $query2 = $pdo->query("SELECT * FROM `key` WHERE room='$roomId'");
    $query3 = $pdo->query("SELECT * FROM employee");

    $stmt = $pdo->prepare($query);
    $stmt->execute(["roomId" => $roomId]);

    $stmt2 = $query2->fetchAll();
    $stmt3 = $query3->fetchAll();

    if ($stmt->rowCount() == 0) {
        http_response_code(404);
        $state = "NotFound";
    } else {
        $room = $stmt->fetch();
    }
}
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        a { text-decoration: none; }
        @media (min-width: 1200px) {
            .container{
            max-width: 500px;
            }
        }
    </style>
    <title><?php if($state == "OK"){ echo "Detail místosti " . $room->no; }?></title>
</head>
<body>
<div class="container">
<?php
$avgSelary = 0;
$num = 0;

if ($state === "OK") {
    echo "<h2>Místnost č. " . $room->no . "</h2>";

    echo "<table class='table table-hover'>";

    echo "<tbody>";
    echo "<tr><th>Číslo</th>" . "<td>" . $room->no . "</td>" . "</tr>";
    echo "<tr><th>Název</th>" . "<td>" . $room->name . "</td>" . "</tr>";
    echo "<tr><th>Telefon</th>" . "<td>" . ($room->phone ?: "&mdash;&mdash;") . "</td>" . "</tr>";
    echo "<tr><th>Lidé</th>";
    echo "<td>";
    foreach ($stmt3 as $value) {
        if($value->room == $roomId) {
            echo "<a href='kartaZamestnanec.php?employee_id={$value->employee_id}'>{$value->surname} {$value->name}</a><br>";
            $avgSelary += $value->wage;
            $num++;
        }
    }
    if ($num > 0) {
        $sellaryNum = ($avgSelary / $num);
    }else {
        $sellaryNum = 0;
        echo "&mdash;&mdash;";
    }
    echo "</td></tr>";
    echo "<tr><th>Průměrná mzda</th>" . "<td>" . number_format($sellaryNum, $decimals = 2, $decimal_separator = ".", $thousands_separator = ","
    ) . "</td>" . "</tr>";
    echo "<tr><th>Klíče</th>";
    echo "<td>";
    foreach ($stmt2 as $val1) {
        foreach ($stmt3 as $val2) {
            if ($val1->employee == $val2->employee_id){
                echo "<a href='kartaZamestnanec.php?employee_id={$val2->employee_id}'>{$val2->surname} {$val2->name}</a><br>";
            }
        }
    }
    echo "</td></tr>";
    echo "</tbody>";

    echo "<tfoot><tr><td><a href='mistnosti.php'>🢀 Zpět na seznam místností</a></td><td></td></tr></tfoot>";
    
} elseif ($state === "NotFound") {
    echo "<h2>Místnost nenalezena</h2>";

    echo "<table class='table table-hover'><tfoot><tr><td><a href='mistnosti.php'>🢀 Zpět na seznam místností</a></tr></td></tfoot></table>";
} elseif ($state === "BadRequest") {
    echo "<h2>Chybný požadavek</h2>";

    echo "<table class='table table-hover'><tfoot><tr><td><a href='mistnosti.php'>🢀 Zpět na seznam místností</a></tr></td></tfoot></table>";
}
?>
</div>
</body>
</html>