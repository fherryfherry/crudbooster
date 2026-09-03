# **Getting Started**

## Introduction
**What is CRUDBooster?**

CRUDBooster is a powerful Laravel CRUD (Create, Read, Update, Delete) generator designed to streamline the development of admin panels and back-end applications. As a Laravel package, it allows developers to quickly and efficiently build robust administrative interfaces with minimal effort.

**Key Features of CRUDBooster**

1. **Rapid Development**: With CRUDBooster, you can create a fully functional admin panel in just a few clicks. This significantly reduces the time and effort required to set up the back-end of your application, allowing you to focus on other critical aspects of your project.

2. **Integration of Popular Packages**: CRUDBooster combines the best features of the most popular Laravel packages, providing a comprehensive toolkit that enhances your development experience. This integration ensures that you have access to a wide range of functionalities without the need to manually configure each package.

3. **Modern Front-End Frameworks**: The package is built on top of widely-used CSS and JavaScript frameworks, ensuring that your admin panel not only functions well but also looks great. By leveraging these frameworks, CRUDBooster provides a responsive and user-friendly interface that enhances the overall user experience.

4. **Extensive Features**: CRUDBooster comes packed with a variety of features, including:
  - User authentication and role management (RBAC)
  - Data filtering, searching, and sorting capabilities
  - Customizable forms and data validation
  - Support for file uploads
  - Exporting data to Excel and PDF formats
  - Importing data from Excel files

5. **Ease of Use**: Designed with developers in mind, CRUDBooster offers an intuitive interface that simplifies the process of creating and managing CRUD operations. Whether you are a seasoned developer or just starting, CRUDBooster makes it easy to build and maintain your applications.

## Overview

CRUDBooster uses the following dependencies:

