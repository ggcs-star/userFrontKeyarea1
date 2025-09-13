<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Project extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'projects';

    protected $fillable = [
        'id', 'name', 'location', 'type', 'area', 'price', 'image', 'imageRera',
        'founderDetails', 'status', 'url', 'reel', 'brochure',
        'images', 'buyProperties', 'sellProperties',
        'floorPlans', 'galleryImages', 'amenities',
        'aboutBuilder', 'projectFeatures',
        'unitBookingStatus', 'blockBookingDetails'
    ];
}
