<?php

function input($message = null)
{
    if ($message) {
        echo $message;
    }
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    return trim($line);
}

function printBoard($board, $message)
{
    system('clear');
    system('cls');

    echo $message;
    echo "\n__|A B C D E F G H I J\n";
    foreach($board as $i => $row) {
        echo str_pad(($i + 1), 2, "0",  STR_PAD_LEFT) . "|" . implode(" ", $row) . "\n";
    }
}

function buildBoard($showLabel = False)
{
    $coordinates = [];
    $board = [];
    
    for($i = 0; $i < 10; $i++) {
        $row = [];
        for($k = 0; $k < 10; $k++) {
            $row[] = "~";
        }
        $board[] = $row;
    }

    $orientationOptions = ['horizontal', 'vertical'];
    $ships = ['a', 'bb', 'ccc', 'dddd', 'eeeee', 'ffffff', 'ggggggg'];
    foreach($ships as $ship) {
        $availableCoords = [];
        $orientation = $orientationOptions[array_rand($orientationOptions)];

        if ($orientation == 'horizontal') {
            foreach($board as $y => $row) {
                $currentSequence = [];
                foreach($row as $x => $column) {
                    if ($board[$y][$x] == "~") {
                        $currentSequence[] = ([$x,$y]);
                    } else {
                        if (count($currentSequence) > 0) {
                            $currentSequence = [];
                        }
                    }
                    if (count($currentSequence) == strlen($ship)) {
                        $availableCoords[] = $currentSequence;
                        $currentSequence = [];
                    }
                }
            }
        } else {
            $rearrengedBoard = [];
    
            foreach ($board[0] as $x => $column) {
                $rearrengedRow = [];
                for ($y = 0; $y < 10; $y++) {
                    $rearrengedRow[] = $board[$y][$x];
                }
                $rearrengedBoard[] = $rearrengedRow;
            }

            foreach($rearrengedBoard as $y => $row) {
                $currentSequence = [];
                foreach($row as $x => $column) {
                    if ($rearrengedBoard[$y][$x] == "~") {
                        $currentSequence[] = ([$y,$x]);
                    } else {
                        if (count($currentSequence) > 0) {
                            $currentSequence = [];
                        }
                    }
                    if (count($currentSequence) == strlen($ship)) {
                        $availableCoords[] = $currentSequence;
                        $currentSequence = [];
                    }
                }
            }
        }

        $shipPlacement = $availableCoords[array_rand($availableCoords)];
    
        foreach ($shipPlacement as $coordinate){
            $board[$coordinate[1]][$coordinate[0]] = ($showLabel) ? $ship[0] : "~";
            $coordinates[] = implode(",", [ $coordinate[0], $coordinate[1] ]);
        }
    }

    return [ $board, $coordinates ];
}

$buildPlayerBoard = buildBoard(true);
$playerBoard = $buildPlayerBoard[0];
$allPlayerCoords = $buildPlayerBoard[1];

$buildComputerBoard = buildBoard();
$computerBoard = $buildComputerBoard[0];
$allComputerCoords = $buildComputerBoard[1];

$letterPos = 'ABCDEFGHIJ';
$playerTurn = true;
$pickedEnemyCoords = [];
$pickedPlayerCoords = [];
$computerFireAgain = false;
$runProgram = true;
$winner = "";

