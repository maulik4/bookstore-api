<?php

namespace App\Observers;

use App\Models\Book;
use App\Services\ElasticsearchService;

class BookObserver
{

    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }


    /**
     * Handle the Book "created" event.
     */
    public function created(Book $book): void
    {
        $this->elasticsearchService->indexDocument('books', $book->id, $book->toArray());
    }

    /**
     * Handle the Book "updated" event.
     */
    public function updated(Book $book): void
    {
        // Update book index
        $this->elasticsearchService->updateDocument('books', $book->id, $book->toArray());
    }

    /**
     * Handle the Book "deleted" event.
     */
    public function deleted(Book $book): void
    {
        //Delete book index
        $this->elasticsearchService->deleteDocument('books', $book->id);
    }

    /**
     * Handle the Book "restored" event.
     */
    public function restored(Book $book): void
    {
        //
    }

    /**
     * Handle the Book "force deleted" event.
     */
    public function forceDeleted(Book $book): void
    {
        //
    }
}