- [Laravel Framework](https://laravel.com/) (PHP Framework)
- [Laravel Livewire](https://livewire.laravel.com/) (Full-stack Framework)
- [Alphine JS](https://alpinejs.dev/) (JavaScript Framework)
- [Tailwind CSS](https://tailwindcss.com/) (CSS Framework)
- [Laravel Excel](https://laravel-excel.com/) (Export & Import Excel)
- [DomPDF](https://github.com/dompdf/dompdf) (Export PDF)

## Release Notes

See [CHANGELOG.md](CHANGELOG.md).

## Installation

### System Requirements

CRUDBooster has a few system requirements. You need to make sure your server meets the following requirements:

- PHP >= 8.2
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- Filter PHP Extension
- Hash PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Session PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- PHP Zip Extension
- PHP GD Extension
- Laravel 11.x, 12.x
- Git
- Composer
- Database Supported: MySQL 5.7.7 or greater, MariaDB 10.2.2 or greater, PostgreSQL, SQLite, SQL Server

### Install Via Composer

**Installing Laravel**

To get started with CRUDBooster, you first need to have **a Laravel project** set up. If you don't have an existing Laravel project, you can easily create a new one by executing the following command in your terminal:

**Laravel 12.x**
```bash
composer create-project --prefer-dist laravel/laravel:^12.0 your-project-name
```
**Laravel 11.x**
```bash
composer create-project --prefer-dist laravel/laravel:^11.0 your-project-name
```

Replace `your-project-name` with the desired name for your Laravel project. This command will download and install a fresh Laravel installation, setting the foundation for your application.

**Installing CRUDBooster**

Once you have your Laravel project ready, you can proceed to install CRUDBooster. Use the following command to add CRUDBooster to your project:

Step 1. Go to your Laravel project directory:
```bash
  cd your-project-name
```

Step 2. Add this repository to your composer by run this following command:
> You need to install Git first if you don't. You can download it from [Git's official website](https://git-scm.com/).

```bash
composer config repositories.crudbooster vcs https://github.com/fherryfherry/crudbooster.git
```

Step 3. Install the CRUDBooster package by running the following command:
```bash
composer require crudbooster/crudbooster:^7.1
```

This command will pull the CRUDBooster package into your Laravel project, allowing you to utilize its features.

> Make sure you have installed the `git` package on your server.
> 
> Please update regularly to get the latest features and bug fixes by running `composer update crudbooster/crudbooster`.

**Running the Installation Command**

After successfully installing CRUDBooster, you need to run the following command to complete the installation process:

> First thing first, you need to have an empty database. If not, please create a new database and make sure you have the database credentials.

```bash
php artisan cb:install
```

<img src="https://crudbooster.com/images/docs/install.jpg" alt="Installation Step" />

Follow the interactive prompts to configure your application name, admin credentials, and database connection.

> **Note**<br/>Please update regularly to get the latest features and bug fixes by running `composer update crudbooster/crudbooster` and run `php artisan vendor:publish --tag=cb-themes --force`.

**Following the Installation Wizard**

Follow the instructions provided in the CRUDBooster installation wizard. Once the installation is complete, CRUDBooster will automatically generate several essential files in the `app/Cb/` directory of your Laravel project. These files are crucial for the functionality of CRUDBooster.

**Accessing the CRUDBooster Admin Panel**

After the installation is finished, you can start your Laravel development server by running the following command:

```bash
php artisan serve --port=8000
```

You can then access the CRUDBooster admin panel by navigating to the following URL in your web browser:

```bash
http://localhost:8000/cms/auth/login
```

This URL will take you to the login page of the CRUDBooster admin panel, where you can begin managing your application.

### Install With Docker

Installing CRUDBooster using Docker is an effective way to create a consistent development environment. Below are the steps to set up CRUDBooster with Docker, including creating a Laravel project.

**Step 1: Create a Laravel Project Locally**

First, you need to create a new Laravel project on your local machine. You can do this by running the following command:

```bash
composer create-project --prefer-dist laravel/laravel your-project-name
```

Replace `your-project-name` with the desired name for your Laravel project. This command will download and install a fresh Laravel installation.

**Step 2: Create a Dockerfile**

Next, navigate to the root of your newly created Laravel project and create a file named `Dockerfile`. Add the following content:

```dockerfile
FROM php:8.2-cli

# Install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    nano \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy the Laravel project files into the container
COPY . /var/www/html

# Install Laravel dependencies
RUN composer install

# Add package repository
RUN composer config repositories.crudbooster vcs https://github.com/fherryfherry/crudbooster.git

# Install CRUDBooster
RUN composer require crudbooster/crudbooster:^7.0

RUN php artisan cb:install

EXPOSE 8000

# Start the Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

This Dockerfile sets up a PHP environment with the necessary extensions for running Laravel and CRUDBooster. It installs Composer, copies your Laravel project files into the container, and installs both Laravel and CRUDBooster dependencies.

**Step 3: Create a Docker Compose File**

Create a file named `docker-compose.yml` in the same directory and add the following configuration:

```yaml
version: '3.3'

services:
  db:
    image: mariadb:10.11.3
    container_name: db
    environment:
      MYSQL_ROOT_PASSWORD: "123456"
      MYSQL_DATABASE: crudbooster
    networks:
      - app-network
    restart: on-failure
    volumes:
      - mariadb-data:/var/lib/mysql

  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: app
    volumes:
      - .:/var/www/html
    ports:
      - "80:8000"
    depends_on:
      - db
    networks:
      - app-network
    restart: on-failure
    environment:
      DB_CONNECTION: mysql
      DB_HOST: db
      DB_PORT: 3306
      DB_DATABASE: crudbooster
      DB_USERNAME: root
      DB_PASSWORD: "123456"

networks:
  app-network:

volumes:
  mariadb-data:
```

This `docker-compose.yml` file defines two services: one for the MariaDB database and another for the CRUDBooster application. It sets up the necessary environment variables for database connectivity and ensures that the application can communicate with the database service.

**Step 4: Build and Run the Docker Containers**

To build and run your Docker containers, open your terminal, navigate to the directory containing your `Dockerfile` and `docker-compose.yml`, and execute the following command:

```bash
docker-compose up -d
```

This command will build the application container and start both the application and database services in detached mode.

**Step 5: Access the CRUDBooster Admin Panel**

Once the containers are up and running, you can access the CRUDBooster admin panel by visiting the following URL in your web browser:

```bash
http://localhost/cms/auth/login
```

This URL will take you to the login page of the CRUDBooster admin panel, where you can begin managing your application.

### Install Via Bash Script

Below is the revised bash script for installing CRUDBooster in a Laravel project, including the command to start the server.

**Step 1: Create the Bash Script**

Create a file named `install_crudbooster.sh` in your project directory and add the following content:

```bash
#!/bin/bash

# Check if a project name is provided
if [ -z "$1" ]; then
  echo "Usage: $0 <project-name>"
  exit 1
fi

PROJECT_NAME=$1

# Create a new Laravel project
echo "Creating a new Laravel project: $PROJECT_NAME"
composer create-project --prefer-dist laravel/laravel $PROJECT_NAME

# Navigate into the project directory
cd $PROJECT_NAME

# Add the CRUDBooster repository to composer
composer config repositories.crudbooster vcs https://github.com/fherryfherry/crudbooster.git

# Install CRUDBooster
echo "Installing CRUDBooster..."
composer require crudbooster/crudbooster:^7.0

# Run the CRUDBooster installation command
echo "Running CRUDBooster installation..."
php artisan cb:install

# Start the Laravel development server
echo "Starting the Laravel development server..."
php artisan serve --host=0.0.0.0 --port=8000 &

# Display completion message
echo "CRUDBooster installation completed successfully!"
echo "You can now access your application at http://localhost:8000/cms/auth/login"
```

**Step 2: Make the Script Executable**

After creating the script, you need to make it executable. Run the following command in your terminal:

```bash
chmod +x install_crudbooster.sh
```

**Step 3: Run the Script**

You can now run the script by providing a project name as an argument. For example:

```bash
./install_crudbooster.sh your-project-name
```

Replace `your-project-name` with the desired name for your Laravel project. The script will create a new Laravel project, install CRUDBooster, run the installation command, and start the Laravel development server.

**Explanation of the Script**

1. **Check for Project Name**: The script first checks if a project name is provided as an argument. If not, it displays a usage message and exits.

2. **Create a New Laravel Project**: It uses the `composer create-project` command to create a new Laravel project with the specified name.

3. **Navigate into the Project Directory**: The script changes the current directory to the newly created Laravel project.

4. **Install CRUDBooster**: It runs the `composer require` command to install CRUDBooster, pulling the package from the specified repository.

5. **Run the CRUDBooster Installation Command**: The script executes the `php artisan cb:install` command to complete the installation process.

6. **Start the Laravel Development Server**: The script starts the Laravel development server using `php artisan serve`, allowing you to access your application immediately. The `&` at the end runs the command in the background, so the script can continue executing.

7. **Completion Message**: Finally, it displays a message indicating that the installation was successful and provides the URL to access the CRUDBooster admin panel.

## Upgrade Guide
### Old Version To v7
**Important Notice Regarding CRUDBooster v7.x**

CRUDBooster v7.x represents a significant and transformative release of the framework. Due to the extensive changes and improvements made in this version, there is currently no upgrade guide available for users transitioning from older versions (such as 5.6.x or below).

**Installation Guidance**

As a result, if you are using an older version of CRUDBooster, you will need to install CRUDBooster v7.0.0 from scratch. This means that rather than attempting to upgrade your existing project, we highly recommend that you create a new project using the latest version of CRUDBooster.

**Steps to Remake Your Project**

1. **Backup Your Existing Project**: Before starting the new installation, ensure that you back up your current project files and database. This will allow you to reference your previous work and data as needed.

2. **Install CRUDBooster v7.0.0**: Follow the official installation instructions for CRUDBooster v7.0.0. You can find the latest version on your dashboard console CRUDBooster.

3. **Recreate Your Modules and Features**: Once you have installed the new version, you will need to recreate your modules, features, and any customizations you had in your previous project. This is a great opportunity to review your project structure and make improvements based on the new capabilities offered by CRUDBooster v7.x.

4. **Test Your New Setup**: After recreating your project, thoroughly test all functionalities to ensure everything works as expected. This will help you identify any issues early on and make necessary adjustments.

**Conclusion**

While transitioning to CRUDBooster v7.x may require additional effort, the benefits of using the latest version—such as improved performance, new features, and enhanced security—are well worth it. We appreciate your understanding and encourage you to take full advantage of the advancements in this major release. If you have any questions or need assistance during the installation process, please refer to the documentation or reach out to the community for support.
### v7.x
We recommend staying updated with CRUDBooster to ensure greater reliability. Please run the following command:
1. **Open your project**<br/>
    Run this bellow command:
```bash
composer update crudbooster/crudbooster
```
1. **Update asset if any**
```bash
php artisan vendor:publish --tag=cb-themes --force
```

1. **Optimize Project**
```bash
php artisan optimize
```


## Lifecycle

**Understanding the Lifecycle of CRUDBooster**

Understanding the lifecycle of CRUDBooster is crucial for effectively utilizing its features and grasping how it operates within your Laravel project. Since CRUDBooster is built on top of Livewire, having a solid understanding of the Livewire lifecycle will greatly enhance your ability to work with CRUDBooster.

**What is CRUDBooster?**

It's important to note that CRUDBooster is not a standalone framework; rather, it is **a package** that you can integrate into your Laravel application. This package simplifies the process of building CRUD (Create, Read, Update, Delete) applications by providing a set of tools and functionalities.

**CRUDBooster Lifecycle**

The lifecycle of CRUDBooster is closely aligned with that of Livewire, but it also includes additional stages specific to CRUDBooster. Here are the key lifecycles in CRUDBooster:

1. **Routing**:
  - This lifecycle is triggered when a request is received. CRUDBooster automatically registers two routes: one for browsing and another for forms. These routes direct the request to the appropriate component classes, determining whether the request is for browsing data or displaying a form. The details of the request are managed through parameters in the form route.

2. **init()**:
  - This lifecycle runs when the component is initialized. It is here that you define all configurations related to columns, forms, hooks, transformations, and more. This method sets the groundwork for how your component will behave.

3. **mount()**:
  - The `mount()` lifecycle is executed when the component is mounted, inheriting functionality from Livewire. The `init()` method is called during this stage, ensuring that all initial configurations are applied. You do not need to define this method manually, as it is already included in the base component.

4. **Core**:
  - The core query process occurs when the component is mounted. This process is responsible for retrieving data from the database, including functionalities such as filtering, searching, and sorting.

5. **formSave()**:
  - This lifecycle is triggered when the form is submitted and saved. It handles the process of saving the form data to the database, ensuring that user inputs are correctly recorded.

6. **render()**:
  - The `render()` lifecycle runs when the component is rendered, also inherited from Livewire. Similar to the `mount()` method, developers do not need to define this manually, as it is already part of the base component.

**Automatic Rendering of Columns and Forms**

Once you define your column configurations using `Column::add(...)` in the `init` method of the Browse Component, CRUDBooster will automatically render these columns on the browse page. The same applies to the Form Component; when you define the form configuration using `Form::add(...)` in the `init` method, it will automatically render the form on both the form page and the detail page.

> **Note**: You don't need to worry about crafting complex queries or handling intricate logic. CRUDBooster takes care of these aspects for you. All you need to do is specify the model, the columns, and the form structure, and CRUDBooster will handle the rendering seamlessly.

**Customizing Queries and Data Relationships**

You might wonder, **what if** you want to **customize the query**, establish **relationship data**, or **transform** the data in some way? CRUDBooster provides a robust hook system that allows you to customize queries, create relationships, and transform data as needed. This flexibility ensures that you can tailor the functionality to meet your specific requirements.

**Recommended Knowledge Areas**

CRUDBooster is developed using the latest technologies, including **PHP, Laravel, Livewire, AlpineJS, and TailwindCSS**. For advanced development and to fully leverage the capabilities of CRUDBooster, we highly recommend that you familiarize yourself with these platforms. Understanding these technologies will empower you to create more sophisticated and efficient applications.

---
## **Quick Start**
### Create First Module (Generate CRUD a.k.a Module)

#### Via GUI / Admin Panel
To kick off your journey in creating your first module, we highly recommend watching this video tutorial. It provides a clear and easy-to-follow step-by-step guide. Happy learning, and we hope you find this process enjoyable and beneficial!
<iframe width="300" height="600" src="https://www.youtube.com/embed/32wlgx8BCrk?si=fb2XOzn_ZsakVQVk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

> Note: When you create a module with the GUI and update it again in the GUI, all the module files you have created will be replaced with new module files. All changes you have made manually in the code editor will be lost. So, make sure to create a backup first before making changes in the GUI.

#### Via Command Line 
To create your first module in Laravel using CRUD Booster, you can start by executing the following command in your terminal:

```
php artisan cb:crud table_name
```

In this command, make sure to replace `table_name` with the actual name of the database table for which you want to create a module. For example, if you wish to create a module for managing user data, you would run:

```
php artisan cb:crud users
```

This command will generate a complete CRUD (Create, Read, Update, Delete) module for the `users` table, including the necessary views, controllers, and routes, allowing you to manage user records efficiently.

**Customizing Your Module Name**

For added flexibility, you can specify a custom name for your module by using the `--name` option. This is particularly useful if you want the module to have a different display name than the table name. Here's how you can do it:

```
php artisan cb:crud users --name=Users
```

In this example, the module will still be created for the `users` table, but it will be referred to as "Users" in the application interface.

**Generating All CRUD Modules at Once**

If you have multiple tables and want to generate CRUD modules for all of them simultaneously, you can use the `--all` option. This command will scan your database and create modules for every table it finds:

```
php artisan cb:crud --all
```

This is a great way to quickly set up a complete administrative interface for your application.

**Using the Command Line Interface**

If you prefer to interact with the command line interface without specifying any options, you can simply run:

```
php artisan cb:crud
```

This command will guide you through the process interactively, allowing you to make selections and configurations as needed.


### Create First Dashboard

To get started on creating your first dashboard, we invite you to check out this helpful video tutorial. It offers a step-by-step guide that will make the process easy and enjoyable. Happy building, and we hope you find it both informative and inspiring!
<iframe width="300" height="600" src="https://www.youtube.com/embed/hoN7tBQfLaw?si=QKLy7lgIsCirZtpm" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

# **Digging Deeper**

## Reserved Name

CRUDBooster has a few reserved names that you can't use. The reserved names are:

- `cb` Reserved for CRUDBooster configuration
- `cb` Also Reserved for prefix view

## **Browse Page**

### Add Column

Add column is a process to add a column in the browse page. You can use the following code to add a column in the
browse:

```php
use CrudBooster\Livewire\ColumnBuilder\Column;

Column::add(label: 'Name', key: 'users.name')
```

For the key parameter, you can define it as `{table}.{column}` or just `{column}`. Using `{table}.{column}` will be
useful if you are performing a join table. Join table will be discussed in the next section.

If you want to disable search on the column, you can use the `searchable` property:

```php
Column::add(label: 'Name', key: 'users.name', searchable: false);
``` 

Now you can also combine it with other options, such as `sortable`, `searchable`, `filterable`, `exportable`. Here is an
example of its usage:

```php
Column::add(label: 'Name', key: 'users.name', searchable: false, sortable: false, filterable: false, exportable: false);
```

### Prevent Text Wrapping (noWrap)

Sometimes you want to ensure that the text in a table column does not wrap to a new line, especially for codes, IDs, or short labels. You can use the `noWrap()` method on a column to apply this behavior.

**Usage Example:**
```php
Column::add('Project Code', 'project_code')->noWrap()
```

This will render the cell with `white-space: nowrap;` so the content always stays on one line, regardless of the table or parent CSS.

- Works for both plain text and HTML columns.
- You can combine `noWrap()` with other column options (e.g., `sortable`, `filterable`, etc).

**Note:**
If the column content is too long, it may overflow or be truncated depending on your table layout. You can combine with Tailwind classes like `truncate` or set a `min-w-[value]` for better control.

**Full Example:**
```php
$this->makeColumns([
    Column::add('Project Name', 'name'),
    Column::add('Project Code', 'project_code')->noWrap(),
    Column::add('Status', 'status'),
]);
```

### Advanced Filtering

By default, every column you add in CRUDBooster can have its filter feature enabled with the `filterable` property. However, you can also specify more specific filter types according to your data needs.

#### Basic: Filterable

To enable filtering on a column, simply add `filterable: true` to the column definition:

```php
Column::add(label: 'Name', key: 'users.name', filterable: true)
```

Or with chaining:
```php
Column::add(label: 'Name', key: 'users.name')->filterable()
```

To disable filtering:
```php
Column::add(label: 'Name', key: 'users.name')->filterable(false)
```

#### Advanced Filter Types

You can specify certain filter types on columns using the following methods:

**Contains Filter (Default)**
```php
Column::add('Name', 'name')->filterContains();
// or simply don't specify filter type (defaults to contains)
Column::add('Name', 'name');
```

**Number Comparison Filters**
```php
// Greater than
Column::add('Price', 'price')->filterGreaterThan();

// Greater than or equal
Column::add('Price', 'price')->filterGreaterEqual();

// Less than
Column::add('Price', 'price')->filterLessThan();

// Less than or equal
Column::add('Price', 'price')->filterLessEqual();
```

**Date Range Filter**
```php
Column::add('Created At', 'created_at')->filterDateRange();
```

**Select Enum Filter**
```php
Column::add('Status', 'status')->filterSelectEnum([
    'active' => 'Active',
    'inactive' => 'Inactive',
    'pending' => 'Pending'
]);
```

**Select Query Filter**
```php
// Simple query - closure must return query with 'value' and 'label' selects
Column::add('Category', 'category_id')->filterSelectQuery(function() {
    return Category::query()
        ->select('id as value', 'name as label')
        ->orderBy('name');
});

// With custom conditions
Column::add('Category', 'category_id')->filterSelectQuery(function() {
    return Category::query()
        ->select('id as value', 'name as label')
        ->where('is_active', true)
        ->orderBy('name');
});
```

**Select Query Filter with Custom Search Logic**
> This feature is available in CRUDBooster v7.9.7 and above.

You can add a second optional parameter to `filterSelectQuery()` to define custom search logic when the filter is applied. This allows you to implement complex queries like subqueries or custom logic instead of the default equality comparison.

```php
// Basic usage with custom search closure
Column::add('Category', 'category_id')->filterSelectQuery(
    function() {
        return Category::query()
            ->select('id as value', 'name as label')
            ->where('is_active', true)
            ->orderBy('name');
    },
    function($query, $filterValue, $field) {
        // Custom search logic when filter is applied
        $query->whereIn('category_id', function($subQuery) use ($filterValue) {
            $subQuery->select('id')
                    ->from('categories')
                    ->where('name', 'like', '%' . $filterValue . '%')
                    ->orWhere('description', 'like', '%' . $filterValue . '%');
        });
    }
);
```

**Advanced Examples:**

**1. Complex Subquery with Multiple Conditions**
```php
Column::add('Products', 'product_id')->filterSelectQuery(
    function() {
        return Product::query()
            ->select('id as value', 'name as label')
            ->where('stock', '>', 0)
            ->orderBy('name');
    },
    function($query, $filterValue, $field) {
        $query->whereExists(function($subQuery) use ($filterValue) {
            $subQuery->select(DB::raw(1))
                    ->from('products')
                    ->whereColumn('products.id', '=', 'orders.product_id')
                    ->where('products.name', 'like', '%' . $filterValue . '%')
                    ->where('products.price', '>', 100);
        });
    }
);
```

**2. Date Range Filter with Custom Logic**
```php
Column::add('Orders', 'order_id')->filterSelectQuery(
    function() {
        return Order::query()
            ->select('id as value', 'order_number as label')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc');
    },
    function($query, $filterValue, $field) {
        $query->whereBetween('created_at', [
            now()->subDays(30),
            now()
        ])->where('total_amount', '>', 1000);
    }
);
```

**3. Multiple Table Join with Custom Search**
```php
Column::add('Users', 'user_id')->filterSelectQuery(
    function() {
        return User::query()
            ->select('users.id as value', 'users.name as label')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_verified', true)
            ->orderBy('users.name');
    },
    function($query, $filterValue, $field) {
        $query->whereHas('profile', function($profileQuery) use ($filterValue) {
            $profileQuery->where('city', 'like', '%' . $filterValue . '%')
                        ->orWhere('country', 'like', '%' . $filterValue . '%');
        });
    }
);
```

**4. Aggregation-based Filter**
```php
Column::add('Categories', 'category_id')->filterSelectQuery(
    function() {
        return Category::query()
            ->select('categories.id as value', 'categories.name as label')
            ->orderBy('categories.name');
    },
    function($query, $filterValue, $field) {
        $query->whereIn('category_id', function($subQuery) use ($filterValue) {
            $subQuery->select('category_id')
                    ->from('products')
                    ->groupBy('category_id')
                    ->havingRaw('COUNT(*) >= ?', [$filterValue]);
        });
    }
);
```

**Parameters for Search Closure:**

The search closure receives three parameters:

- `$query`: The current query builder instance for the main table
- `$filterValue`: The selected value from the dropdown filter
- `$field`: The field name being filtered (e.g., 'category_id')

**Usage Notes:**

- The search closure modifies the query directly, no return value is needed
- If no search closure is provided, the default equality comparison (`=`) will be used
- The search closure is only called when a filter value is selected
- You can use any Laravel Query Builder methods including `where`, `whereIn`, `whereExists`, `whereHas`, etc.
- Complex queries like subqueries, joins, and aggregations are fully supported
- The search closure runs after the main query is built, so you can modify it as needed

**Compatibility:**

This feature is available in CRUDBooster v7.9.7 and above. The second parameter is optional, so existing code will continue to work without modification.

#### Complete Example

```php
<?php

namespace App\Cb\Modules\Products;

use CrudBooster\Livewire\BaseBrowseComponent;
use CrudBooster\Livewire\ColumnBuilder\Column;
use App\Models\Product;
use App\Models\Category;

class Products extends BaseBrowseComponent
{
    public $modelName = Product::class;
    public $modelService = ProductService::class;
    
    public function init(): void
    {
        $this->makeColumns([
            // Contains filter (default)
            Column::add('Name', 'name'),
            
            // Number comparison filters
            Column::add('Price', 'price')->filterGreaterThan(),
            Column::add('Stock', 'stock')->filterLessEqual(),
            
            // Date range filter
            Column::add('Created At', 'created_at')->filterDateRange(),
            
            // Select enum filter
            Column::add('Status', 'status')->filterSelectEnum([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'draft' => 'Draft'
            ]),
            
            // Select query filter
            Column::add('Category', 'category_id')->filterSelectQuery(function() {
                return Category::query()
                    ->select('id as value', 'name as label')
                    ->where('is_active', true)
                    ->orderBy('name');
            }),
            
            // Regular column without filter
            Column::add('Description', 'description', null, true, false),
        ]);
    }
}
```

#### How It Works

1. **Contains Filter**: Shows a text input field for string search
2. **Number Filters**: Shows a number input with the operator displayed
3. **Date Range**: Shows two date inputs (start and end date)
4. **Select Enum**: Shows a dropdown with predefined options
5. **Select Query**: Shows a dropdown populated from database query

#### Notes

- If you only use `filterable()` without specifying a filter type, the default filter type will be used (usually text input/contains).
- For number filters, use `filterGreaterThan`, `filterLessThan`, etc.
- For date filters, use `filterDateRange`.
- For selection filters, use `filterSelectEnum` or `filterSelectQuery`.
- All existing code will continue to work as before. If no filter type is specified, it defaults to 'contains' behavior.

Column creation must always be wrapped with makeColumns as follows:

```php
use CrudBooster\Livewire\ColumnBuilder\Column;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->makeColumns([
            Column::add(label: 'Name', key: 'users.name'),
            Column::add(label: 'Email', key: 'users.email'),
            Column::add(label: 'Phone', key: 'users.phone'),
            Column::add(label: 'Position', key: 'users.position'),
        ]);
    }
}
```

### Transform Column

Sometimes we want to change the data displayed in the column. To do this, we can use the `transform` function. Here is
an example of using the `transform` function:

```php
use CrudBooster\Livewire\ColumnBuilder\Column;

Column::add(label: 'Name', key: 'users.name')->transform(function ($value) {
    return strtoupper($value);
});
```

Or you want to do a transform and need other column data, you can use `transformWithRow` as in the following example:
```php
Column::add(label: 'Name', key: 'users.name')->transformWithRow(function($row) {
    return strtotupper($row->name)."<br/>".$row->created_at;
});
```

### Conditional Transform (transformWhen)
```php
Column::add('Status', 'status')->transformWhen('active', function($value) {
    return '<span class="text-green-600">' . $value . '</span>';
});
```

### Badgeable (Auto Badge)
```php
Column::add('Status', 'status')->badgeable([
    'active' => 'success',
    'inactive' => 'danger',
    'pending' => 'warning',
]);
```

### Badgeable Success
```php
Column::add('Status', 'status')->badgeableSuccess('active', 'Active');
```

### Badgeable Danger
```php
Column::add('Status', 'status')->badgeableDanger('inactive', 'Inactive');
```

### Badgeable Warning
```php
Column::add('Status', 'status')->badgeableWarning('pending', 'Pending');
```

### Badgeable Info
```php
Column::add('Status', 'status')->badgeableInfo('info', 'Info');
```

### Badgeable Primary
```php
Column::add('Status', 'status')->badgeablePrimary('main', 'Main');
```

**Notes:**
- All badgeable methods automatically display a consistent TailwindCSS badge.
- If the value does not match, the plain value will be shown.
- For custom badges, use `badgeable` with a mapping array.
- For complex transformations, use a closure in `transform()`.

### Image Column

If you want to display an image in the column, you can use the `image` function. Here is an example of using the `image`
function:

```php
use CrudBooster\Livewire\ColumnBuilder\Column;

Column::add(label: 'Photo', key: 'users.photo')->image();

```


In the example above, we use the `image` function to display an image in the `photo` column.

Basically, the `image` function will call the `transformWithRow` function with the `$row` parameter containing the row data
being processed. If you want a more advanced image function, you can use the `transformWithRow` function as follows:

```php

Column::add(label: 'Photo', key: 'users.photo')->transformWithRow(function ($row) {
    return '<img src="' . $row->photo . '" />';
});
```

### Link Column

If you want to display a link in the column, you can use the `link` function. Here is an example of using the `link`
function:

```php
use CrudBooster\Livewire\ColumnBuilder\Column;

Column::add(label: 'Name', key: 'users.name')->link();
```

In the example above, we use the `link` function to display a link in the `name` column. The url and text of the link
will be taken from the data in the column. If you want to customize the `text` you can pass the configuration as
follows:

```php
Column::add(label: 'Name', key: 'users.name')->link(['text'=> 'Click Me']);
```

If you need something more advanced, you can use the `transform` function as follows:

```php
Column::add(label: 'Name', key: 'users.name')->transform(function ($value) {
    return '<a href="https://example.com">' . $value . '</a>';
});
```

### Hook Query

CRUDBooster now uses the query hook to handle interception processes, instead of using the hook function concept.
You can use the following query hook to make a custom query in your browse page:

```php
// Hook Query
$this->hookQuery(function (Builder $query) {
    $query->whereLike('email','%gmail.com%');
});
```

### Hook Search

CRUDBooster v7.8.0+ provides a custom search hook that allows you to implement your own search logic in Livewire modules. This is useful when you need more complex search functionality beyond the default search behavior.

#### Basic Usage

To use the hook search feature, you need to call `hookSearch()` method inside your `init()` method:

```php
class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->makeColumns([
            Column::add(label: 'Name', key: 'name'),
            Column::add(label: 'Email', key: 'email'),
            Column::add(label: 'Phone', key: 'phone'),
        ]);
        
        $this->hookSearch(function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        });
    }
}
```

#### Advanced Search with Relationships

You can also implement search across related tables:

```php
$this->hookSearch(function($query, $search) {
    $query->where(function($q) use ($search) {
        $q->where('users.name', 'like', '%' . $search . '%')
          ->orWhere('users.email', 'like', '%' . $search . '%')
          ->orWhereHas('category', function($categoryQuery) use ($search) {
              $categoryQuery->where('name', 'like', '%' . $search . '%');
          });
    });
});
```

#### Search with Custom Logic

You can implement complex search logic based on your business requirements:

```php
$this->hookSearch(function($query, $search) {
    // Search by user status
    if (str_contains(strtolower($search), 'active')) {
        $query->where('status', 'active');
        return;
    }
    
    if (str_contains(strtolower($search), 'inactive')) {
        $query->where('status', 'inactive');
        return;
    }
    
    // Default search across multiple fields
    $query->where(function($q) use ($search) {
        $q->where('name', 'like', '%' . $search . '%')
          ->orWhere('email', 'like', '%' . $search . '%')
          ->orWhere('phone', 'like', '%' . $search . '%');
    });
});
```

#### Parameters

The closure function receives two parameters:

- `$query`: The current query builder instance
- `$search`: The search term entered by the user

#### Usage Notes

- The closure modifies the query directly, no return value is needed
- If no custom search logic is implemented, the default search behavior will be used
- You can call `hookSearch()` multiple times in the same `init()` method to add multiple search conditions

#### Compatibility

This feature is available in CRUDBooster v7.8.0 and above. The `hookSearch` method is automatically available in `BaseBrowseComponent`, so you can use it in any browse component that extends this base class.

### Relationship

#### Join Table

You can use the following join table to make a custom join table in your browse page:

```php
Column::add(label: 'Category', key: 'category_id')->relation('categories', function (JoinClause $joinClause) {
                $joinClause->on('categories.id', '=', 'users.category_id');
            }, 'name')
```

#### Join Multiple Table

You can use the following join multiple table to make a custom join multiple table in your browse page:

```php
use CrudBooster\Livewire\ColumnBuilder\Column;
use CrudBooster\Livewire\ColumnBuilder\Relation;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->makeColumns([
            Column::add(label: 'Name', key: 'name'),
            Column::add(label: 'Email', key: 'email'),
            Column::add(label: 'Phone', key: 'phone'),
            Column::add(label: 'Position', key: 'position'),
            Column::add(label: 'Category', key: 'sub_category_id')->relationWithNested([
                Relation::add('subcategories', 'subcategories', 'id', '=', 'sub_category_id')
                Relation::add('categories', 'categories', 'id', '=', 'category_id')                
            ], 'name'),
        ]);
    }
}
```

Explanation:

- `Relation::add('subcategories', 'subcategories', 'id', '=', 'sub_category_id')` is a relation with the subcategories
  table.
- `Relation::add('categories', 'categories', 'id', '=', 'category_id')` is a relation with the categories table.

#### Join Many To Many

Let's say we have `users`, `roles`, and `user_roles` tables. We want to display the `roles` column in the `users` table.
Here is an example of using many-to-many join:

```php
->relationMany(string $modelMany, string $firstFk, string $secondFk, string $displayModel, string $displayColumn, string $displayDelimiter = ', ')
```

Here is an example of using many-to-many join:

```php
use App\Cb\Modules\Users\Models\UserRole;
use App\Cb\Modules\Roles\Models\Role;

