<?php
Class Voltorbflip
{
    private $board;
    private $result;

    private $status;
    private $finalScore;

    private $level=1;
    private $voltorbs;
    private $multipliers;


    public function __construct()
    {
        $this->createBoard();
        $this->status = 'playing';
    }

    //getters

    public function getBoard(){
        return $this->board;
    }

    public function getResult(){
        return $this->result;
    }
    
    public function getStatus(){
        return $this->status;
    }

    public function getScore(){
        return $this->checkBoard();
    }

    public function getLevel(){
        return $this->level;
    }

    public function getVoltorbs(){
        return $this->voltorbs;
    }

    public function getMultipliers(){
        return $this->multipliers;
    }

    //setters
    
    public function setStatus($newStatus){
       $this->status = $newStatus;
    }

    public function setLevel($newLevel){
        $this->level = $newLevel;
    }

    public function createBoard(){
        $this->board = array();
        $this->voltorbs = 5 + $this->level;
        $bonus = random_int(9, 11) + (($this->level - 1) * 2);
        $this->multipliers = array();

        foreach (range(0, 4) as $fila => $a) {
            $this->board[$fila] = array();
            foreach (range(0, 4) as $columna => $b) {
                $this->board[$fila][$columna] = "?";
            }
        }
        $this->result = $this->board;

        foreach (range(0, 4) as $fila => $a) {
            $this->result[$fila] = array();
            foreach (range(0, 4) as $columna => $b) {
                $this->result[$fila][$columna] = 1;
            }
        }

        while ($this->voltorbs > 0) {
            $fila_random = random_int(0, 4);
            $columna_random = random_int(0, 4);
            if ($this->result[$fila_random][$columna_random] != "V") {
                $this->result[$fila_random][$columna_random] = "V";
                $this->voltorbs = $this->voltorbs - 1;
            }
        }

        while ($bonus > 0) {
            $random_number = random_int(2, 3);
            if (($bonus - $random_number) >= 0) {
                $this->multipliers[] = $random_number;
                $bonus = $bonus - $random_number;
            }
            if ($bonus == 1) {
                $this->multipliers[] = 1;
                $bonus = $bonus - 1;
            }
        }

        while (count($this->multipliers) > 0) {
            $fila_random = random_int(0, 4);
            $columna_random = random_int(0, 4);
            if ($this->result[$fila_random][$columna_random] === 1) {
                $this->result[$fila_random][$columna_random] = array_pop($this->multipliers);
            }
        }
    }

    public function flip($button_click)
    {
        if ($this->result[$button_click[0]][$button_click[1]] == "V") {
            $this->stop = true;
            $this->status = "Lost";
            //$this->board = $this->result;
        } else {
            $this->board[$button_click[0]][$button_click[1]] = $this->result[$button_click[0]][$button_click[1]];
        }
    }

    public function mark($button_click)
    {
        if ($this->board[$button_click[0]][$button_click[1]] == "?") {
            $this->board[$button_click[0]][$button_click[1]] = "V";
        } elseif ($this->board[$button_click[0]][$button_click[1]] == "V") {
            $this->board[$button_click[0]][$button_click[1]] = "?";
        }
    }

    public function stop()
    {
        $this->finalScore = checkBoard();
        $this->status = 'stopped';
    }

    public function restart()
    {
        $_SESSION['status'] = array();
        $_SESSION['mark'] = false;
    }

    public function checkBoard()
    {
        $tiene_uno = false;
        $total = 1;
        $multi = array();
        foreach (range(0, 4) as $i => $v_i) {
            foreach (range(0, 4) as $j => $v_j) {
                if ($this->board[$i][$j] != "V" && $this->board[$i][$j] != "?") {
                    if ($this->board[$i][$j] == 1 && !$tiene_uno) {
                        $tiene_uno = true;
                        $multi[] = $this->board[$i][$j];
                    } elseif ($this->board[$i][$j] != 1) {
                        $multi[] = $this->board[$i][$j];
                    }
                }
            }
        }
        //echo implode(",",$multi);
        foreach ($multi as $v) {
            $total = $total * $v;
        }
        if ($this->status == "Lost") {
            return -1;
        } else {
            if ($tiene_uno || count($multi) > 0) {
                return $total;
            } else {
                return 0;
            }
        }
    }
}
?>