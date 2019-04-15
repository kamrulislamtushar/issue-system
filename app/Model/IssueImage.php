<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class IssueImage extends Model
{

    protected $fillable = ['image', 'issue_id'];
    public function issue()
    {
      return  $this->belongsTo(Issue::class);
    }
}