Column::add(label: 'Roles', key: 'roles')->relationMany(UserRole::class, 'users_id', 'roles_id', Role::class, 'name')
```

In the example above, we use the `user_roles` table as the linking table between `users` and `roles`. We also use
the `roles` table as the table to join. For the first argument, it is the model class of the `user_roles` table, the
second argument is the name of the foreign key column from the `users` table, the third argument is the name of the
foreign key column from the `roles` table, the fourth argument is the model class of the `roles` table, and the fifth
argument is the name of the column to be displayed.

#### Advanced Join Case

Sometimes you want to join tables with certain conditions and a bit more complex. We will use `hookQuery`:

```php
use CrudBooster\Livewire\ColumnBuilder\Column;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        // Make hook query
        $this->hookQuery(function ($query) {
            $query->join('categories', function (\Illuminate\Database\Query\JoinClause $joinClause) {
                $joinClause->on('categories.id', '=', 'users.category_id')
                           ->where('categories.active', '=', 1);
            })
            ->join('subcategories', function (\Illuminate\Database\Query\JoinClause $joinClause) {
                $joinClause->on('subcategories.id', '=', 'users.subcategory_id');
            });
        });
    
        $this->makeColumns([
            Column::add(label: 'Name', key: 'name'),
            Column::add(label: 'Email', key: 'email'),
            Column::add(label: 'Phone', key: 'phone'),
            Column::add(label: 'Position', key: 'position'),
            Column::add(label: 'Category', key: 'categories.name'),
            Column::add(label: 'Subcategory', key: 'subcategories.name'),
        ]);
    }
}
```

In the example above, we use `hookQuery` to perform a join query. This `hookQuery` is very flexible because it is a
callback from Laravel's Query Builder, so you can perform any query here.

### Visibility of Button

Do you want to hide the button on the browse page? Here is an example to hide the add button on the browse page:

```php
// ...

class User extends BaseBrowseComponent
{
    public $buttonCreate = false; // To hide the add button
    
    public function init(): void
    {
        // ...
    }
}
```

Bellow are the available properties:

| Property            | Description                                            |
|---------------------|--------------------------------------------------------|
| `$buttonCreate`     | To hide the add button                                 |
| `$buttonDelete`     | To hide the delete button                              |
| `$buttonEdit`       | To hide the edit button                                |
| `$buttonDetail`     | To hide the detail button                              |
| `$buttonExportXls`  | To hide the export to XLS button                       |
| `$buttonExportCsv`  | To hide the export to CSV button                       |
| `$buttonExportPdf`  | To hide the export to PDF button                       |
| `$buttonImport`     | To hide the import button                              |
| `$buttonSearch`     | To hide the search button                              |
| `$buttonFilter`     | To hide the filter button                              |
| `$buttonBulkAction` | To hide the bulk action button and the checkbox column |

So if the complete version looks like this:

```php
// ...

class User extends BaseBrowseComponent
{
    public $buttonCreate = false; // To hide the add button
    public $buttonDelete = false; // To hide the delete button
    public $buttonEdit = false; // To hide the edit button
    public $buttonDetail = false; // To hide the detail button
    public $buttonExportXls = false; // To hide the export to XLS button
    public $buttonExportCsv = false; // To hide the export to CSV button
    public $buttonExportPdf = false; // To hide the export to PDF button
    public $buttonImport = false; // To hide the import button
    public $buttonSearch = false; // To hide the search button
    public $buttonFilter = false; // To hide the filter button
    public $buttonBulkAction = false; // To hide the bulk action button and the checkbox column
    
    public function init(): void
    {
        // ...
    }
}
```

The question then arises, how can I display the button under certain conditions? You can use the following code:

```php
// ...

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        // Hide delete button for the first row (id = 1)
        $this->hideDeleteButtonWhen(function ($row) {
            return $row->id == 1;
        });
        // ...
    }
}
```

The example above is for hiding the delete button for the first row (id = 1). You can use the `$row` variable to get the
current row data. This special function is only available
for `hideDeleteButtonWhen`, `hideEditButtonWhen`, `hideCheckboxWhen`, and `hideDetailButtonWhen`.

### Bulk Action

Bulk Action is a feature that allows you to perform an action on multiple rows at once. CRUDBooster provides a feature
to create a bulk action. Here is an example of creating a bulk action:

```php
// ...
use CrudBooster\Components\Icon\Icon;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->addBulkAction(label: 'Delete', icon: Icon::TRASH, action: function ($selected) {
            User::whereIn('id', $selected)->delete();
        });
        
        // Or you can use chaining model class
        
        BulkAction::add('Delete')
        ->icon(Icon::TRASH)
        ->action(fn($selected) => User::whereIn('id', $selected)->delete());
        
        // ...
    }
}
```

In the example above, we use the `addBulkAction` function to create a bulk action. The first argument is the name of the
bulk action, the second argument is the icon of the bulk action, and the third argument is the callback function to
perform the action. The `$selected` variable is an array of selected row IDs.

**Confirmation**

By default, when the user clicks on this bulk action, a confirmation dialog will appear. If you want to change the text
in the confirmation dialog, you can use the `confirmText` method as follows:

```php 
$this->addBulkAction(label:'Delete', icon: Icon::TRASH, action: function ($selected) {
    User::whereIn('id', $selected)->delete();
}, confirmTitle: 'Are you sure?', confirmText: 'This action cannot be undone');
```

**Permission**

You can also add permissions to the bulk action button, so the button will only appear if the user has the required
permission. Here is an example of using permissions on a bulk action:

```php
use CrudBooster\Modules\Role\Enum\RolePermission;
use CrudBooster\Components\Icon\Icon;

$this->addBulkAction(label:'Delete', icon: Icon::TRASH, action: function ($selected) {
    User::whereIn('id', $selected)->delete();
}, permission: RolePermission::DELETE);
```

In the example above, the bulk action button will only appear if the user has the `DELETE` permission for the related
module.

### Table Action Button

If you want to add a new button alongside Add, Import, Export, etc., you can use the `addTableAction` function.
Here is how to add a new button:

```php
use CrudBooster\Components\Icon\Icon;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->addTableActionButton("Remove All")
            ->icon(Icon::TRASH)
            ->buttonIconText()
            ->buttonInfo()
            ->confirmation(title: "Are you sure?", message: "This action will remove all promos. This action cannot be undone.")
            ->action(fn() => $this->modelService::query()->delete());
        // ...
    }
}
```
PIn the example above, we use `addTableActionButton` to add a new button to delete all data. In the first argument of `addTableActionButton`, you can enter the label of the button.

Next, if you want to add an icon, use the chain method `icon` with a parameter from `CrudBooster\Components\Icon\Icon`.

If you want to add icon text to the button, use the chain method `buttonIconText`. Available options: `buttonIconOnly`, `buttonIconText`, `buttonTextOnly`.

If you want to make the button blue info, use the chain method `buttonInfo`. Available options: `buttonInfo`, `buttonSuccess`, `buttonWarning`, `buttonDanger`, `buttonPrimary`.

If you want to add a confirmation dialog before the button is executed, use the chain method `confirmation` with the parameters title and message.

If you want to add an action to the button, use the chain method `action` with a closure function parameter.

Alternatively, if you want to perform a redirect action, you can use the chain method `actionRedirect` with a URL path parameter.kukan action redirect url, Anda bisa menambahkan chain method `actionRedirect` dengan parameter url path. 

### Row Action Button

Row Action Button is a feature that allows you to perform an action on a single row. CRUDBooster provides a feature to
create a row action button. Here is an example of creating a row action button:

```php
use CrudBooster\Components\Icon\Icon;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->addActionButton(label: 'Edit', url: function ($row) {
            $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
        }, icon: Icon::PENCIL);
        // ...
    }
}
```

**Visibility**

By default, the row action button will always appear. If you want to hide the row action button under certain
conditions, you can use the `visible` method as follows:

```php
use CrudBooster\Components\Icon\Icon;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->addActionButton(label: 'Edit', url: function ($row) {
            $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
        }, icon: Icon::PENCIL)->visible(function ($row) {
            return $row->id != 1;
        });
        
        // or directly
        $this->addActionButton(label: 'Edit', url: function ($row) {
            $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
        }, icon: Icon::PENCIL)->visible(false);
        // ...
    }
}
```

**Custom Button Class**

You can also add a custom class to the row action button. Here is an example of adding a custom class to the row action
button:

```php
use CrudBooster\Components\Icon\Icon;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->addActionButton(label: 'Edit', url: function ($row) {
            $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
        }, icon: Icon::PENCIL)->buttonClass('btn btn-primary');
        // ...
    }
}
```

**Enable Confirmation**

By default, when the user clicks on this row action button, a confirmation dialog will appear. If you want to disable
the confirmation dialog, you can use the `enableConfirmation` method as follows:

```php
use CrudBooster\Components\Icon\Icon;

class User extends BaseBrowseComponent
{
    public function init(): void
    {
        $this->addActionButton(label: 'Edit', url: function ($row) {
            $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
        }, icon: Icon::PENCIL)->confirmation();
        // ...
    }
}
```

**Permission**

You can also add permissions to the row action button, so the button will only appear if the user has the required
permission. Here is an example of using permissions on a row action button:

```php
use CrudBooster\Modules\Role\Enum\RolePermission;

$this->addActionButton(label: 'Edit', url: function ($row) {
    $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
}, icon: Icon::PENCIL)->permission(RolePermission::EDIT);

// or multiple permission
$this->addActionButton(label: 'Edit', url: function ($row) {
    $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
}, icon: Icon::PENCIL)->permission([RolePermission::EDIT, RolePermission::DELETE]);
```

**Action Button Mode**

CRUDBooster provides two display modes for action buttons: inline (default) and dropdown (threedot). You can control how action buttons are displayed by setting the `$actionButtonMode` property in your browse component.

**Inline Mode (Default)**
By default, action buttons are displayed inline as separate buttons in each row:

```php
class User extends BaseBrowseComponent
{
    public $actionButtonMode = 'inline'; // Default mode
    
    public function init(): void
    {
        $this->addActionButton(label: 'Edit', url: function ($row) {
            $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
        }, icon: Icon::PENCIL);
        
        $this->addActionButton(label: 'Delete', url: function ($row) {
            $this->redirect(route('crudbooster.user.delete', ['id' => $row->id]));
        }, icon: Icon::TRASH);
    }
}
```

**Dropdown Mode (Threedot)**
To display action buttons in a dropdown menu with a 3-dot icon, set the mode to `'threedot'`:

```php
class User extends BaseBrowseComponent
{
    public $actionButtonMode = 'threedot'; // Dropdown mode
    
    public function init(): void
    {
        $this->addActionButton(label: 'Edit', url: function ($row) {
            $this->redirect(route('crudbooster.user.edit', ['id' => $row->id]));
        }, icon: Icon::PENCIL);
        
        $this->addActionButton(label: 'Delete', url: function ($row) {
            $this->redirect(route('crudbooster.user.delete', ['id' => $row->id]));
        }, icon: Icon::TRASH);
        
        $this->addActionButton(label: 'View Details', url: function ($row) {
            $this->redirect(route('crudbooster.user.show', ['id' => $row->id]));
        }, icon: Icon::EYE);
    }
}
```

**Note:** When using dropdown mode, all action buttons (including default Edit, Delete, and Detail buttons) will be displayed in the dropdown menu. The dropdown automatically handles button visibility based on permissions and visibility callbacks.

---

## **Form Page**

### Add Form Field

Now let's learn how to add input fields to the form. To create a form class, always make sure to extend
the `CrudBooster\Livewire\BaseFormComponent` class. Then, add the fields in the `init()` method; create or override
the `init()` method if it doesn't exist.
Finally, don't forget to wrap it with the `makeForm()` function. Here is an example of adding a field to the form:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text'),
        ]);
    }
}
```

