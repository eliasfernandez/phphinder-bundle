# PHPhinder Bundle 


[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)  
[![Packagist](https://img.shields.io/packagist/v/eliasfernandez/phphinder-bundle.svg)](https://packagist.org/packages/eliasfernandez/phphinder-bundle)  

---


## What is it?  
[PHPhinder](https://github.com/eliasfernandez/phphinder) is an open-source, lightweight, and modular search engine designed for PHP applications. It provides powerful search capabilities with a focus on simplicity, speed, and extensibility.

The PHPhinder bundle connects PHPhinder with symfony to improve the searchability of Doctrine entities. 

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
    #[PHPhinder\Property(Schema::IS_INDEXED | Schema::IS_REQUIRED)]
    private array $authors = [];

    #[ORM\Column(type: Types::TEXT)]
    #[PHPhinder\Property(Schema::IS_INDEXED)]
    private ?string $description = null;
...
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

Then, in the actions, we can get results with one simgle method:

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

You can find a fully working example on the [PHPhinder project](https://github.com/eliasfernandez/phphinder-project)