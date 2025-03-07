# Nested CMS

A dynamic CMS built with **Laravel 11** (backend) and **Vue 3** (frontend) to manage nested pages with unlimited depth. This project demonstrates how to handle dynamic routing, nested page structures, and a collapsible tree view for visualizing the hierarchy.

---

## Features

- **Dynamic Nested Pages**:
  - Pages can be nested to any depth.
  - Each page has a `parent_id` to establish parent-child relationships.
  - Unique content resolution based on the full slug path.

- **Dynamic Routing**:
  - Routes like `/page1`, `/page1/page2`, and `/page1/page2/page3` dynamically resolve to the correct page content.

- **CRUD Operations**:
  - Create, read, update, and delete pages with nested relationships.
  - Deleting a parent page cascades to its children.

- **Tree Visualization**:
  - A collapsible tree view to display the nested page structure.
  - Dynamic updates when pages are added, edited, or deleted.

- **API Endpoints**:
  - RESTful API for managing pages.
  - Dynamic route resolution for slug-based content.

---

## Technologies Used

- **Backend**: Laravel 11 (PHP 8+)
- **Frontend**: Vue 3 (Composition API)
- **Database**: MySQL
- **Tools**: Composer, NPM, Git

---

## Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/krushilshah/nested-cms.git
cd nested-cms

composer install

npm install

cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nested_cms
DB_USERNAME=root
DB_PASSWORD=

php artisan key:generate

php artisan migrate

php artisan serve

npm run dev