Above is a basic example of adding a form field. Always make sure to fill in the 3 required arguments: `label`, `key`,
and `type`.
Creating vertical form fields might be boring? You can also create horizontal form fields as follows:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class BooksForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            [
                Form::add(label: 'Title', key:'title', type: 'text'),
                Form::add(label: 'SKU', key:'sku', type: 'sku'),
            ],
            Form::add(label: 'Description', key:'description', type: 'trix'),
        ]);
    }
}
```

With the above method, the form input fields will look something like this:
<br/><br/>
<img alt="CRUDBooster" src="https://crudbooster.com/images/docs/horizontal-form-input.png" width="400px"/>

And the bellow any other options that you can use:

- `validation` (string): You can add this option, and the value is validation rule from laravel. See the rules
  on [Laravel Validation Rules](https://laravel.com/docs/11.x/validation#available-validation-rules)
- `placeholder` (string): You can add this option, and the value is placeholder text.
- `helpText` (string): You can add this option, and the value is help text.
- `readonly` (boolean): You can add this option, and the value is true or false.
- `bindValue` (string): This option is used to bind the value from other fields. The value is other field key. I will
  explain this later in the next section.

> Tips: If you want to create a 2-column grid but do not want to use the 2nd column, use `Form::empty()` in the 2nd
> array.

**EmptyField Type**

The `Form::empty()` method creates an empty field placeholder that doesn't render any input element. This is useful for creating multi-column layouts where you want to leave some columns empty.

**Usage:**
```php
$this->makeForm([
    [
        Form::add(label: 'Name', key: 'name', type: 'text'),
        Form::empty(), // Creates an empty column
    ],
    Form::add(label: 'Description', key: 'description', type: 'textarea'),
]);
```

**Properties:**
- **Type:** `empty`
- **Key:** `empty`
- **Visible:** `true`
- **Show Detail:** `false`
- **Show Edit:** `false`
- **Show Create:** `false`

**Note:** Empty fields are automatically hidden in detail, edit, and create forms since they don't contain any actual input elements. They are only used for layout purposes in the form design.

---

### Show Field On Detail Page

If you want to show a field on the detail page, you can use the `showDetail` function. Here is an example of using
the `showDetail` function:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text')->showDetail(),
        ]);
    }
}
```

In the example above, we use the `showDetail` function to show the `name` field on the detail page.
By default, all fields in the form will automatically be displayed on the detail page. So, if you do not
use `showDetail`, the field will automatically be displayed on the detail page.
If you want to hide a field on the detail page, set the value of `showDetail` to `false`. Here is an example of
using `showDetail`:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text')->showDetail(false),
        ]);
    }
}
```

### Show On Create or Edit 

> Note: This feature is only available in CRUDBooster version 7.2.x and above.

If you want to show a field only on the create page or only on the edit page, you can use the `showCreate` and `showEdit` functions. 
By default, all fields in the form will automatically be displayed on the create and edit pages. So, if you do not use `showCreate` or `showEdit`, the field will automatically be displayed on the create and edit pages.
Here is an example of using the `showCreate` and `showEdit` functions:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text')->showCreate(false),
            Form::add(label: 'Name', key:'name', type: 'text')->showEdit(false),
        ]);
    }
}
```

---

### Password Input Case

If you want to create a password input, you can use the `password` type. But the question is, how to make the password
required when creating but not required when editing. Here is an example of using the `password` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Password', key: 'password', type: 'password', validation: $this->formId ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6', placeholder: $this->formId ? 'Leave empty if not changed' : 'Required')->showDetail(false),
            Form::add(label: 'Password Confirmation', key: 'password_confirmation', type: 'password', placeholder: $this->formId ? 'Leave empty if not changed' : 'Required')->showDetail(false)
        ]);
    }
}
```

In the example above, we make the validation dynamic based on the `formId` condition. If `formId` is not empty, the
password validation becomes `nullable|confirmed|min:6`, if empty, the password validation
becomes `required|confirmed|min:6`.
The `formId` variable is a "magic" variable that will always be present in the form. If `formId` is not empty, the
form is in edit mode, if empty, the form is in create mode.
---

### Transform Field

Sometimes we want to change the data displayed in the field. To do this, we can use the `transform` function. Here is an
example of using the `transform` function:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text')->transform(function ($value) {
                return strtoupper($value);
            }),
        ]);
    }
}
```

In the example above, we use the `transform` function to convert the `name` field data to uppercase.

---

Sure! Here's a more informative version of the documentation regarding setting a default value for a field using the `default` function, translated into English.

---

### Default Value

Setting a default value for a field can enhance user experience by pre-filling fields with commonly used or expected values. In CRUDBooster's Livewire component, you can easily set a default value using the `default` function.

**Example of Using the `default` Function**

Below is an example demonstrating how to set a default value for a text input field in a form:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...

    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key: 'name', type: 'text')->default('John Doe'),
        ]);
    }
}
```

The `default` function is called with the argument 'John Doe', which sets the initial value of the field to "John Doe".

**Conclusion**

By using the `default` function, you can easily set default values for form fields in CRUDBooster's Livewire components. This feature can help streamline the data entry process and improve the overall user experience. 


### Readonly

If you want to make a field readonly with a condition, you can use the `readonlyOn` function. Here is an example of
using the `readonlyOn` function:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text')->readonlyOn(function ($data) {
                return $this->formId ? true : false;
            }),
        ]);
    }
}
```

In the example above, we use the `readonlyOn` function to make the `name` field readonly based on the `formId`
condition. If `formId` is not empty, the `name` field will be readonly, if empty, the `name` field will not be
readonly.
You can use the `$data` variable, which is a map array containing data from the form.

To make a field readonly without any conditions, you can use the readonly() method. Here is an example of how to do this:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key: 'name', type: 'text')->readonly(),
        ]);
    }
}
```

---

### Nested Fields (Province > City)

When creating input fields for selecting a Province and a City, it is essential to ensure that the City options are dynamically populated based on the selected Province. This means that the user must first select a Province, and then the City dropdown will be automatically filled with the corresponding cities for that Province.

Below is an example code snippet that demonstrates how to implement this functionality:

```php
<?php

namespace App\Cb\Modules\Customers\Livewire;

use App\Cb\Modules\Cities\Models\Cities;
use App\Cb\Modules\Customers\Services\CustomersService;
use App\Cb\Modules\Customers\Models\Customers as CustomersModel;
use App\Cb\Modules\Provinces\Models\Provinces;
use CrudBooster\Livewire\BaseFormComponent;
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class CustomersForm extends BaseFormComponent
{
    public $pageTitle = "Customers";
    protected $modelService = CustomersService::class;
    protected $modelName = CustomersModel::class;

    public function init()
    {
        $this->makeForm([
            Form::add(label: "Name", key: "name", type: "text", helpText: "Input the Name here")->showDetail(true),
            Form::add(label: "Province", key: "provinces_id", type: "select", helpText: "Select the Province here")
                ->option(Select::option()->model(modelName: Provinces::class, key: 'id', label: 'name'))
                ->showDetail(true),
            Form::add(label: "City", key: "cities_id", type: "select", validation: "", placeholder: "", helpText: "Select the City here")
                ->option(Select::option()->model(Cities::class, 'id', 'name', function ($query) {
                    // Filter cities based on the selected province
                    $query->where('provinces_id', $this->formData['provinces_id'] ?? 0);
                }))
                ->showDetail(true),
        ]);
    }
}
```

**Dynamic Update of City Field**

In the example code above, the City input field is set up to automatically update its options based on the selected Province. By adding a callback query that filters the cities with `WHERE provinces_id = $this->formData['provinces_id']`, the City dropdown will refresh its options whenever the user selects a Province. This is made possible by the power of Livewire, which allows for real-time updates without requiring a full page reload.

Remember again, `$this->formData` is a "magic" property that contains the form data works on BaseFormComponent area. By using `$this->formData['provinces_id']`, we can access the selected Province ID and use it to filter the City options accordingly.

**Enhanced Callback Query with $id Parameter (CRUDBooster v7.6.27+)**

Starting from CRUDBooster v7.6.27, callback queries now support an additional `$id` parameter that provides access to the current form data ID being edited. This enables more sophisticated filtering and context-aware options.

```php
Form::add(label: "City", key: "cities_id", type: "select")
    ->option(Select::option()->model(Cities::class, 'id', 'name', function ($query, $id = null) {
        // Filter cities based on the selected province
        $query->where('provinces_id', $this->formData['provinces_id'] ?? 0);
        
        // Additional filtering based on current record
        if ($id) {
            $currentRecord = MainModel::find($id);
            if ($currentRecord) {
                // Filter based on current record's data
                $query->where('region_id', $currentRecord->region_id);
            }
        }
    }))
```

**Benefits of the $id Parameter:**

1. **Context-Aware Filtering**: Filter options based on the current record being edited
2. **Permission-Based Options**: Show different options based on user permissions or record ownership
3. **Relationship Filtering**: Filter related options based on parent record data
4. **Dynamic Validation**: Apply different validation rules based on existing data
5. **Backward Compatibility**: Old callback queries continue to work without modification

**Usage Examples:**

```php
// Filter based on current user's role
Form::add(label: 'Departments', key: 'department_id', type: 'select')
    ->option(Select::option()
        ->model(Department::class, 'id', 'name', function($query, $id = null) {
            if ($id) {
                $currentUser = User::find($id);
                if ($currentUser && $currentUser->role === 'manager') {
                    // Managers can see all departments
                    $query->where('status', 'active');
                } else {
                    // Regular users can only see their own department
                    $query->where('id', $currentUser->department_id ?? 0);
                }
            } else {
                // For new users, show all active departments
                $query->where('status', 'active');
            }
        }));

// Filter based on parent record
Form::add(label: 'Subcategories', key: 'subcategory_id', type: 'select')
    ->option(Select::option()
        ->model(Subcategory::class, 'id', 'name', function($query, $id = null) {
            if ($id) {
                $currentRecord = MainModel::find($id);
                if ($currentRecord) {
                    $query->where('category_id', $currentRecord->category_id);
                }
            }
            return $query->where('status', 'active');
        }));
```

**Setting Form Configuration:**

When configuring callback queries in the setting form, you can use the new `$id` parameter:

```php
// Basic example
'function($query, $id = null) { 
    if ($id) {
        $query->where("parent_id", $id);
    }
    return $query->where("status", "active"); 
}'

// Complex example with multiple conditions
'function($query, $id = null) { 
    if ($id) {
        $currentRecord = App\Models\MainModel::find($id);
        if ($currentRecord) {
            $query->where("category_id", $currentRecord->category_id)
                  ->where("created_by", $currentRecord->created_by)
                  ->where("is_visible", true);
        }
    } else {
        $query->where("is_public", true);
    }
    return $query->orderBy("name"); 
}'
```

**Eloquent Query Builder**
The callback query used in the City field is based on Eloquent Query Builder. This means you can explore more advanced query options if needed. You can add additional conditions, joins, or any other Eloquent features to customize the query according to your requirements.

---

### Dynamic Form Fields (onChange Event)

In this example, we are creating a form for managing transaction items in an application. The form includes fields for selecting a product, entering its price, quantity, and calculating the total price based on the selected product and quantity. The key feature of this implementation is the dynamic update of the product price and total price when the user selects a product from the dropdown. This is achieved using the `onChange` event.

Below is the code snippet demonstrating this functionality:

```php
<?php

namespace App\Cb\Modules\TransactionItems\Livewire;

use App\Cb\Modules\Products\Models\Products;
use App\Cb\Modules\TransactionItems\Models\TransactionItems as TransactionItemsModel;
use App\Cb\Modules\TransactionItems\Services\TransactionItemsService;
use CrudBooster\Components\Type\Money\Function\Money;
use CrudBooster\Components\Type\Select\Function\Select;
use CrudBooster\Livewire\BaseFormComponent;
use CrudBooster\Livewire\FormBuilder\Form;

class TransactionItemsForm extends BaseFormComponent
{
    public $pageTitle = "Transaction Items";
    protected $modelService = TransactionItemsService::class;
    protected $modelName = TransactionItemsModel::class;

    public function init()
    {
        $this->makeForm([
            Form::add(label: "Transactions Id", key: "transactions_id", type: "text", validation: "", placeholder: "", helpText: "Input the Transactions Id here")->showDetail(true),
            Form::add(label: "Product", key: "product_id", type: "select", validation: "required", placeholder: "", helpText: "Select a product")
                ->option(Select::option()->model(Products::class, 'id', 'name'))
                ->onChange(function ($value) {
                    $product = Products::find($value);
                    $this->formData['product_price'] = $product->price;
                    $this->formData['total_price'] = $product->price * $this->formData['quantity'];
                })
                ->showDetail(true),
            Form::add(label: "Price", key: "product_price", type: "money", validation: "required", placeholder: "", helpText: "Input the Product Price here")
                ->option(Money::option())->showDetail(true),
            Form::add(label: "Quantity", key: "quantity", type: "number", validation: "", placeholder: "", helpText: "Input the Quantity here")->showDetail(true),
            Form::add(label: "Total Price", key: "total_price", type: "money", validation: "", placeholder: "", helpText: "Input the Total Price here")->option(Money::option())->showDetail(true),
        ]);
    }
}
```

**Explanation of the onChange Event**

In the code above, the `onChange` event is implemented for the "Product" select field. Here's how it works:

- **Dynamic Price Update**: When a user selects a product from the dropdown, the `onChange` callback is triggered. This callback receives the selected product's ID as its parameter (`$value`).

- **Fetching Product Details**: The callback uses Eloquent to find the product by its ID:
  ```php
  $product = Products::find($value);
  ```

- **Updating Form Fields**:
  - The `product_price` field is updated with the price of the selected product:
    ```php
    $this->formData['product_price'] = $product->price;
    ```
  - The `total_price` field is calculated by multiplying the product price by the quantity entered in the `quantity` field:
    ```php
    $this->formData['total_price'] = $product->price * $this->formData['quantity'];
    ```

### Show On

If you want to show a field with a condition, you can use the `showOn` function. Here is an example of using
the `showOn` function:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text')->showOn(function ($data) {
                return $this->formId ? true : false;
            }),
        ]);
    }
}
```

In the example above, we use the `showOn` function to show the `name` field based on the `formId` condition.
If `formId` is not empty, the `name` field will be shown, if empty, the `name` field will not be shown.
You can use the `$data` variable, which is a map array containing data from the form.

---

### Input Types

#### Text

To create a text input, you can use the `text` type. Here is an example of using the `text` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text'),
        ]);
    }
}
```

In the example above, we use the `text` type to create a text input for the `name` field.

#### Textarea

To create a textarea input, you can use the `textarea` type. Here is an example of using the `textarea` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Description', key:'description', type: 'textarea'),
        ]);
    }
}
```

In the example above, we use the `textarea` type to create a textarea input for the `description` field.
To adjust height of textarea, you can use `Textarea::option()->heightRow(...)` function. Here is an example of using
the `heightRow` function:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Textarea\Function\Textarea;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Description', key:'description', type: 'textarea')->option(Textarea::option()->heightRow(5));
        ]);
    }
}
```

#### Trix (WYSIWYG Editor)

This type is used to create a trix input. [Trix](https://github.com/basecamp/trix) is a WYSIWYG editor that is easy to
use. Here is an example of using the `trix` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Description', key:'description', type: 'trix'),
        ]);
    }
}
```

#### TinyMCE (WYSIWYG Editor)

This type is used to create a TinyMCE input. [TinyMCE](https://www.tiny.cloud/) is a WYSIWYG editor that is easy to use.
Here is an example of using the `tinymce` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Description', key:'description', type: 'tinymce'),
        ]);
    }
}
```

And, if you want to adjust the height of the TinyMCE input, you can use the `TinyMCE::option()->height()` function. Here
is an example of using the function:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\TinyMCE\Function\TinyMCE;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Description', key:'description', type: 'tinymce')->option(TinyMCE::option()->height(500)),
        ]);
    }
}
```

