<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
class Issue extends Model
{


    protected  $fillable = [
      'title',
      'description',
      'user_id'
    ];
    public function user()
    {
       return $this->belongsTo(User::class);
    }
    public function issueImages()
    {
      return  $this->hasMany(IssueImage::class);
    }

}
