<?php

trait Movable
{
    private int $x = 0;
    private int $y = 0;
    protected int $fuel = 100;

    public function move(int $x, int $y): void
    {
        $dis = round(sqrt($x * $x + $y * $y));
        if($dis > $this->fuel)
            {
                echo "Not enough fuel!\n";
            }
        else
            {
                $this->x += $x;
                $this->y += $y;
                $this->fuel -= $dis;
                echo "The object has moved to the position ({$this->x}, {$this->y})\n";
            }
    }

    public function refueling(int $value): void
    {
        $this->fuel += $value;
    }

    public function getPosition(): array
    {
        return [$this->x, $this->y];
    }
}

interface CanMove{
    public function move(int $x, int $y): void;
    public function refueling(int $value): void;
    public function getPosition(): array;
}

class Car implements CanMove{
    use Movable;

    public function message(): void
    {
        echo "Good luck on your journey!";
    }
}

class SportCar extends Car{
    private bool $nitro = false;

    public function turnOn(): void
    {
        if ($this->nitro === false)
            {
                $this->nitro = true;
            }
        else
            {
                echo "Nitro is already running\n";
            }
    }

    public function turnOff(): void
    {
        if($this->nitro === true)
            {
                $this->nitro = false;
            }
        else
            {
                echo "Nitro has already stopped\n";
            }
    }

    public function move(int $x, int $y): void
    {
        if ($this->nitro === true)
            {
                $dis = round(sqrt($x * $x + $y * $y)) * 2;
                if($dis > $this->fuel)
                    {
                        echo "Not enough fuel!\n";
                    }
                else
                    {
                        parent::move($x * 2, $y * 2);
                    }
            }
        else
            {
                $dis = round(sqrt($x * $x + $y * $y));
                if($dis > $this->fuel)
                    {
                        echo "Not enough fuel!\n";
                    }
                else
                    {
                        parent::move($x, $y);
                    }
            }
    }
}

$car = new Car();
$sportsCar = new SportCar();
$sportsCar->move(5, 5);
$sportsCar->turnOn();
$sportsCar->move(5, 5);
$sportsCar->turnOff();
$sportsCar->move(5, 5);
$sportsCar->turnOff();
$sportsCar->move(5, 5);
?>