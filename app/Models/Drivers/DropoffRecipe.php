<?php

namespace App\Models\Drivers;

use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropoffRecipe extends Model
{
    use HasFactory;
    protected $fillable = [
        'dropoff_claim_id',
        'recipe_id',
        'pinpoint',
        'latitude',
        'longitude'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }

    public function dropoffRecipeItems()
    {
        return $this->hasMany(DropoffRecipeItem::class, 'dropoff_recipe_id', 'id');
    }
}
