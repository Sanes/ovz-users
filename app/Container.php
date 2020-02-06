<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
  public function users()
  {
    return $this->belongsTo(User::class);
  }

    public function containers()
    {
    return $this->hasMany(Ip4address::class);
    }
}
