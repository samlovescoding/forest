<?php

namespace App\Roles;

abstract class Role
{
  abstract public string $name { get; }

  abstract public string $slug { get; }

  abstract public function description();
}
