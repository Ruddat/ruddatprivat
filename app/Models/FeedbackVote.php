<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackVote extends Model
{
    protected $fillable = ['feedback_id', 'customer_id', 'upvote'];
}