The value of height is in pixels.
Additionally, for the TinyMCE type, we recommend using an API Key from TinyMCE. You can register
at [TinyMCE](https://www.tiny.cloud/).
Then add a new key in the `.env` file with the name `TINYMCE_KEY`.
Here is an example of changes to the `.env` file:

```env
TINYMCE_KEY=your-api-key
```

And clear the config cache by running the following command `php artisan config:cache`.

#### Summernote (WYSIWYG Editor)

> This feature is available in CRUDBooster v7.9.12 and above.

This type is used to create a Summernote input. [Summernote](https://summernote.org/) is a WYSIWYG editor that provides a clean and intuitive interface for rich text editing. Here is an example of using the `summernote` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Content', key:'content', type: 'summernote'),
        ]);
    }
}
```

**With Options**

You can customize the Summernote editor with various options:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Summernote\Function\Summernote;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Content', key:'content', type: 'summernote')
                ->option(Summernote::option()
                    ->height(400)
                    ->autoReformat()
                ),
        ]);
    }
}
```

**Available Options**

- **height(int $height)**: Set the editor height in pixels (default: 300)
- **autoReformat(bool $enabled = true)**: Enable auto-reformat for pasted content from Word, Google Docs, etc.

**Features**

- **Image Upload**: Drag and drop or click to upload images directly into the editor
- **Auto-Reformat**: Automatically cleans pasted content from external sources
- **Responsive Design**: Works well on desktop and mobile devices
- **CSS Scoped**: Prevents conflicts with existing CSS frameworks like Tailwind
- **Livewire Integration**: Seamless integration with Livewire components
- **SPA Navigation**: Supports single-page application navigation

**Image Upload**

Summernote supports image upload functionality. Images are automatically uploaded to the `storage/app/public/summernote/images/` directory and served through the public storage link.

**Example Usage**

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Summernote\Function\Summernote;

class ArticleForm extends BaseFormComponent
{
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Title', key: 'title', type: 'text'),
            Form::add(label: 'Content', key: 'content', type: 'summernote')
                ->option(Summernote::option()
                    ->height(500)
                    ->autoReformat()
                ),
            Form::add(label: 'Status', key: 'status', type: 'select')
                ->option(Select::option()
                    ->dataset([
                        ['key' => 'draft', 'label' => 'Draft'],
                        ['key' => 'published', 'label' => 'Published']
                    ])),
        ]);
    }
}
```

**Auto-Reformat Feature**

When enabled, the auto-reformat feature automatically cleans pasted content by:
- Removing unwanted HTML tags and attributes
- Converting to clean, semantic HTML
- Removing inline styles and classes
- Cleaning up formatting from Word, Google Docs, and other sources

**CSS Scoping**

The Summernote component uses scoped CSS to prevent conflicts with existing frameworks. All Summernote styles are contained within the `.summernote-container` class, ensuring compatibility with Tailwind CSS and other CSS frameworks.

#### URL

To create a URL input, you can use the `url` type. Here is an example of using the `url` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Website', key:'website', type: 'url'),
        ]);
    }
}
```

In the example above, we use the `url` type to create a URL input for the `website` field.

#### Date

To create a date input, you can use the `date` type. Here is an example of using the `date` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Birth Date', key:'birth_date', type: 'date'),
        ]);
    }
}
```

In the example above, we use the `date` type to create a date input for the `birth_date` field.
You can change the date format displayed on the detail page by adding the following option:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Date\Function\Date;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Birth Date', key:'birth_date', type: 'date')->option(Date::option()->format('d-m-Y')),
        ]);
    }
}
```

In the example above, we use the `format` function to change the date format to `d-m-Y`.

#### Date & Time

To create a date & time input, you can use the `datetime` type. Here is an example of using the `datetime` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Birth Date', key:'birth_date', type: 'datetime'),
        ]);
    }
}
```

In the example above, we use the `datetime` type to create a date & time input for the `birth_date` field.
You can change the date format displayed on the detail page by adding the following option:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\DateTime\Function\DateTime;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Birth Date', key:'birth_date', type: 'datetime')->option(DateTime::option()->format('d-m-Y H:i:s')),
        ]);
    }
}
```

In the example above, we use the `format` function to change the date format to `d-m-Y H:i:s`.

#### Time

> Only support for CRUDBooster version 7.6.6 or above

To create a time input, you can use the `time` type. Here is an example of using the `time` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Join Time', key:'join_time', type: 'time'),
        ]);
    }
}
```

#### Email

To create an email input, you can use the `email` type. Here is an example of using the `email` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Email', key:'email', type: 'email'),
        ]);
    }
}
```

In the example above, we use the `email` type to create an email input for the `email` field.

#### Money

To create a money input, you can use the `money` type. Here is an example of using the `money` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Salary', key:'salary', type: 'money'),
        ]);
    }
}
```

In the example above, we use the `money` type to create a money input for the `salary` field.

#### Number

To create a number input, you can use the `number` type. Here is an example of using the `number` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Age', key:'age', type: 'number'),
        ]);
    }
}
```

In the example above, we use the `number` type to create a number input for the `age` field.

#### Password

To create a password input, you can use the `password` type. Here is an example of using the `password` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Password', key:'password', type: 'password'),
        ]);
    }
}
```

In the example above, we use the `password` type to create a password input for the `password` field.

#### File Upload

To create a file upload input, you can use the `file` type. Here is an example of using the `file` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Photo', key:'photo', type: 'file'),
        ]);
    }
}
```

In the example above, we use the `file` type to create a file upload input for the `photo` field.
You can limit file accept by using the `accept` option. Here is an example of using the `accept` option:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\File\Function\File;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Attachment', key:'attachment', type: 'file')->option(File::option()->accept('.pdf,.doc,.xls')),
        ]);
    }
}
```

In the example above, we use the `accept` function to limit the file accept to `.pdf`, `.doc`, and `.xls`.

#### Image Upload

To create an image upload input, you can use the `image` type. Here is an example of using the `image` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Photo', key:'photo', type: 'image'),
        ]);
    }
}
```

In the example above, we use the `image` type to create an image upload input for the `photo` field.

If you want to upload more than one image or multiple images, simply add the `multiple` option as follows:

```php
use CrudBooster\Livewire\FormBuilder\Form;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Photos', key:'photos', type: 'image')->option(\CrudBooster\Components\Type\Image\Function\Image::option()->multiple()),
        ]);
    }
}
```

In the example above, we use the `multiple` function to create multiple image upload input for the `photos` field.

> The multiple image upload feature is only available in CRUDBooster version 7.5 and above.
> Ensure the data type of the field you use is `TEXT` or `JSON` in the database. This is because multiple image uploads will store data in JSON format.

#### Checkbox

To create a checkbox input, you can use the `checkbox` type. Here is an example of using the `checkbox` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Tags', key:'tags', type: 'checkbox')
                ->option(Checkbox::option()
                    ->dataset([
                    ['key' => 'foo','label'=>'Foo'],
                    ['key' => 'bar','label'=>'Bar']
                    ])),
        ]);
    }
}
```

In the example above, we use the `checkbox` type to create a checkbox input for the `tags` field. We also use
the `dataset` function to set the checkbox value.
Now maybe you want to lookup from a model / dynamic data, then you can use `model`. Here is an example of using
the `model` function:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Roles', key:'roles', type: 'checkbox')
                ->option(Checkbox::option()
                    ->model(\App\Cb\Modules\Roles::class, 'id', 'name')),
        ]);
    }
}
```

In the example above, we use the `model` function to set the checkbox value from the `roles` table. The first argument
is the model class, the second argument is the key column, and the third argument is the label column.
Sometimes you want to add special conditions to the model query, here is how:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Roles', key:'roles', type: 'checkbox')
                ->option(Checkbox::option()
                    ->model(\App\Cb\Modules\Roles::class, 'id', 'name', function ($query) {
                        $query->where('active', 1);
                    })),
        ]);
    }
}
```

In the example above, we use the `model` function to set the checkbox value from the `roles` table. The first argument
is the model class, the second argument is the key column, the third argument is the label column, and the fourth
argument is the query condition.
This query is an instance of Laravel's Query Builder, so you can manipulate the query as you wish.

**Label Transformation**
> This feature available for version 7.6.23 and above.

You can customize the display of checkbox labels using the `transformLabel` function. This allows you to modify how labels are displayed without changing the underlying data. Here are some examples:

**Basic Transformation:**
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Status', key: 'status', type: 'checkbox')
                ->option(Checkbox::option()
                    ->dataset([
                        ['key' => 'active', 'label' => 'active'],
                        ['key' => 'inactive', 'label' => 'inactive']
                    ])
                    ->transformLabel(function($label, $key, $row) {
                        return strtoupper($label);
                    })),
        ]);
    }
}
```

**With Model and Transformation:**
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Roles', key: 'roles', type: 'checkbox')
                ->option(Checkbox::option()
                    ->model(\App\Cb\Modules\Roles::class, 'id', 'name')
                    ->transformLabel(function($label, $key, $row) {
                        return ucfirst($label) . ' (ID: ' . $key . ')';
                    })),
        ]);
    }
}
```

**With Model Fields (All Fields Available):**
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Users', key: 'users', type: 'checkbox')
                ->option(Checkbox::option()
                    ->model(\App\Models\User::class, 'id', 'name')
                    ->transformLabel(function($label, $key, $row) {
                        // Access all User model fields via $row
                        $email = $row->email ?? '';
                        $status = $row->status ?? 'unknown';
                        $created_at = $row->created_at ?? '';
                        
                        $statusIcon = $status === 'active' ? '🟢' : '🔴';
                        return $statusIcon . ' ' . ucfirst($label) . ' (' . $email . ')';
                    })),
        ]);
    }
}
```

**Complex Model Transformation:**
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Checkbox\Function\Checkbox;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Products', key: 'products', type: 'checkbox')
                ->option(Checkbox::option()
                    ->model(\App\Models\Product::class, 'id', 'name')
                    ->transformLabel(function($label, $key, $row) {
                        // Access all product fields
                        $price = $row->price ?? 0;
                        $category = $row->category ?? '';
                        $stock = $row->stock ?? 0;
                        
                        $stockStatus = $stock > 0 ? '📦' : '❌';
                        $priceFormatted = number_format($price, 2);
                        
                        return $stockStatus . ' ' . ucfirst($label) . ' - $' . $priceFormatted . ' (' . $category . ')';
                    })),
        ]);
    }
}
```

The `transformLabel` function receives three parameters:
- `$label`: The current label text
- `$key`: The option key/value  
- `$row`: An object containing the full option data. For model data, this includes all model fields (e.g., `$row->email`, `$row->status`, `$row->created_at`, etc.)

#### Radio Button

To create a radio button input, you can use the `radio` type. Here is an example of using the `radio` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Radio\Function\Radio;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Status Active', key: 'status', type:'radio')
                ->option(Radio::option()
                    ->dataset([
                        ['key' => '1', 'label' => 'Active'],
                        ['key' => '0', 'label' => 'Inactive']
                    ])),
        ]);
    }
}
```

In the example above, we use the `radio` type to create a radio button input for the `status` field. We also use
the `dataset` function to set the radio button value.
Now, what about model data? You can use the `model` function. Here is an example of using the `model` function:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Radio\Function\Radio;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Status Active', key: 'status', type:'radio')
                ->option(Radio::option()
                    ->model(\App\Cb\Modules\Status::class, 'id', 'name')),
        ]);
    }
}
```

In the example above, we use the `model` function to set the radio button value from the `status` table. The first
argument is the model class, the second argument is the key column, and the third argument is the label column.
Sometimes you want to add special conditions to the model query, here is how:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Radio\Function\Radio;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Status Active', key: 'status', type:'radio')
                ->option(Radio::option()
                    ->model(\App\Cb\Modules\Status::class, 'id', 'name', function ($query) {
                        $query->where('active', 1);
                    })),
        ]);
    }
}
```

In the example above, we use the `model` function to set the radio button value from the `status` table. The first
argument is the model class, the second argument is the key column, the third argument is the label column, and the
fourth argument is the query condition.

#### Select

To create a select input, you can use the `select` type. Here is an example of using the `select` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Category', key: 'category_id', type: 'select')
                ->option(Select::option()
                    ->dataset([
                        ['key' => '1', 'label' => 'Category 1'],
                        ['key' => '2', 'label' => 'Category 2']
                    ])),
        ]);
    }
}
```

In the example above, we use the `select` type to create a select input for the `category_id` field. We also use
the `dataset` function to set the select value.
Now, what about model data? You can use the `model` function. Here is an example of using the `model` function:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Category', key: 'category_id', type: 'select')
                ->option(Select::option()
                    ->model(\App\Cb\Modules\Categories::class, 'id', 'name')),
        ]);
    }
}
```

In the example above, we use the `model` function to set the select value from the `categories` table. The first
argument is the model class, the second argument is the key column, and the third argument is the label column.
Sometimes you want to add special conditions to the model query, here is how:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Category', key: 'category_id', type: 'select')
                ->option(Select::option()
                    ->model(\App\Cb\Modules\Category\Model\Categories::class, 'id', 'name', function ($query) {
                        $query->where('active', 1);
                    })),
        ]);
    }
}
```

In the example above, we use the `model` function to set the select value from the `categories` table. The first
argument is the model class, the second argument is the key column, the third argument is the label column, and the
fourth argument is the query condition.

Dataset grouping can be done as follows:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Category', key: 'category_id', type: 'select')
                ->option(Select::option()
                    ->datasetGroup(
                        [
                            [
                            'label'=>'Group1',
                            'options' => [
                                    ['key' => '1', 'label' => 'Category 1'],
                                    ['key' => '2', 'label' => 'Category 2']
                                ],
                            ],
                            [
                            'label'=>'Group2',
                                'options' => [
                                    ['key' => '3', 'label' => 'Category 3'],
                                    ['key' => '4', 'label' => 'Category 4']
                                ],
                            ]
                        ],
                    ])),
        ]);
    }
}
```

In the example above, we use the `datasetGroup` function to set the select value with grouping. The first argument is an
array of groups, each group has a `label` and `options` key. The `label` key is the group name, and the `options` key is
an array of options.

**Searchable**
> This feature available for version 7.4.0 and above.

If you want to make the select searchable, you can use `searchable`. Here is an example of using `searchable`:
```php
use CrudBooster\Livewire\FormBuilder\Form;

use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Category', key: 'category_id', type: 'select')
                ->option(Select::option()
                    ->searchable()
                    ->model(\App\Cb\Modules\Category\Model\Categories::class, 'id', 'name', function ($query) {
                        $query->where('active', 1);
                    })),
        ]);
    }
}
```

**Label Transformation**
> This feature available for version 7.6.23 and above.

You can customize the display of option labels using the `transformLabel` function. This allows you to modify how labels are displayed without changing the underlying data. Here are some examples:

**Basic Transformation:**
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Status', key: 'status', type: 'select')
                ->option(Select::option()
                    ->dataset([
                        ['key' => 'active', 'label' => 'active'],
                        ['key' => 'inactive', 'label' => 'inactive']
                    ])
                    ->transformLabel(function($label, $key, $row) {
                        return strtoupper($label); // ACTIVE, INACTIVE
                    })),
        ]);
    }
}
```

**With Model and Transformation:**
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'User', key: 'user_id', type: 'select')
                ->option(Select::option()
                    ->model(\App\Models\User::class, 'id', 'name')
                    ->transformLabel(function($label, $key, $row) {
                        return ucfirst($label) . ' (ID: ' . $key . ')'; // John (ID: 1)
                    })),
        ]);
    }
}
```

