<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;

class Student extends Authenticatable
{
    use HasRoles;
    protected $fillable = ['first_name', 'last_name', 'email', 'dob', 'password'];
    protected $casts = ['dob' => 'date', 'password' => 'hashed'];
    protected $guard_name = 'student';
    public function complianceProfiles()
    {
        return $this->hasMany(StudentComplianceProfile::class);
    }
    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }
    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }
}
