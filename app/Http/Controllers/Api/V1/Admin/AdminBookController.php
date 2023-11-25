<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Image;

class AdminBookController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $books = Book::paginate(5);
            $this->paging = true;
            return $this->sendResponse(
                BookResource::collection($books),
                true
            );
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Create book
     * @param  BookRequest $request
     */
    public function store(BookRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['image'] =  [
            'thumbnail' => $this->processImage($request->file('image'), 'book_image/thumbnail', 75, Book::$imageSize['thumbnail']),
            'medium' => $this->processImage($request->file('image'), 'book_image/medium', 75, Book::$imageSize['medium']),
            'large' => $this->processImage($request->file('image'), 'book_image/large', 75, Book::$imageSize['large']),
        ];
        $book = Book::create($input);
        return $this->sendResponse(
            new BookResource($book),
            'Book Created Successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): JsonResponse
    {
        try {
            return $this->sendResponse(
                new BookResource($book)
            );
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, Book $book)
    {
        try {
            $input = $request->all();
            $this->upsertImage($request, $book, $input);
            $book->update($input);
            return $this->sendResponse(
                new BookResource($book),
                'Book Updated Successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        try {
            $this->deleteImage($book);
            $book->delete();
            return $this->sendResponse(
                [],
                'Book Deleted Successfully',
                204
            );
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Upsert image
     */
    private function upsertImage(BookRequest $request, Book $book, array &$input): void
    {
        if ($request->file()) {
            $input['image'] =  [
                'thumbnail' => $this->processImage($request->file('image'), 'book_image/thumbnail', 75, Book::$imageSize['thumbnail']),
                'medium' => $this->processImage($request->file('image'), 'book_image/medium', 75, Book::$imageSize['medium']),
                'large' => $this->processImage($request->file('image'), 'book_image/large', 75, Book::$imageSize['large']),
            ];
            $this->deleteImage($book);
        }
    }

    /**
     * Delete image
     */
    private function deleteImage(Book $book): void
    {
        if (!empty($book->image)) {
            Storage::delete(
                array_map(function ($path) {
                    return 'public/' . $path;
                }, array_values($book->image ?? []))
            );
        }
    }
    /**
     * Process image
     */
    private function processImage(mixed $file, string $folder, int $compressLevel = 75, array $size = array()): string
    {
        //Create image
        $image = Image::make($file);

        //Resize image
        if (!empty($size)) {
            $image->resize($size[0], $size[1], function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        //Compress Image
        $image->encode('jpg', $compressLevel);

        // Create hash value
        $hash = md5($image->__toString());

        // Prepare qualified image name
        $imagePath = $folder . '/' . $hash . ".jpg";

        //Save Image
        $savedImage = Storage::put('public/' . $imagePath, $image->__toString());

        if ($savedImage) {
            return $imagePath;
        } else {
            return '';
        }
    }
}
