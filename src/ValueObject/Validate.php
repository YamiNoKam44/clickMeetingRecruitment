<?php
declare(strict_types=1);


namespace App\ValueObject;


interface Validate
{
    public function validate(): void;
}
