<?php

namespace App\Helpers;

use Illuminate\Support\Str;



class Helper
{

    //! File or Image Upload
    public static function fileUpload($file, string $folder): ?string
    {
        if (!$file || !$file->isValid()) return null;
        $originalName = $file->getClientOriginalName();
        $name = Str::uuid() . '-' . now()->format('Y-m-d-H-i-s');
        $path = $file->storeAs($folder, $name . '-' . $originalName, ['disk' => 'public']);
        return $path;
    }

    //! File or Image Delete
    public static function fileDelete(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    //! Generate Slug
    public static function makeSlug($model, string $title): string
    {
        $slug = Str::slug($title);
        while ($model::where('slug', $slug)->exists()) {
            $randomString = Str::random(5);
            $slug         = Str::slug($title) . '-' . $randomString;
        }
        return $slug;
    }
}
