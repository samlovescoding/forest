<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class AppearanceForm extends Form
{
  public string $title = '';

  public string $slug = '';

  public string $type = '';

  public $source;

  public $person_id;

  public $film_id;

  public $show_id;

  public $season_id;

  public $episode_id;
}