while ($runProgram)
{
    $currentTurn = ($playerTurn) ? "Player" : "Computer";
    $currentBoard = ($playerTurn) ? "Computer" : "Player";
    $message = $currentTurn . "'s turn! | Showing " . $currentBoard . "'s board";
    printBoard((($playerTurn)? $computerBoard : $playerBoard), $message);

    if (count($allPlayerCoords) == 0 || count($allComputerCoords) == 0) {
        $winner = "Game ended!" . ((count($allComputerCoords) == 0) ? " Player won!" : " Computer won!");
        $runProgram = false;
        continue;
    }

    if (!$playerTurn) {
        
        $pickingCoord = true;

        while ($pickingCoord) {

            if (!$computerFireAgain) {
                $cpuX = rand(0, 9);
                $cpuY = rand(0, 9);
            } else {
                $directionsToMove = ['up', 'down', 'left', 'right'];

                if ($cpuY == 9) {
                    unset($directionsToMove[array_keys($directionsToMove, "down")[0]]);
                }
                if ($cpuY == 0) {
                    unset($directionsToMove[array_keys($directionsToMove, "up")[0]]);
                }
                if ($cpuX == 0) {
                    unset($directionsToMove[array_keys($directionsToMove, "left")[0]]);
                }
                if ($cpuX == 9) {
                    unset($directionsToMove[array_keys($directionsToMove, "right")[0]]);
                }

                $directionToMove = $directionsToMove[array_rand($directionsToMove)];

                switch ($directionToMove) {
                    case 'up':
                        $cpuY--;
                        break;
                    case 'down':
                        $cpuY++;
                        break;
                    case 'right':
                        $cpuX++;
                        break;
                    case 'left':
                        $cpuX--;
                        break;
                }
            }

            if (!in_array([$cpuX,$cpuY], $pickedEnemyCoords)) {
                $pickingCoord = false;
            }
        }

        $positionText = implode(",", [ $letterPos[$cpuX], ($cpuY + 1) ]);
        echo (($computerFireAgain) ? "Firing again at " : "Firing at ") . $positionText;
        sleep(3);

        $outcome = 'Missed!';
        $coord = implode(",",[ $cpuX, $cpuY ]);
        if (in_array($coord, $allPlayerCoords)) {
            $outcome = "That's a hit!";
            $playerBoard[$cpuY][$cpuX] = 'X';
            unset($allPlayerCoords[array_keys($allPlayerCoords, $coord)[0]]);
            $playerTurn = false;
            $computerFireAgain = true;
        } else {
            $playerBoard[$cpuY][$cpuX] = 'O';
            $playerTurn = true; // false for Debug
            $computerFireAgain = false;
        }

        $pickedEnemyCoords[] = [$cpuX, $cpuY];

        printBoard($playerBoard, $outcome);

        input("Press any key to continue."); // Comment this for Debug
    } else {
        $input = input("Pick a coordinate (letter,number): ");

        if ($input == "quit") {
            $runProgram = false;
            echo "\nBye";
        }

        if (strlen($input) == 0 || !strpos($input, ",") || count(explode(",", $input)) != 2) {
            continue;
        } else {
            $option = explode(",", $input);
            $posX = strpos($letterPos, strtoupper($option[0]));
            $posY = intval($option[1]);

            if (!is_numeric($posX)|| !is_numeric($posY)){
                echo "Try again 1 $posX $posY\n";
            } elseif (is_numeric($posY) && ($posY < 1 || $posY > 10)){
                echo "Try again 2 $posX $posY\n";
            } else {
                $posY -= 1;

                if (in_array([$posX, $posY], $pickedPlayerCoords)) {
                    continue;
                }

                echo "Firing at " . strtoupper($option[0]) . "," . $option[1] . "...";
                sleep(3);

                $outcome = "Missed!";
                $coord = implode(",", [$posX, $posY]);

                if (in_array($coord, $allComputerCoords)) {
                    $outcome = "That's a hit! Get ready to firing again!";
                    $computerBoard[$posY][$posX] = 'X';
                    unset($allComputerCoords[array_keys($allComputerCoords, $coord)[0]]);
                    $playerTurn = true;
                } else {
                    $computerBoard[$posY][$posX] = 'O';
                    $playerTurn = false;
                }

                $pickedPlayerCoords[] = [$posX, $posY];

                printBoard($computerBoard, $outcome);
                
                input("Press any key to continue.");
            }
        }
    }
}

echo "\n" . $winner;