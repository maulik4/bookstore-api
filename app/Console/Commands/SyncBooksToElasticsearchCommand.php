<?php

namespace App\Console\Commands;

use App\Jobs\SyncBooksToElasticsearch;
use Illuminate\Console\Command;

class SyncBooksToElasticsearchCommand extends Command
{
    protected $signature = 'sync:books-to-elasticsearch';
    protected $description = 'Sync all existing books to Elasticsearch';

    public function handle()
    {
        $this->info('Syncing books to Elasticsearch...');

        // Dispatch the job to sync books to Elasticsearch
        SyncBooksToElasticsearch::dispatch();

        $this->info('Books synced to Elasticsearch successfully!');
    }
}
