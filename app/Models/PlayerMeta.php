<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerMeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'playermeta';

    protected $fillable = [
        'uid', 'meta_key', 'meta_value'
    ];

    // Assuming 'uid' is the foreign key for the user ID from the registration

    /**
     * Get the user associated with the PlayerMeta.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