**Complex Transformation with Emoji:**
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Status', key: 'status', type: 'select')
                ->option(Select::option()
                    ->dataset([
                        ['key' => 'active', 'label' => 'active'],
                        ['key' => 'inactive', 'label' => 'inactive']
                    ])
                    ->transformLabel(function($label, $key, $row) {
                        $status = $label === 'active' ? '🟢' : '🔴';
                        return $status . ' ' . ucfirst($label); // 🟢 Active, 🔴 Inactive
                    })),
        ]);
    }
}
```

**With Dataset Group:**
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\Select\Function\Select;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Category', key: 'category_id', type: 'select')
                ->option(Select::option()
                    ->datasetGroup([
                        [
                            'label' => 'Premium Categories',
                            'options' => [
                                ['key' => '1', 'label' => 'gold'],
                                ['key' => '2', 'label' => 'silver']
                            ]
                        ],
                        [
                            'label' => 'Standard Categories',
                            'options' => [
                                ['key' => '3', 'label' => 'bronze'],
                                ['key' => '4', 'label' => 'copper']
                            ]
                        ]
                    ])
                    ->transformLabel(function($label, $key, $row) {
                        return '⭐ ' . ucfirst($label); // ⭐ Gold, ⭐ Silver, etc.
                    })),
        ]);
    }
}
```

**Transform Function Parameters:**
- `$label` - The original label from the option
- `$key` - The key/value of the option
- `$row` - Complete option data (useful for accessing other fields)

**Setting Form Integration:**
You can also configure label transformation through the setting form by adding a "Label Transformation" field. This allows non-developers to customize label display without code changes.

**Error Handling:**
The system includes error handling for invalid transformation code. If the transformation function has errors, it will fall back to displaying the original label.


#### Select Chips (Multiple)

Select Chips is a select type that can accept more than one value. However, it is used to be stored in a Many To Many
table or a column that you set as JSON, as the values will be stored in JSON format. Here is an example of using
the `selectchips` type:

```php
function model(string $pivotModel, string $firstPivotFk, string $secondPivotFk, string $displayModel, string $displayColumn, Closure $displayQueryCallback = null)
```

Here is an example of using the `selectchips` type:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\SelectChips\Function\SelectChips;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Roles', key: 'roles', type: 'selectchips')
                ->option(SelectChips::option()
                    ->model(\App\Cb\Modules\Role\UserRoles::class, 'user_id', 'role_id', \App\Cb\Modules\Roles::class, 'name')),
        ]);
    }
}
```

In the example above, we use the `model` function to set the select value from the `roles` table. The first argument is
the pivot model class, the second argument is the key column from the main table, and the third argument is the key
column from the secondary table.
The fourth argument is the model class for displaying data, and the fifth argument is the name of the column to be
displayed. If you want to add special conditions to the model query, you can use the sixth argument.

Here is an example if you want to add additional query conditions:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\SelectChips\Function\SelectChips;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Roles', key: 'roles', type: 'selectchips')
                ->option(SelectChips::option()
                    ->model(\App\Cb\Modules\Role\UserRoles::class, 'user_id', 'role_id', \App\Cb\Modules\Roles::class, 'name', function ($query) {
                        $query->where('active', 1);
                    })),
        ]);
    }
}
```

In the example above, the sixth argument is an additional query condition.

#### Select Icon

Select icon merupakan komponen input yang akan menampilkan combo box list icon SVG yang ada di CRUDBooster. Anda bisa menggunakan ini apabil ingin 
menggunakan icon SVG yang sudah disediakan oleh CRUDBooster. Berikut contoh penggunaan `selectIcon`:
```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\SelectIcon\Function\SelectIcon;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add('Icon', 'icon', 'selectIcon'),
        ]);
    }
}
```

<img src='https://crudbooster.com/images/docs/select-icon.png' width="400px"/>

Saat di save, akan tersimpan berupa `enum` icon yang tersedia di CRUDBooster. Kemudian untuk menampilkan icon tersebut, Anda bisa menggunakan helper `cbIcon`:
```php
{!! cbIcon($data->icon) !!}
```


#### Json Checklist

Json Checklist is an input type that will generate a table with a checklist column. A simple example is for permission
checklists. As shown in the following image:
<br/><br/>
<img alt="CRUDBooster" src="https://crudbooster.com/images/docs/preview-json-checklist.png" width="700px"/>

When the user saves the data, it will be stored in JSON format. Here is an example of using `jsonchecklist`:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\JsonChecklist\Function\JsonChecklist;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add('Permissions', 'permissions', 'jsonChecklist')
                ->option(JsonChecklist::option()->dataset([
                ['key'=>'product','label'=>'Product'],
                ['key'=>'customer','label'=>'Customer'],
            ], 'Module', ['Create', 'Read', 'Update', 'Delete'])),
        ]);
    }
}
```

The first argument is the list of items. The second argument is the title of the item list. The third argument is an
array of checklist columns.
When the data is saved, it will generate JSON like the following:

```json
{
    "product": {
        "read": true,
        "create": false,
        "delete": true,
        "update": true
    },
    "customer": {
        "read": true,
        "create": true,
        "delete": false,
        "update": false
    }
}
```

This JSON will be stored in the `permissions` column in JSON format.

#### Json Table

Json Table is a bit more advanced than Json Checklist. In Json Table, you can change the type of input fields such as
text, number, and checkbox.
Json Table is more suitable for questionnaire-type inputs.
Here is an example of using `jsontable`:

```php
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Components\Type\JsonTable\Function\JsonTable;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add('Questioner', 'questioner', 'jsonTable')
                ->option(JsonTable::option()->dataset(
                items: ['Whats your name?', 'How old are you?', 'Do you like programming?'], 
                itemLabel: 'Question Name', 
                inputs: [
                    ['name'=>'Answer','type'=>'text']
                ])),
        ]);
    }
}
```

The first argument is the list of items. The second argument is the title of the item list. The third argument is an
array of checklist columns.
When the data is saved, it will generate JSON like the following:

```json
{
    "Whats your name?": {
        "Answer": "John Doe"
    },
    "How old are you?": {
        "Answer": "25"
    },
    "Do you like programming?": {
        "Answer": "Yes"
    }
}
```

## **Extra**

### Alert Message

You can display an alert message using the `showAlertMessage` method. Make sure you have extended
the `BaseBrowseComponent` or `BaseFormComponent`. If not, you need to inject the `WithAlertMessage` trait.

```php
use CrudBooster\Livewire\BaseFormComponent;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function store(): void
    {
        $this->showAlertMessage(message: 'Data has been saved!', type: 'success'); // type = success, warning, danger, info
        $this->redirect(route('crudbooster.user.index'));
    }
}
```

### Icon Collection

You can use the icon collection from CRUDBooster. Here is an example of using the icon collection:

```php
use CrudBooster\Components\Icon\Icon;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Name', key:'name', type: 'text', icon: Icon::USER),
        ]);
    }
}
```

In the example above, we use the `Icon::USER` icon to create an icon for the `name` field. You can see the list of icons
in the `CrudBooster\Components\Icon\Icon` class.

This icon is SVG, from [Heroicons](https://heroicons.com/).

---

## **Advanced**

### Event Listener

CRUDBooster now uses event listeners to handle interception processes, instead of using the hook function concept.

#### Using Laravel Event

You can use the following events to make a listener in your Laravel project. This method is suitable for global events.
For example if you want to make a listener when a user failed logs in:

```php
use CrudBooster\Events\LoginAttemptFailed;

class LogFailedLogin
{
    /**
     * Handle the given event.
     */
    public function handle(LoginAttemptFailed $event): void
    {
        // Make a log here
        // You may get the user email from the event
        Log::debug('Oops user failed login, user email : ' . $event->email);
    }
}
```

Below are the events used in CRUDBooster, which are standard Laravel events.  
Namespace location: `CrudBooster\Events`

##### EventBrowseColumnRendering

Triggered during the process of rendering table columns (browse columns) in CRUDBooster.

**Payload:**

- `$model` — The model class for the related data.
- `$rowData` — An array of data being rendered.
- `$column` — The names of the columns being rendered.

---

##### EventBrowseRendering

Triggered during the process of rendering the browse page.

**Payload:**

- `$model` — The model class for the related data.

---

##### EventDataDeleted

Triggered after a data deletion process is completed.

**Payload:**

- `$model` — The model class for the related data.
- `$data` — An array containing the data before it was deleted.
- `$uuid` — The UUID of the related data.

---

##### EventDataDeleting

Triggered before the data deletion process begins.

**Payload:**

- `$model` — The model class for the related data.
- `$data` — An array containing the data before it is deleted.
- `$uuid` — The UUID of the related data.

---

##### EventFormGettingData

Triggered before the process of reading data for editing or viewing details.

**Payload:**

- `$model` — The model class for the related data.
- `$data` — An array of the related data.
- `$uuid` — The UUID of the related data.

---

##### EventFormGetData

Triggered after the data reading process is completed during editing or viewing details.

**Payload:**

- `$model` — The model class for the related data.
- `$data` — An array of the related data.
- `$uuid` — The UUID of the related data.

---

##### EventFormInit

Triggered when initializing form components for the first time.

**Payload:**

- `$model` — The model class for the related data.

---

##### EventFormMounting

Triggered during the mounting process, before it is fully completed.

**Payload:**

- `$model` — The model class for the related data.

---

##### EventFormSaving

Triggered before saving data, whether adding or editing data.

**Payload:**

- `$model` — The model class for the related data.
- `$data` — An array of the related data.
- `$uuid` — The UUID of the related data (can be `null` when adding new data as the UUID has not been generated yet).

---

##### EventFormSaved

Triggered after saving data is completed, whether adding or editing data.

**Payload:**

- `$model` — The model class for the related data.
- `$data` — An array of the related data.
- `$uuid` — The UUID of the related data.

---

##### EventFormValidated

Triggered after the validation process is completed for adding or editing data.

**Payload:**

- `$model` — The model class for the related data.
- `$data` — An array of the related data.
- `$uuid` — The UUID of the related data (can be `null` when adding new data as the UUID has not been generated yet).

##### LoginAttemptFailed

Triggered when a user fails to log in.

**Payload:**

- `$email` — The email address of the user who attempted to log in.

##### LoginAttemptSuccess

Triggered when a user successfully logs in.

**Payload:**

- `$user` — The model class instance from the user table.

Read more about events & listener in Laravel [here](https://laravel.com/docs/11.x/events)

#### Event Attributes Style

Unlike Laravel event listeners, in event attributes, we can listen to events generated by CRUDBooster, especially for
the component class we are using.
In other words, this CB event attribute is more isolated to the related module. It can only listen to events generated
by the component class we are using.
For example, if we want to listen to an event when the form is successfully saved:

```php
use CrudBooster\Attributes\OnFormSaved;

public function init() {
    // init code goes here
}

#[OnFormSaved]
public function yourPostFormSavedListenFunction($model, $data, $uuid = null)
{
    // ...
}
```

Ensure the arguments created matches the one generated by the event.

And if you want to make a listener when before the form is saved:

```php
use CrudBooster\Attributes\OnFormSaving;
// ...

public function init() {
    // init code goes here
}

#[OnFormSaving]
public function yourPreFormSavedListenFunction($model, $data, $uuid = null)
{
    // ...
}
```

At the sample above, we use the `OnFormSaving` attribute to listen to the event before the form is saved. Only applied
for the form component class.

CRUDBooster supports a series of attribute-based events that are isolated per module. These events can be used to add
custom functionality to specific components. Below is a list of available events, along with detailed descriptions,
payloads, and applicable components.

---

##### OnDragged

Triggered after the drag-and-drop operation is completed in the browse feature.

**Payload:**

- `$ids` — An array of IDs that were reordered via drag-and-drop.

**Applicable:**

- **Browse Component**

**Example Use Case:**  
Update the position of records in the database after a drag-and-drop operation.

---

##### OnDataDeleted

Triggered after data has been successfully deleted.

**Payload:**

- `$model` — The model class associated with the deleted data.
- `$data` — An array containing the details of the deleted data.
- `$uuid` — The unique identifier (`uuid`) of the deleted data.

**Applicable:**

- **Browse Component**

**Example Use Case:**  
Log or notify an admin about the deletion of specific records.

---

##### OnDataDeleting

Triggered before data is deleted.

**Payload:**

- `$model` — The model class associated with the data to be deleted.
- `$data` — An array containing the details of the data to be deleted.
- `$uuid` — The unique identifier (`uuid`) of the data to be deleted.

**Applicable:**

- **Browse Component**

**Example Use Case:**  
Check permissions or confirm dependencies before allowing the deletion.

---

##### OnBrowseColumnRendering

Triggered when a browse column is being rendered.

**Payload:**

- `$model` — The model class associated with the data being rendered.
- `$data` — An array of data being rendered.
- `$column` — A list of columns being rendered.

**Applicable:**

- **Browse Component**

**Example Use Case:**  
Customize column data formatting, such as adding icons or links dynamically.

---

##### OnBrowseRendering

Triggered before the browse page is rendered.

**Payload:**

- `$model` — The model class associated with the data to be displayed.

**Applicable:**

- **Browse Component**

**Example Use Case:**  
Inject additional variables or filters before rendering the browse page.

---

##### OnFormValidated

Triggered after the form validation process is completed.

**Payload:**

- `$model` — The model class associated with the validated data.
- `$data` — An array of the validated data.
- `$uuid` — The unique identifier (`uuid`) of the data. It can be `null` if the data comes from a new entry form.

**Applicable:**

- **Form Component**

**Example Use Case:**  
Perform additional actions or logging after validation passes.

---

##### OnFormGetData

Triggered after the form data is fetched from the table.

**Payload:**

- `$model` — The model class associated with the fetched data.
- `$data` — An array of the data being accessed.
- `$uuid` — The unique identifier (`uuid`) of the data.

**Applicable:**

- **Form Component**

**Example Use Case:**  
Transform or filter data before displaying it in the form.

---

##### OnFormGettingData

Triggered before the form data is fully fetched from the table.

**Payload:**

- `$model` — The model class associated with the data being accessed.
- `$uuid` — The unique identifier (`uuid`) of the data.

**Applicable:**

- **Form Component**

**Example Use Case:**  
Modify query parameters or add constraints before retrieving data.

---

##### OnFormSaved

Triggered after the form data is successfully saved to the table.

**Payload:**

- `$model` — The model class associated with the saved data.
- `$data` — An array of the saved data.
- `$id` — The unique identifier of the saved data.

**Applicable:**

- **Form Component**

**Example Use Case:**  
Send notifications or perform post-save actions like updating related records.

**Method Signature:**
```php
#[OnFormSaved]
public function onFormSaved($model, $data, $id)
{
    // Your logic here
    // $model: The model class (e.g., User::class)
    // $data: Array of saved form data
    // $id: Primary key value of the saved record
}
```

---

##### OnFormSaving

Triggered before the form data is saved to the table.

**Payload:**

- `$model` — The model class associated with the data to be saved.
- `$data` — An array of the data to be saved.
- `$id` — The unique identifier of the data. Can be `null` for new records.

**Applicable:**

- **Form Component**

**Example Use Case:**  
Validate additional conditions or set default values before saving.

**Method Signature:**
```php
#[OnFormSaving]
public function onFormSaving($model, $data, $id = null)
{
    // Your logic here
    // $model: The model class (e.g., User::class)
    // $data: Array of form data to be saved
    // $id: Primary key value (null for new records)
}
```

---

##### OnFormDehydrate

Triggered when the Livewire component is dehydrated.

**Payload:**

- `N/A`

**Applicable:**

- **Form Component**

---

##### OnFormInit

Triggered when the Livewire form component is initialized.

**Payload:**

- `$model` — The model class associated with the form.

**Applicable:**

- **Form Component**

---

##### OnFormMounted

Triggered after the Livewire mount process is completed.

**Payload:**

- `$model` — The model class associated with the form.

**Applicable:**

- **Form Component**

---

##### OnFormMounting

Triggered before the Livewire mount process is completed.

**Payload:**

- `$model` — The model class associated with the form.

**Applicable:**

- **Form Component**

---

##### OnFormHydrate

Triggered during the Livewire hydration process.

**Payload:**

- `N/A`

**Applicable:**

- **Form Component**

---

##### OnPropertyUpdated

Triggered when a property is updated by Livewire.

**Payload:**

- `N/A`

**Applicable:**

- **Form Component**

---

### Master Detail (Sub Module)
In CRUDBooster, creating sub-modules or master-detail modules is a straightforward process that enhances the functionality of your application. To illustrate this, let's consider an example where you have a `category` table and a `sub_category` table. The first step is to generate modules for both tables. This ensures that you have the necessary structure in place to manage the relationship between categories and subcategories effectively.

**Step-by-Step Guide to Creating Sub-Modules**

1. **Generate Modules**: Start by creating the modules for both the `category` and `sub_category` tables. You can do this using the appropriate CRUD commands in your terminal. This will create the necessary files and configurations for both modules.

2. **Menu Configuration**: After generating the modules, you have the flexibility to either display the sub-module (`sub_category`) in the menu or remove it entirely. This choice depends on how you want to structure your application's navigation.

3. **Integrating Sub-Modules**: The sub-module can be accessed from the parent or master module. To achieve this, navigate to the form component file of the master module, which in this case is the `Category` form. You will need to add the following code to integrate the sub-module:

```php
use CrudBooster\Components\MasterDetail\SubModule;
use App\Cb\Modules\Category\Livewire\Category;
use App\Cb\Modules\SubCategory\Livewire\SubCategory;

