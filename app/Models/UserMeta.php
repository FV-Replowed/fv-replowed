<?php

// app\Models\UserMeta.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usermeta';

    protected $fillable = [
        'uid', 'firstName', 'lastName', 'xp', 'cash', 'gold', 'energyMax', 'energy', 'seenFlags', 'isNew', 'firstDay'
    ];

    protected $attributes = [
        'seenFlags' => 'a:1:{s:13:"ftue_complete";b:0;}',
    ];

    // Assuming 'uid' is the foreign key for the user ID from the registration

    /**
     * Get the user associated with the UserMeta.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
