<?php
require_once("./Voltorbflip.php");

function printBoard($board)
{
    foreach (range(0, 4) as $i) {
        foreach (range(0, 4) as $j) {
            echo " " . $board[$i][$j] . " ";
        }
        echo "\n";
    }
    echo "\n";
}

$game = new Voltorbflip();
printBoard($game->getResult());
$game->flip([0, 2]);
$game->flip([1, 4]);
printBoard($game->getBoard());
print($game->getScore());