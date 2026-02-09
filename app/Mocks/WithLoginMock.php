<?php

namespace App\Mocks;

trait WithLoginMock
{
  public function prefill()
  {
    $this->email = 'admin@forest.test';
    $this->password = 'helloworld';
  }
}
