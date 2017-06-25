<?php

class Seeder extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'seeds';

    /**
     * Fillable properties.
     *
     * @var array
     */
    protected $fillable = [
        'seed',
        'env',
        'batch',
    ];
}
