<?php
namespace PHPhinderBundle\Schema;

use PHPhinder\Schema\Schema;
use PHPhinder\Transformer\Transformer;
use PHPhinderBundle\Schema\Attribute\Property;
use PHPhinderBundle\Schema\Attribute\SchemaClass;
use ReflectionClass;
use ReflectionProperty;

class SchemaGenerator
{
    /**
     * @var array<ReflectionClass>
     */
    private array $reflectionClasses = [];

    public function __construct(private string $cacheDir)
    {
    }

    /**
     * @param class-string $entityClass
     */
    public function generate(string $entityClass): Schema
    {
        $name = $this->getSchemaClassName($entityClass);
        $this->validateName($name, $entityClass);

        $schemaProperties = $this->getSchemaProperties($entityClass);
        $transformers = $this->getSchemaTransformers($entityClass);

        return $this->getSchema($name, $schemaProperties, $transformers);
    }


    public function getSchema(string $className, array $schemaData, array $transformers): Schema
    {
        $filePath = $this->cacheDir . "/$className.php";
        if (!file_exists($filePath)) {
            $this->createSchema($filePath, $className, $schemaData);
        }
        return $this->importSchema($filePath, $className, $transformers);
    }

    /**
     * @param class-string $entityClass
     */
    public function isSearchable(string $entityClass): bool
    {
        return null !== $this->getSchemaClassName($entityClass);
    }

    /**
     * @param class-string $entityClass
     */
    public function getSchemaClassName(string $entityClass): ?string
    {
        $class = $this->getReflectionClass($entityClass);
        if (0 === count($class->getAttributes(SchemaClass::class))) {
            return null;
        }
        return $class->getAttributes(SchemaClass::class)[0]?->newInstance()->name;
    }

    private function getPropertyAttribute(ReflectionProperty $property): ?Property
    {
        $attributes = $property->getAttributes(Property::class);
        if (!$attributes) {
            return null;
        }
        return $attributes[0]?->newInstance() ?? null;
    }

    /**
     * @return array<Transformer>
     */
    private function getSchemaTransformers(string $entityClass): array
    {
        $class = $this->getReflectionClass($entityClass);
        $attributes = $class->getAttributes(SchemaClass::class);
        return $attributes[0]?->newInstance()->transformers ?? [];
    }

    private function createSchema(string $filePath, string $className, array $schemaData): void
    {
        $properties = '';
        foreach ($schemaData as $property => $flags) {
            $property = preg_replace('/\W+/', '', $property);
            if ($property === 'id') {
                $property = '_id';
            }
            $flags = intval($flags);
            $properties .= "    public int \${$property} = {$flags};\n";
        }

        $schemaCode = <<<SCHEMA
        <?php
        
        use PHPhinder\Schema\SchemaTrait;
        use PHPhinder\Schema\Schema;
        
        class $className implements Schema
        {
            use SchemaTrait;
            
        $properties
            
        }
        SCHEMA;

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }

        file_put_contents($filePath, $schemaCode);
    }

    /**
     * @param string $filePath
     * @param class-string $className
     * @param array<Transformer> $transformers
     */
    private function importSchema(string $filePath, string $className, array $transformers): Schema
    {
        require_once $filePath;
        return new $className(...$transformers);
    }

    private function validateName(?string $name, string $entityClass): void
    {
        $class = $this->getReflectionClass($entityClass);

        if (null === $name) {
            throw new \InvalidArgumentException("Schema name for `{$class->getName()}` cannot be empty");
        }

        if (preg_match('/\W+/', $name)) {
            throw new \InvalidArgumentException("Schema name for `{$class->getName()}` is invalid. Please use only valid class names.");
        }
    }

    private function getSchemaProperties(string $entityClass): array
    {
        $class = $this->getReflectionClass($entityClass);
        $properties = $class->getProperties();

        $schemaProperties = [];
        foreach ($properties as $property) {
            $attribute = $this->getPropertyAttribute($property);
            if ($attribute !== null) {
                $schemaProperties[$property->getName()] = $attribute->flags;
            }
        }
        return $schemaProperties;
    }

    private function getReflectionClass(string $entityClass): ReflectionClass
    {
        if (isset($this->reflectionClasses[$entityClass])) {
            return $this->reflectionClasses[$entityClass];
        }

        $this->reflectionClasses[$entityClass] = new ReflectionClass($entityClass);
        return $this->reflectionClasses[$entityClass];
    }
}
