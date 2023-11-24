<?php

namespace App\Services;

use Elastica\Client;
use Elastica\Document;
use Elastica\Query;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'host' => env('ELASTICSEARCH_HOST', 'localhost'),
        ]);
    }

    /**
     * Create index if not exists 
     *
     */
    public function indexDocument($index, $id, $document)
    {
        $index = $this->client->getIndex($index);
        $index->addDocument(new Document($id, $document));
        $index->refresh();
    }

    /**
     * Search document from index by query
     */
    public function search($index, $query)
    {
        $index = $this->client->getIndex($index);

        $query = new Query($query);
        $resultSet = $index->search($query);

        return $resultSet;
    }

    /**
     * Delete document by id from index
     */
    public function deleteDocument($index, $id): void
    {
        $index = $this->client->getIndex($index);
        $index->deleteById($id);
        $index->refresh();
    }

    /**
     * Update document
     */
    public function updateDocument($index, $id, $document): void
    {
        $index = $this->client->getIndex($index);
        $index->updateDocument(new Document($id, $document));
        $index->refresh();
    }

    /**
     * Get document by id from index
     */
    public function getDocument($index, $id): Document
    {
        $index = $this->client->getIndex($index);
        $document = $index->getDocument($id);
        return $document;
    }
}
