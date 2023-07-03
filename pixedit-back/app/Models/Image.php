<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'link',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'link' => 'string',
        'user_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

     /**
     * Save the uploaded image locally and update the link column.
     *
     * @param UploadedFile $file
     * @param string $title
     * @param int|null $userId
     * @return Image
     */
    public static function saveImage(UploadedFile $file, string $title, ?int $userId = null): Image
    {
        $image = new Image();
        $image->title = $title;
        $image->user_id = $userId;

        // Save the image locally
        $path = $file->store('images', 'public');

        // Update the link column
        $image->link = $path;

        $image->save();

        return $image;
    }

}
