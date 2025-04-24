<?php

namespace TomatoPHP\FilamentMediaManager\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\FileAdderFactory;

class Media extends \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    use SoftDeletes;
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($media) {
            $user = Auth::user();
            $client = User::getActiveClient();

            if ($user && $client) {
                $media->user_id = $user->id;
                $media->client_id = $client->id;
            }
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope('folder', function (Builder $query) {
            $folder = Folder::find(session()->get('folder_id'));
            if($folder){
                if(!$folder->model_type){
                    $query->where('collection_name', $folder->collection);
                }
                else {
                    $query
                        ->where('model_type', $folder->model_type)
                        ->where('model_id', $folder->model_id)
                        ->where('collection_name', $folder->collection);
                }
            }
        });
    }
}
