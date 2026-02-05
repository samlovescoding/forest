<?php

namespace App\Roles;

class AdminRole extends Role
{
  public string $name = "Administrator";
  public string $slug = "admin";

  public function description()
  {
    return <<<MD
      Has website administration capabilities.
      This role should have permission to do everything.
    MD;
  }
}
