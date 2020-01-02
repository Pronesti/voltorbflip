<?php
session_start();
if(empty($_SESSION['status'])){
    //create game
    $_SESSION['level'] = 1;
    $board = array();
    $voltorbs=5 + $_SESSION['level'];
    $bonus = random_int(9,11) + (($_SESSION['level'] - 1) * 2);
    $multipliers = array();
    $_SESSION['stop'] = "";

    foreach(range(0,4) as $fila => $a){
        $board[$fila] = array();
        foreach(range(0,4) as $columna => $b){
                $board[$fila][$columna] = "?";
        }
    }
    $_SESSION['board'] = $board;

    foreach(range(0,4) as $fila => $a){
        $board[$fila] = array();
        foreach(range(0,4) as $columna => $b){
                $board[$fila][$columna] = 1;
        }
    }

    while($voltorbs > 0){
        $fila_random=random_int(0,4);
        $columna_random=random_int(0,4);
        if($board[$fila_random][$columna_random] != "V"){
            $board[$fila_random][$columna_random] = "V";
            $voltorbs = $voltorbs - 1;
        }
    }

    while ($bonus > 0){
        $random_number = random_int(2,3);
        if (($bonus - $random_number) >= 0){
            $multipliers[] = $random_number;
            $bonus = $bonus - $random_number;
        }
        if ($bonus == 1){
            $multipliers[] = 1;
            $bonus = $bonus - 1;
        }
    }

    while(count($multipliers) > 0){
        $fila_random=random_int(0,4);
        $columna_random=random_int(0,4);
        if($board[$fila_random][$columna_random] === 1){
            $board[$fila_random][$columna_random] = array_pop($multipliers);
        }
    }

    $_SESSION['status'] = 'playing';
    $_SESSION['result'] = $board;
}else{
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GiraVoltorb</title>
    <link rel="stylesheet" href="/index.css" type="text/css"></link>
</head>
<body>
<div class="game">
<?php
    echo "<h1 class='title'>";
    echo $_SESSION['status'];
    if ($_SESSION['status'] == 'playing'){
        echo " on level " . $_SESSION['level'];
    } 
    echo "</h1>";

?>
<form action='index.php' method='post'>
<?php
    foreach(range(0,4) as $fila => $a){
        foreach(range(0,4) as $columna => $b){
            if($_SESSION['board'][$fila][$columna] == "V"){
                $color = "rojo";
            }elseif($_SESSION['board'][$fila][$columna] > 1){
                $color = "verde";
            }else{
                $color = "negro";
            }
            echo "<button class='botonTablero $color' name='play' value='$fila,$columna'>";
            echo $_SESSION['board'][$fila][$columna];
            echo "</button>";
        }
        echo "<button class='botonInfo' disabled>";
        echo "V: " . count(array_filter($_SESSION['result'][$fila], function($v){
            return $v == "V";
        }));
        echo " N: " . array_sum(array_filter($_SESSION['result'][$fila], function($v){
            return is_int($v);
        }));
        echo "</button>";
        echo "</br>";
    }
    for($i=0;$i<5;$i=$i+1){
        $numeros = array();
        $n_voltorbs = 0;
        for ($j=0; $j < 5; $j++) { 
            if($_SESSION['result'][$j][$i] == "V"){
                $n_voltorbs++;
            }else{
                $numeros[] = $_SESSION['result'][$j][$i];
            }
        }
        echo "<button class='botonInfoAbajo' disabled>";
        echo "V: " . $n_voltorbs . " N: " . array_sum($numeros);
        echo "</button>";
    }
    //echo "<button class='botonInfoExtra' disabled></button>";
    echo "<button class='botonInfoExtra' disabled> Coins:  ";
    echo checkBoard();
    echo "</boton>";
    ?>
    </br>
    <button class="botonAccion <?=$_SESSION['mark'] ? "marking" : "" ?>" name='mark' value='mark' <?=$_SESSION['stop']?>><?=$_SESSION['mark'] ? "MARKING" : "MARK" ?></button>
    <button class="botonAccion" name='stop' value='stop' <?=$_SESSION['stop']?>>STOP</button>
    <button class="botonAccion" name='restart' value='restart'>RESTART</button>
    </form>
</div>
</body>
</html>

<?php
if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_POST['stop'])){
        $_SESSION['stop'] = "disabled";
        $final_score = checkBoard();
        $_SESSION['board'] = $_SESSION['result'];
        Header('Location: '.$_SERVER['PHP_SELF']);    
        Exit(); //optional
    }elseif(isset($_POST['restart'])){
        $_SESSION['status'] = array();
        $_SESSION['mark'] = false;
        Header('Location: '.$_SERVER['PHP_SELF']);
        Exit(); //optional
    }elseif(isset($_POST['mark'])){
        $_SESSION['mark'] = !$_SESSION['mark'];
        Header('Location: '.$_SERVER['PHP_SELF']);
        Exit(); //optional
    }else{
        $button_click = (str_split($_POST['play']));
        if($_SESSION['mark'] == true && $_SESSION['stop'] != "disabled"){
            if($_SESSION['board'][$button_click[0]][$button_click[2]] == "?"){
                $_SESSION['board'][$button_click[0]][$button_click[2]] = "V";
            }elseif($_SESSION['board'][$button_click[0]][$button_click[2]] == "V"){
                $_SESSION['board'][$button_click[0]][$button_click[2]] = "?";
            }
        }elseif($_SESSION['stop'] != "disabled"){
            if($_SESSION['result'][$button_click[0]][$button_click[2]] == "V"){
                $_SESSION['stop'] = "disabled";
                $_SESSION['status'] = "Lost";
                $_SESSION['board'] = $_SESSION['result'];
            }else{
                $_SESSION['board'][$button_click[0]][$button_click[2]] = $_SESSION['result'][$button_click[0]][$button_click[2]];
            }
        }
        Header('Location: '.$_SERVER['PHP_SELF']);
        Exit(); //optional
    }
}
function checkBoard(){
    $tiene_uno = false;
    $total = 1;
    $multi = array();
    foreach (range(0,4) as $i => $v_i) {
        foreach(range(0,4) as $j => $v_j){
            if($_SESSION['board'][$i][$j] != "V" && $_SESSION['board'][$i][$j] != "?"){
                if($_SESSION['board'][$i][$j] == 1 && !$tiene_uno){
                $tiene_uno = true;
                $multi[] = $_SESSION['board'][$i][$j];
                }elseif($_SESSION['board'][$i][$j] != 1 ){
                    $multi[] = $_SESSION['board'][$i][$j];
                }
            }
        }
    }
    //echo implode(",",$multi);
    foreach($multi as $v){
        $total = $total * $v;
    }
    if($_SESSION['status'] == "Lost"){
        return -1;
    }else{
        if($tiene_uno || count($multi)>0){
            return $total;
        }else{
            return 0;
        }
    }
    
}
?>