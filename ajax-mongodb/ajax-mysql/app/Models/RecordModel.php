<?php

namespace App\Models;

use CodeIgniter\Model;

class RecordModel extends Model
{
    protected $table = 'records'; // MySQL table name
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'created_at'];
}
