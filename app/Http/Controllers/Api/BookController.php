<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendBookRequest;
use App\Http\Resources\Api\BookResource;
use App\Mail\Api\SendBookMail;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::active()->get();

        return response([
            'data' => [
                'books' => BookResource::collection($books),
                'result' => true
            ]]);
    }

    public function show($book)
    {
        $book = Book::active()->where('id', $book)->first();

        if(!$book){
            return response()->json(array(
                'data' => [
                'result' => false,
                'message'   =>  'Book not found',
                ]), 404);
        }

        return response([
            'data' => [
                'book' => BookResource::make($book),
                'result' => true
            ]]);
    }

    public function send(SendBookRequest $request, $book)
    {
        $book = Book::where('id', $book)->first();

        if(!$book){
            return response()->json(array(
                'data' => [
                'result' => false,
                'message'   =>  'Book not found',
                ]), 404);
        }

        $file = File::get($book->getMedia('book')->first()?->getPath());

        $data = [
            'name' => $book->name,
            'author' => $book->author,
            'title' => $book->title,
            'description' => $book->description,
            'price' => $book->price,
            'image' => $book->getMedia('image')->first()?->getUrl() ?? null,
            'book' => $book->getMedia('book')->first()?->getPath() ?? null,
        ];

        Mail::to($request->email)
            ->send(new SendBookMail($data));

        return response([
            'data' => [

                'result' => true
            ]]);
    }

    public function trialSend(SendBookRequest $request, $book)
    {

        $book = Book::where('id', $book)->first();

        if(!$book){
            return response()->json(array(
                'data' => [
                'result' => false,
                'message'   =>  'Book not found',
                ]), 404);
        }

        $file = File::get($book->getMedia('book')->first()?->getPath());

        $data = [
            'name' => $book->name,
            'author' => $book->author,
            'title' => $book->title,
            'description' => $book->description,
            'price' => $book->price,
            'image' => $book->getMedia('image')->first()?->getUrl() ?? null,
            'book' => $book->getMedia('book_trial')->first()?->getPath() ?? null,
        ];

        Mail::to($request->email)
            ->send(new SendBookMail($data));

        return response([
            'data' => [
                'result' => true
            ]]);
    }
}
