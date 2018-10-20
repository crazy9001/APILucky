<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/17/2018
 * Time: 12:43 PM
 */

namespace App\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $table = 'comments';

    protected $softDelete = true;

    protected $fillable = ['fullName', 'avatar', 'contentMessage', 'typeGift', 'giftImage', 'status', 'userId'];
}