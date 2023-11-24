<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\ElasticsearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncBooksToElasticsearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $elasticsearchService = app(ElasticsearchService::class);

        // Fetch all books from the database
        $books = Book::all();

        // Loop through each book and index it in Elasticsearch
        foreach ($books as $book) {
            $elasticsearchService->indexDocument('books', $book->id, $book->toArray());
        }
    }
}
