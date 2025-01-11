# PHPhinder Bundle 


[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)  
[![Packagist](https://img.shields.io/packagist/v/eliasfernandez/phphinder-bundle.svg)](https://packagist.org/packages/eliasfernandez/phphinder-bundle)  

---


## What is it?  
[PHPhinder](https://github.com/eliasfernandez/phphinder) is an open-source, lightweight, and modular search engine designed for PHP applications. It provides powerful search capabilities with a focus on simplicity, speed, and extensibility.

The PHPhinder bundle connects PHPhinder with Symfony to improve the searchability of Doctrine entities. 

---

## Installation  
Install PHPhinder via Composer:  
```bash
composer require eliasfernandez/phphinder-bundle
```

---

## Usage

### Entities

Imagine a book entity we want to optimize to search for. This is how it could look like:

```php
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[PHPhinder\Property(Schema::IS_UNIQUE | Schema::IS_INDEXED | Schema::IS_REQUIRED| Schema::IS_STORED)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[PHPhinder\Property(Schema::IS_INDEXED | Schema::IS_STORED | Schema::IS_REQUIRED | Schema::IS_FULLTEXT)]
    private ?string $title = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $authors = [];

    #[ORM\Column(type: Types::TEXT)]
    #[PHPhinder\Property(Schema::IS_INDEXED)]
    private ?string $description = null;
...
    
    #[PHPhinder\Property(Schema::IS_INDEXED | Schema::IS_REQUIRED, name: 'authors')]
    public function getAuthorsCsv(): string
    {
        return implode(', ', $this->authors);
    }}
```

### Controller

On the controller side we'll need to configure the Search engine to look for `Book` objects:

```php
    private SearchEngine $searchEngine;

    public function __construct(private StorageFactory $storageFactory, private SchemaGenerator $schemaGenerator)
    {
        $this->searchEngine = new SearchEngine(
            $this->storageFactory->createStorage(
                $this->schemaGenerator->generate(Book::class)
            )
        );
    }

```

Then, in the actions, we can get results with one single method:

```php
    #[Route('/search', name: 'app_search', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $query = $request->query->get('q', '');
        $results = [];

        if ($query) {
            $results = $this->searchEngine->search($query);
        }

        return $this->render('search/index.html.twig', [
            'query' => $query,
            'results' => $results,
        ]);
    }
```

### Configuration

#### `phphinder.storage` and `phphinder.name`

You can define where the indexes are going to exist by configuring these two variables:

* phphinder.storage could be `dbal`(search entities will live in a database) or `json` (in this case the data is stored in files).
* phphinder.name depending on the chosen storage the name will be considered as a folder inside the `var` folder on the project (in case the storage is json) or as database defined by a DBAL dsn connection string. 

### `phphinder.auto_sync`

If auto_sync is enabled. Every time a searchable entity is added or updated the search engine will store the neccesary indexes.
Attention: It could have a very high impact in systems with a high volume of writing in the database.

### Final notes

You can find a fully working example on the [PHPhinder project](https://github.com/eliasfernandez/phphinder-project)