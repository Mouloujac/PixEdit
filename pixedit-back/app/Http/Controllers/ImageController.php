<?php

namespace App\Http\Controllers;

use Intervention\Image\Facades\Image as InterventionImage;
use App\Http\Requests\ImageStoreRequest;
use App\Http\Requests\ImageUpdateRequest;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index(Request $request): ImageCollection
    {
        $images = Image::all();

        return new ImageCollection($images);
    }

    public function store(ImageStoreRequest $request): ImageResource
{
    $validatedData = $request->validated();
    $imageFile = $request->file('image');
    $fileName = $validatedData['title'] . '.' . $imageFile->getClientOriginalExtension();
    $width = $request->input('cropWidth');
    $height = $request->input('cropHeight');
    $userId = $request->input('userId');
    $x = $request->input('x');
    $y = $request->input('y');
    // Définir les dimensions souhaitées
    $desiredHeight = $request->input('desired_height');
    $desiredWidth = $request->input('desired_width');

    // Créer une instance du modèle Laravel Image
    $image = new Image();
    $image->title = $validatedData['title'];
    $image->link = $fileName;
    $image->user_id = $userId;
    // Enregistrer l'image dans la base de données
    $image->save();

    // Utiliser Intervention\Image pour manipuler et sauvegarder l'image
    $imageInstance = InterventionImage::make($imageFile);
    
    if ($x && $y && $width && $height){
          
    $imageInstance->crop($width, $height, $x, $y);
    };
    
    if ($desiredWidth && $desiredHeight) {
        // Effectuer le redimensionnement de l'image
        $imageInstance->resize($desiredWidth, $desiredHeight);
    }
    
    // Sauvegarder l'image dans le stockage
    $imageInstance->save(storage_path('app/public/' . $fileName));

    // Le reste de votre code pour renvoyer la réponse appropriée

    return new ImageResource($image);
}


  
    public function show(Request $request, Image $image): ImageResource
    {
        return new ImageResource($image);
    }

    
    public function update(ImageUpdateRequest $request, Image $image): ImageResource
    {
        $validatedData = $request->validated();
        $imageName = $request->input('link'); 
        $imageTitle = $request->input('title');
        $width = $request->input('cropWidth');
        $height = $request->input('cropHeight');
        $imageUpdate = $request->input('imageUpdate');
        $x = $request->input('x');
        $y = $request->input('y');
        $desiredHeight = $request->input('desired_height');
        $desiredWidth = $request->input('desired_width');
    
        // Supprimer l'ancienne image du stockage
        $oldImagePath = storage_path('app/public/' . $imageUpdate);
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        // Télécharger l'image à partir de l'URL et la sauvegarder dans le stockage
        $interventionImage = InterventionImage::make(public_path($imageUpdate));
        if ($desiredWidth && $desiredHeight) {
            // Effectuer le redimensionnement de l'image
            $interventionImage->resize($desiredWidth, $desiredHeight);
        }
        if ($x && $y && $width && $height){
          
        $interventionImage->crop($width, $height, $x, $y);
        };
        $image->title = $validatedData['title'];
        $image->link = $imageName;
        $interventionImage->save(storage_path('app/public/' . $imageName));
    
        $image->save();
    
        return new ImageResource($image);
    }
    




    public function destroy(Request $request, Image $image): Response
    {
        // Supprimer l'image du stockage
        $imagePath = storage_path('app/public/' . $image->link);
        if(file_exists($imagePath)) {
            unlink($imagePath);
        }

        $image->delete();

        return response()->noContent();
    }

    public function getImage($file)
    {
        $filePath = storage_path('app/public/' . $file);
        
        if (file_exists($filePath)) {
            return response()->file($filePath);
        } else {
            return response()->json(['error' => 'Fichier non trouvé.'], 404);
        }
    }

}
