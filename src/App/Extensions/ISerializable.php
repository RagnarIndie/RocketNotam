<?php

namespace App\Extensions;


interface ISerializable
{
    public function toArray(): array;
}