<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ip4address extends Model
{
  public function users()
  {
    return $this->belongsTo(Ip4address::class);
  }
}
