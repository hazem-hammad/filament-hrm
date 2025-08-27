<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $moduleName = 'Notifications';

    public function getModuleName(): string
    {
        return $this->moduleName;
    }
}
