# LaraStructor - Laravel Structure Generator

A set of Artisan commands to automatically generate **Repository**, **Interface**, and **Service** classes in your Laravel application with clean structure and naming conventions. Inspired by clean architecture — made simple and fun!

---

## Requirements

- Laravel 8 or above
- PHP 8.0+
- Your model classes should exist before running the repository generation

---

## Generated Folder Structure Example

```text
app/
├── Models/
│   └── Product.php
├── Repositories/
│   ├── ProductRepository.php
│   └── Interfaces/
│       └── ProductRepositoryInterface.php
├── Services/
│   └── ProductService.php
```

## Installation

```
composer require laralearn/larastructor
```

## Available Commands

## 1. Make a Model

```
php artisan larastruct:make-model
```

You'll be prompted for:

```
    Model name
    Table name
    Fillable fields
    Hidden fields
    Soft delete support
```

What it does:

```
    Checks if the model already exists in the given path
    Creates a model file inside app/Models
```

## 2. Generate Repository & Interface

```
php artisan larastruct:make-repository {model} --model-path=Models
```

Options:

```
model – The name of your model (e.g. Product)
--model-path – Relative path to your model directory (default: Models)
```

### What it does:

```
Checks if the given model exists in the specified path
Creates:
    ProductRepository.php in app/Repositories
    ProductRepositoryInterface.php in app/Repositories/Interfaces
    Adds boilerplate CRUD methods
```

## 3. Generate Service Class for Repository

Options:

```
repository – The name of your repository/model (e.g. Product)
--repository-path – Relative path to your repositories (default: Repositories)
```

### What it does:

```
    Checks if the repository and interface exist
    Creates a service class in app/Services
    Injects the interface in the constructor
    Adds methods: getAll, getById, create, update, delete
```

## 4. Generate Service Class for Model

```
php artisan larastruct:make-service {model} --model-path=Models
```

Options:

```
model – The name of your model (e.g. Product)
--model-path – Relative path to your model directory (default: Models)
```

What it does:

```
Checks if the model exists
Creates a service class in app/Services
Injects the model in the constructor
Adds basic methods like getAll, getById, create, update, delete
```

## Example Output

### ProductRepository

```code
class ProductRepository
{
    public function all() { ... }
    public function find($id) { ... }
    public function create(array $data) { ... }
    public function update($id, array $data) { ... }
    public function delete($id) { ... }
}

```

## Let's improve it together 

Would you like to improve this tool or add new features?
Feel free to fork and submit a PR. Let’s make LaraStructor even cooler together

## Author(s)

- [@lokeshrangani](https://www.github.com/lokeshrangani)
- waiting for yourname