class CategoryForm extends BaseFormComponent
{
    //...

    public function init()
    {
        //... other initialization code here

        $this->addSubModule([
            SubModule::create(SubCategory::class)->foreignKey('category_id'),
            //... add other sub-modules if necessary
        ]);
    }

    //...
}
```

**Explanation of the Code**

- **SubModule Class**: In the `create(...)` function, you refer to the `SubModule` class. In the example above, this corresponds to the `SubCategory` module. It's important to note that in CRUDBooster, each module typically has two main class files: one for browsing and one for the form. When specifying the sub-module, you should include the browsing class, which is `SubCategory` (not `SubCategoryForm`).

- **Foreign Key Specification**: The next argument you need to provide is the `foreignKey`. In this example, it is set to `category_id`, which establishes the relationship between the `category` and `sub_category` tables. This foreign key is crucial for linking the sub-module to its parent module.

#### Display Modes

CRUDBooster provides two display modes for SubModules:

**1. Dialog Mode (Default)**
By default, SubModules open in a dialog/modal window:

```php
$this->addSubModule([
    SubModule::create(SubCategory::class)->foreignKey('category_id'),
]);
```

**2. Same Page Mode**
> This feature is available in CRUDBooster v7.9.12 and above.

You can make SubModules open in the same page instead of a dialog using the `openInPage()` method:

```php
$this->addSubModule([
    SubModule::create(SubCategory::class)->foreignKey('category_id')->openInPage(),
]);
```

When using `openInPage()`, the SubModule will:
- Open in the same page/tab instead of a modal dialog
- Provide proper navigation back to the parent detail page
- Maintain the parent-child relationship context
- Show breadcrumb navigation for better user experience

#### Navigation and URL Handling

SubModules support proper navigation between parent and child modules:

**Back Navigation**
- When viewing a SubModule, users can navigate back to the parent detail page
- The system maintains the relationship context throughout navigation
- URL parameters are properly preserved for seamless navigation

**URL Structure**
- SubModule URLs are properly constructed to maintain the parent-child relationship
- Encrypted parameters ensure secure navigation between modules
- The system automatically handles URL generation for all SubModule actions (browse, create, edit, detail)

#### Security Features

SubModules implement several security measures:

**Encrypted Parameters**
- Parent module information is encrypted when passed to SubModules
- This prevents URL tampering and unauthorized access
- Automatic validation ensures data integrity

**Permission Inheritance**
- SubModules inherit permissions from their parent modules
- Access control is automatically enforced based on user roles
- Foreign key relationships are validated to ensure data security

#### Advanced Usage

**Multiple SubModules**
You can add multiple SubModules to a single parent module:

```php
$this->addSubModule([
    SubModule::create(SubCategory::class)->foreignKey('category_id'),
    SubModule::create(CategoryImages::class)->foreignKey('category_id')->openInPage(),
    SubModule::create(CategoryTags::class)->foreignKey('category_id'),
]);
```

**Custom SubModule Configuration**
SubModules can be configured with various options:

```php
$this->addSubModule([
    SubModule::create(SubCategory::class)
        ->foreignKey('category_id')
        ->openInPage()
        ->title('Category Items'), // Custom title (if supported)
]);
```

#### Best Practices

1. **Choose the Right Display Mode**:
   - Use dialog mode for simple, quick operations
   - Use same page mode for complex operations requiring more screen space

2. **Navigation Consistency**:
   - Always provide clear navigation paths back to parent modules
   - Use consistent naming conventions for foreign keys

3. **Performance Considerations**:
   - SubModules are loaded on-demand to optimize performance
   - Consider pagination for SubModules with large datasets

4. **User Experience**:
   - Choose display modes based on the complexity of SubModule operations
   - Provide clear visual indicators of the current navigation context

**Visual Representation**

Once you have implemented the above code, you will be able to see the sub-module displayed within the parent module. When you access the master module (in this case, the Category module) and navigate to the detail page, the sub-module will be visible, allowing you to manage subcategories directly from the category interface. This integration provides a seamless user experience and enhances the overall functionality of your application.

By following these steps, you can effectively create and manage master-detail relationships in CRUDBooster, making your application more organized and user-friendly. If you have any further questions or need assistance, feel free to consult the documentation or reach out to the community!

<img width="700px" src="https://crudbooster.com/images/docs/master-detail.png">

### Custom Input Type

To create a custom input type, you need to pay attention to the following file and folder structure:

```bash
Date
|
--- Function
------ Date.php
--- views
------ form.blade.php
------ view.blade.php
--- DateServiceProvider.php
```

The input type you create must have the folder and file structure as shown above. This structure will help CRUDBooster
recognize the input type you create.

> We recommend creating custom input types in the `app/Cb/Types` folder. If the `Types` folder does not exist, you can
> create it first.

**Service Provider**

By creating a service provider, you can register the custom input type you created. Here is an example of a service
provider:

```php
// File: /Date/DateServiceProvider.php
namespace App\Cb\Types\CustomType;

use Illuminate\Support\ServiceProvider;
use CrudBooster\Components\Type\CBTypeRegistrar;

class CustomTypeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'custom-type');
        CBTypeRegistrar::addDateTime([
            'type'=>'custom-type', // <-- This is the type name
            'form'=>'custom-type::form', // <-- This is the form view
            'view'=>'custom-type::view', // <-- This is the view name 
            'clazz'=>Function\CustomType::class, // <-- This is the class name 
            'generalOption'=>true // <-- This is the general option
        ]);
    }
}
```

After creating the service provider, you must register it in the `\app\Providers\AppServiceProvider.php` at `register`
method so that this service provider is registered in the Laravel container.
Or if you are creating a package, you can place it in the `register` method of your package's Service Provider.

```php
// File: /app/Providers/AppServiceProvider.php
public function register()
{
    $this->app->register(\App\Cb\Types\CustomType\CustomTypeServiceProvider::class);
}
```

If the custom type you created is a Date & Time group, use `CBTypeRegistrar::addDateTime`. For other types, refer to the
following functions:

- `addWysiwyg`: WYSIWYG Editor
- `addText`: For text input
- `addNumeric`: For numeric input. This function is used for number, money, and decimal
- `addUpload`: For file upload input. This function is used for file and image
- `addPassword`: For password input
- `addJson`: For JSON input
- `addSelect`: For select input. This function is used for select, radio, and checkbox
- `addMap`: For map input

> Register the input type with CBTypeRegistrar is required, to make sure the CrudBooster can recognize the input type
> you created.

`generalOption` means that when you set it to true, the module builder will show default options for this type such as:

| Option                 | Description                                         |
|------------------------|-----------------------------------------------------|
| uppercase              | To make the input uppercase                         |
| lowercase              | To make the input lowercase                         |
| noSpace                | To remove space in the input                        |
| noSpecialChar          | To remove special characters in the input           |
| noSpecialChartAndSpace | To remove special characters and space in the input |
| numeric                | To make the input numeric                           |
| nonNumeric             | To remove numeric in the input                      |
| numberFormat           | To format the input as a number                     |
| phoneFormat            | To format the input as a phone number               |

Now you need to ensure that the input type you created supports HTML input attributes or not. Functionally, this option
will inject input and fill it with preg replace with a specific pattern.

**Form Template**

This template refers to the contents of the `/views/form.blade.php` file. This template will be used to display the form
input.
Now we will create the form input template. Here is an example code for creating a Date input type form:

```php
// File: /Date/views/form.blade.php
{{-- Only variable map $column that you can use. There are key: key, placeholder, label, helpText, etc --}}
<input type="date"
       id="{{$column['key']}}"
       {{ $focus ? 'autofocus': '' }}
        placeholder="{{$column['placeholder'] ?? ''}}"
        @readonly($column['readonly'] ?? false)
        wire:loading.attr="readonly"
        wire:target="formSave"
        @if(isset($column['live']))
        wire:model.live.debounce.{{$column['live']}}ms="formData.{{$column['key']}}"
        @else
        wire:model="formData.{{$column['key']}}"
        @endif
        class="form-control">
```

**Add Assets (CSS & JS)**

Now the question is how to add additional CSS or JS assets. It is quite easy, you can add the asset registrar in the
service provider of this input type:

```php
// File: /Date/DateServiceProvider.php
namespace App\Cb\Types\CustomType;

use Illuminate\Support\ServiceProvider;

class CustomTypeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'custom-type');
        CBTypeRegistrar::addDateTime([
            'type'=>'custom-type', // <-- This is the type name
            'form'=>'custom-type::form', // <-- This is the form view
            'view'=>'custom-type::view', // <-- This is the view name 
            'clazz'=>Function\CustomType::class, // <-- This is the class name 
            'generalOption'=>true // <-- This is the general option
        ]);
        
        // Register asset CSS
        \CrudBooster\Themes\CbThemeAssetRegistrar::addCss('https://path-to-your-css-file.css');
        // Register asset Javascript
        \CrudBooster\Themes\CbThemeAssetRegistrar::addJs('https://path-to-your-js-file.js');
    }
}
```

This will automatically inject CSS and JS assets into the header of the CrudBooster template.

**View Template**

Lastly, we will create the view template. This template refers to the contents of the `/views/view.blade.php` file. This
template will be used to display the view input.
Here is an example code for creating a Date input type view:

```php
{{-- There are variables $column, $value, and $formData that you can use --}}
<p>{{ $value ? date($column['option']['format'] ?? (config('cb.date_format') ?? 'Y-m-d'), strtotime($value)) : null }}</p>

```

**Type Option**

You can add additional options to the input type you created. Here is an example of adding an option to the input type:

```php
// file : /Date/Function/Date.php
namespace CrudBooster\Components\Type\Date\Function;

use CrudBooster\Components\Type\TypeOptionAbstract;

class Date extends TypeOptionAbstract
{
    /**
     * Set the date format
     * @param string $format E.g: Y-m-d
     * @return $this
     */
    public function format(string $format): static
    {
        $this->option['format'] = $format;
        return $this;
    }
}
```

This option can be used in both form input and view template, as shown in the view template example:

```php
{{-- There are variables $column, $value, and $formData that you can use --}}
<p>{{ $value ? date($column['option']['format'], strtotime($value)) : null }}</p>
```

Options can be accessed through the map array `$column['option']`.

Great job!, Now you have successfully created a custom input type. You can use this custom input type in the CRUDBooster
module builder.

```php
// Example of using custom input type in the Form Component

use CrudBooster\Livewire\FormBuilder\Form;
use App\Cb\Types\CustomType\Function\CustomType;

class UserForm extends BaseFormComponent
{
    // ...
    
    public function init(): void
    {
        $this->makeForm([
            Form::add(label: 'Date', key:'date', type: 'custom-type')->option(Date::option()->format('Y-m-d')),
        ]);
    }
}
```

### Custom Module

What you need to know is the module structure in CRUDBooster as follows:

```bash
User
|
--- Database
------ Migrations
--------- file_name_migration.php
--- Livewire
------ Name.php
------ NameForm.php
--- Models
------ Name.php
--- Services
------ NameService.php
--- views
--- ModuleServiceProvider.php
--- router.php
```

Then this folder should be placed in `/app/Cb/Modules/`. All modules in the `Modules` folder will be automatically
scanned by CRUDBooster to be registered as modules.
Creating a Custom Module means we do not need to create a module through the GUI feature of the Module Builder in the
CRUDBooster Admin Panel. We can create the module manually, and CRUDBooster will automatically detect the module.
However, creating a custom module does not automatically add it to the Module Builder menu, but it will be registered in
the CRUD Module options in Menu Management, so we can add the menu later.

**Migration**

If you have a table migration that you want to run, you can create a migration file in the `Database/migrations` folder,
following the usual Laravel migration structure. If you are not familiar with creating migration files, please refer to
the official Laravel migration documentation: [Laravel Migration](https://laravel.com/docs/11.x/migrations).

**Browse/Table Page**

To create a browse page, you can create a file in the `Livewire` folder named `Name.php`. This file will contain a
Livewire class that will be used to display data in a table.
Generally, this file contains the same content as the module created through the Module Builder. You can refer to
the `Browse Page` section above.

**Form Page & Detail Page**

To create a form page, you can create a file in the `Livewire` folder named `NameForm.php`. This file will contain a
Livewire class that will be used to display the form page.
Generally, this file contains the same content as the module created through the Module Builder. You can refer to
the `Form Page` section above.

**Model**

To create a model, you can create a file in the `Models` folder with the name `Name.php`. This file will contain the
model class that will be used to access data in the table.

**Service**

To create a service, you can create a file in the `Services` folder named `NameService.php`. This file will contain the
service class that will be used to access data in the table.
> Service is a design pattern used to separate business logic from the controller. So all business logic related to this
> module can be placed in the service.

**Views**

Views are only used if you want to create your own custom view, which I will discuss in the next section.

**Service Provider**

After you have prepared the files above, you need to create a service provider to register the module in CRUDBooster.
Here is an example of a service provider:

```php
// File: /User/ModuleServiceProvider.php
namespace App\Cb\Modules\User;

use Illuminate\Support\ServiceProvider;
use CrudBooster\Components\Module\CBModuleRegistrar;

class UserServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/router.php');
        ModuleRegistrar::registerModule(
                    key:'users', 
                    name:'Users', 
                    browseModuleClass: \App\Cb\Modules\User\Livewire\User::class,
                    formModuleClass: \App\Cb\Modules\User\Livewire\UserForm::class,
                    serviceProvider: self::class, 
                    additional: [
                    'permissionAvailable' => [
                        RolePermission::CREATE, RolePermission::READ, RolePermission::UPDATE, RolePermission::DELETE, 
                    ],
                ]);
    }
}
```

**Router**

The router file contains the routes for the module.

```php
// File: /User/router.php
use CrudBooster\Helpers\CBRoute;
use App\Cb\Modules\Users\Livewire\Users;
use App\Cb\Modules\Users\Livewire\UsersForm;

