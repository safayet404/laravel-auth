<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceMessage extends Model
{
    protected $fillable = ['interview_id','author_user_id','message'];

    public function interview(){
        return $this->belongsTo(Interview::class);
    }

    public function author(){
        return $this->belongsTo(User::class,'author_user_id');
    }
}
