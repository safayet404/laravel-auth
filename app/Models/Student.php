<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['first_name','last_name','email','dob','password'];
    protected $casts = ['dob' => 'date'];

    public function complianceProfiles() {
        return $this->hasMany(StudentComplianceProfile::class);
    }
    public function documents(){
        return $this->hasMany(StudentDocument::class);
    }
      public function interviews() { 
        return $this->hasMany(Interview::class); 
    }

}