CBRoute::createRoute('users',
                        Users::class,
                        UsersForm::class);
```

In the example above, you can use the `CBRoute` helper to create routes for `browse`, `form`, and `detail` pages.
The first argument is the route path, the second argument is the Livewire class for the browse page, and the third
argument is the Livewire class for the form page. The detail page is included in the form page.

> Tips: with this modular concept, you can even create your own package to add new modules to CRUDBooster.

### Additional Top Bar Button

Top Bar Button is a button that is displayed at the top of the screen. You can add additional buttons to the top bar by
creating a service provider and registering the button in the service provider.

The following is directory structure:

```bash
YourTopBar
|
--- Livewire
------ YourTopBarButton.php
--- views
--- YourTopBarServiceProvider.php
```

This actually is the same with creating a component in livewire.

**Service Provider**

The service provider file is used to register the button in the top bar. Here is an example of a service provider:

```php
// File: /YourTopBar/YourTopBarServiceProvider.php
namespace App\Cb\TopBar\YourTopBar;

use Illuminate\Support\ServiceProvider;
use CrudBooster\Components\TopBar\TopBarRegistrar;
use App\Cb\TopBar\YourTopBar\Livewire\YourTopBarButton;

class YourTopBarServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'your-top-bar'); // <-- This is the view folder
        TopBarRegistrar::add([
            'name'=> 'your-top-bar-component', // <-- This is the name of component, should be unique, no namespace, no space, no special character, using kebab-case.
            'clazz'=> YourTopBarButton::class, // <-- This is the class name
            'order'=> 99 // <-- This is the order of the button from right to left
        ]);
    }
}
```

After creating the service provider file, register it in the `AppServiceProvider` in the `register` method so that this
service provider is registered in the Laravel container.

**Livewire Component**

The Livewire component file is used to create the button in the top bar. Here is an example of a Livewire component
file:

```php
// File: /YourTopBar/Livewire/YourTopBarButton.php
namespace App\Cb\TopBar\YourTopBar\Livewire;

use Livewire\Component;

class YourTopBarButton extends Component
{
    public function render()
    {
        return view('your-top-bar::button');
    }
}
```

Create a view file in the `views` folder named `button.blade.php`. This file will contain the button template.

### Role Permission Condition

Sometimes we need to add conditions for role permissions in a function. For example, we want to add a condition that only users with DELETE permission can click the additional `actionButton` we created.

```php
// ...
class User extends BaseBrowseComponent
{
    // ...
    
    public function init(): void
    {
        if(Gate::check('delete', 'users')) {
            $this->addActionButton(label: 'Delete All', url: function () {
                // your code here
            }, icon: Icon::TRASH);
        }
    }
}
```

Custom guards you can use besides delete are:

| Guard          | Description                                              |
|----------------|----------------------------------------------------------|
| create         | Guard for create                                         |
| read           | Guard for read                                           |
| update         | Guard for update                                         |
| delete         | Guard for delete                                         |
| is_super_admin | Guard to check if the user is a super admin or not       |

### Add Additional Setting

This feature allows you to add your settings to the CRUDBooster settings page without modifying the module settings. This is very useful for creating standalone and isolated settings pages, even when you build your own package, you can insert settings into this settings page.

If you have a module that you have created and want to add special settings for that module, you can place the settings in the module directory. Here is an example:
```bash
/app/Cb/Modules/Users/Setting/
```
Then please arrange the file structure as follows:
```bash
Setting
|
-- UserSettingServiceProvider.php
-- Livewire
---- UserSetting.php
-- views
---- form.blade.php
```

**Service Provider**

The service provider file is used to register the setting in the CRUDBooster settings page. Here is an example of a service provider:

```php
// File: /Users/Setting/UserSettingServiceProvider.php
namespace App\Cb\Modules\Users\Setting;

use Illuminate\Support\ServiceProvider;

class UserSettingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'user-setting');
        \CrudBooster\Modules\Setting\CbSettingRegistrar::add(key:'user-setting', option: [
            'label'=> 'User Setting', // <-- This is the label name
            'icon'=> Icon::COG, // <-- This is the svg icon
            'clazz'=> Livewire\UserSetting::class, // <-- This is the class name
            'order'=> 99 // <-- This to set the order of the setting
        ]);
    }
}
```
You can register this service provider to the related module service provider (in this example, it means the user service provider) at `register` method. Or 
you can register this service provider in the `AppServiceProvider` in the `register` method so that this service provider is registered in the Laravel container. Or in your package's service provider.

**Component Class**

The component class file is used to create the setting page. Here is an example of a Livewire component file:

```php
// File: /Users/Setting/Livewire/UserSetting.php
namespace App\Cb\Modules\Users\Setting\Livewire;

use CrudBooster\Modules\Setting\CbBaseSetting;

class UserSetting extends CbBaseSetting
{
    public $key = 'user-setting'; // <-- This is the key name
    
    public function render()
    {
        return view('user-setting::form');
    }
}
```
You need to extend `CbBaseSetting` so that the setting component can work properly.

**View File**

Create a view file in the `views` folder named `form.blade.php`. This file will contain the setting template.

Here is an example of creating a basic information setting page:    
```php
// File: /Users/Setting/views/form.blade.php
<div>
    @alertBottomRight
    <h1 class="text-2xl mb-10 flex justify-start items-center gap-2">{!! \CrudBooster\Components\Icon\Icon::BUILDING !!} Basic Information</h1>
    <div class="frame">
        <div class="frame-title">
            Basic Information
        </div>
        <div class="frame-content">
            <form wire:submit.prevent="save">
                <div class="form-group">
                    <label for="">App Name</label>
                    <input type="text" wire:model="form.app_name" class="form-control w-full lg:!w-1/2">
                    <div class="form-help">
                        This is the name of your application
                    </div>
                </div>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                        <input type="text" wire:model="form.company_name" placeholder="E.g: AI Company" class="form-control w-full lg:!w-1/2">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" wire:model="form.address" class="form-control">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" wire:model="form.phone" class="form-control w-full lg:!w-1/3">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" wire:model="form.email" class="form-control w-full lg:!w-1/3" placeholder="E.g: email@example.com">
                </div>
                <div class="flex flex-row justify-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
```
> This component is created using Livewire. For complete documentation on how Livewire works, you can refer to the official documentation [Livewire](https://livewire.laravel.com/docs/components).

**Implementing Settings in Real-World Applications**

To call the function we created in the settings, we can use the `setting` function provided by CrudBooster. Here is an example of using the setting in a class:

```php
    $fooBar = setting($nameKey, $formKey, $defaultValue);

    # In the example above, the $nameKey is user-setting, the $formKey can be: app_name, company_name, address, phone, email
    # $defaultValue is the default value if the setting is not found
    $fooBar = setting('user-setting', 'app_name', 'My App');    
```

---
### Inject Installer

Sometimes you need to run a command after installing the package. You can use `CbInstallRegistrar`. By registering your command with `CbInstallRegistrar`, the command will be executed during the installation process. Here is an example of using `CbInstallRegistrar`:

Create a service provider to register your command with `CbInstallRegistrar`. Or use an existing service provider, such as `AppServiceProvider` or the service provider in your package.

```php
use CrudBooster\Commands\CbInstallRegistrar;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        CbInstallRegistrar::add(key: 'seed-default-data', callback: function () {
            // ... execute your anything command here
            
        });
    }
}
```
Key should be unique and snake-case format, and callback is a closure that contains the command you want to execute. 
In this way, your command will be executed after the installation process is complete. This is useful when you are developing a package that requires additional installation steps.

> We recommend naming the key with your package prefix, e.g., package-name-default-data

---
### Cache Booster
Cache Booster is an amazing feature that can significantly improve page load times and reduce CPU load. Cache Booster uses standardized cache headers to instruct the browser to retrieve data from their local cache. With an intelligent algorithm, Cache Booster can automatically invalidate the cache when data changes.

Here is how to enable Cache Booster in your CRUDBooster project.
Open the `.env` file and add the following 2 configurations:
```dotenv
CACHE_BOOSTER=true
CACHE_BOOSTER_EXPIRY=5
```
The expiry above is in minutes.

Next, you need to add this middleware globally in Laravel. Open the `bootstrap/app.php` file and add the following middleware:
```php
  //...
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->append(\CrudBooster\CacheBooster\Optimize::class);
  })
  //...
```

> All links will contain the query parameter `?v={version}` which is necessary to force the browser to fetch new data from the server or maintain the state in cached conditions.
> Don't forget to run `php artisan config:cache` after adding the above config.
> Cache Booster is still in experimental version, you may encounter bugs or unwanted behavior, such as in certain conditions you may need to manually refresh the browser to get the latest data.


# **Deployment**

## Shared Hosting cPanel

Deploying your CRUDBooster application is an essential step to make it accessible to users. One common deployment method is using shared hosting with cPanel. Below are the steps to successfully deploy your CRUDBooster application on a shared hosting environment using cPanel.

1. **Prepare Your Application for Deployment**:
  - Before deploying, ensure that your application is ready for production. This includes:
    - Setting the environment variable in your `.env` file to `APP_ENV=production`.
    - Running `php artisan config:cache` to cache your configuration settings.
    - Running `php artisan route:cache` to cache your routes for better performance.
    - Running `php artisan view:cache` to compile your Blade views.

2. **Compress Your Application Files**:
  - Compress your Laravel project files into a `.zip` file. Make sure to exclude unnecessary files and directories, such as:
    - The `node_modules` directory (if applicable).
    - The `vendor` directory (you will install dependencies on the server).
    - Any local development files that are not needed in production.

3. **Upload Your Application to cPanel**:
  - Log in to your cPanel account.
  - Navigate to the **File Manager**.
  - Go to the directory where you want to deploy your application (usually the `public_html` directory).
  - Upload the `.zip` file you created earlier.
  - Once uploaded, right-click on the file and select **Extract** to unpack the files.

4. **Set Up the Database**:
  - In cPanel, navigate to **MySQL Databases**.
  - Create a new database and a new database user. Make sure to grant the user all privileges to the database.
  - Import your database schema and data using **phpMyAdmin**:
    - Go to **phpMyAdmin** in cPanel.
    - Select the newly created database.
    - Click on the **Import** tab and upload your SQL file.

5. **Configure the `.env` File**:
  - After extracting your files, locate the `.env` file in the root of your Laravel project.
  - Update the database connection settings in the `.env` file to match the database you created in cPanel:
    ```plaintext
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```

6. **Set the Document Root**:
  - In cPanel, ensure that the document root points to the `public` directory of your Laravel application. This is crucial because the `public` directory contains the `index.php` file that serves as the entry point for your application.
  - If your hosting provider allows it, you can set the document root for your domain to point to `your_project_directory/public`.

7. **Install Composer Dependencies**:
  - If your hosting provider supports SSH access, you can log in via SSH and navigate to your project directory. Run the following command to install the necessary Composer dependencies:
    ```bash
    composer install --no-dev
    ```
  - If SSH access is not available, you can run `composer install` locally and upload the `vendor` directory to your server.

8. **Set Permissions**:
  - Ensure that the `storage` and `bootstrap/cache` directories are writable by the web server. You can set the permissions using the File Manager or via SSH:
    ```bash
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    ```

9. **Access Your Application**:
  - Once everything is set up, you can access your CRUDBooster application by navigating to your domain in a web browser. If everything is configured correctly, you should see your application running.

## VPS

Deploying your CRUDBooster application is an essential step to make it accessible to users. One common deployment method is using a Virtual Private Server (VPS). Below are the steps to successfully deploy your CRUDBooster application on a VPS.

1. **Prepare Your VPS**:
  - Ensure that your VPS is set up with a compatible operating system (e.g., Ubuntu, CentOS) and has the necessary software installed, including:
    - PHP (version 8.2 or higher)
    - Composer
    - A web server (e.g., Apache or Nginx)
    - MySQL or MariaDB
    - Git (optional, for version control)

2. **Connect to Your VPS**:
  - Use SSH to connect to your VPS. Open your terminal and run:
    ```bash
    ssh username@your_vps_ip
    ```
  - Replace `username` with your VPS username and `your_vps_ip` with the IP address of your VPS.

3. **Install Required Software**:
  - If you haven't already installed the necessary software, you can do so using the package manager. For example, on Ubuntu, you can run:
    ```bash
    sudo apt update
    sudo apt install php php-cli php-mbstring php-xml php-mysql php-zip unzip git
    ```
  - Install Composer globally:
    ```bash
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    ```

4. **Clone Your Application**:
  - Navigate to the directory where you want to deploy your application (e.g., `/var/www/html`):
    ```bash
    cd /var/www/html
    ```
  - Clone your CRUDBooster application from your version control repository (e.g., GitHub). Ensure the repository is in private mode as per the terms and conditions:
    ```bash
    git clone https://username:password@github.com/yourusername/your-repo.git your-project-name
    ```
  - Replace `yourusername` and `your-repo` with your GitHub username and repository name.

5. **Install Dependencies**:
  - Navigate into your project directory:
    ```bash
    cd your-project-name
    ```
  - Run Composer to install the necessary dependencies:
    ```bash
    composer install --no-dev
    ```

6. **Set Up the Database**:
  - Create a new database and user for your application using MySQL or MariaDB:
    ```sql
    CREATE DATABASE crudbooster;
    CREATE USER 'your_db_user'@'localhost' IDENTIFIED BY 'your_db_password';
    GRANT ALL PRIVILEGES ON crudbooster.* TO 'your_db_user'@'localhost';
    FLUSH PRIVILEGES;
    ```
  - Import your database schema and data using `phpMyAdmin` or the MySQL command line.

7. **Configure the `.env` File**:
  - Update the `.env` file in your project directory with your database connection settings:
    ```plaintext
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=crudbooster
    DB_USERNAME=your_db_user
    DB_PASSWORD=your_db_password
    ```

8. **Set Permissions**:
  - Ensure that the `storage` and `bootstrap/cache` directories are writable by the web server:
    ```bash
    sudo chown -R www-data:www-data storage
    sudo chown -R www-data:www-data bootstrap/cache
    sudo chmod -R 775 storage
    sudo chmod -R 775 bootstrap/cache
    ```

9. **Configure the Web Server**:
  - If you are using Apache, create a new virtual host configuration file:
    ```bash
    sudo nano /etc/apache2/sites-available/your-project-name.conf
    ```
  - Add the following configuration:
    ```apache
    <VirtualHost *:80>
        ServerName yourdomain.com
        DocumentRoot /var/www/html/your-project-name/public

        <Directory /var/www/html/your-project-name/public>
            AllowOverride All
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>
    ```
  - Enable the new site and rewrite module:
    ```bash
    sudo a2ensite your-project-name.conf
    sudo a2enmod rewrite
    sudo systemctl restart apache2
    ```

  - If you are using Nginx, create a new server block configuration file:
    ```bash
    sudo nano /etc/nginx/sites-available/your-project-name
    ```
  - Add the following configuration:
    ```nginx
    server {
        listen 80;
        server_name yourdomain.com;

        root /var/www/html/your-project-name/public;
        index index.php index.html index.htm;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Adjust PHP version as necessary
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.ht {
            deny all;
        }
    }
    ```
  - Enable the new site and restart Nginx:
    ```bash
    sudo ln -s /etc/nginx/sites-available/your-project-name /etc/nginx/sites-enabled/
    sudo systemctl restart nginx
    ```

10. **Access Your Application**:
  - After completing the setup, you can access your CRUDBooster application by navigating to your domain in a web browser. If everything is configured correctly, your application should be live and accessible.



