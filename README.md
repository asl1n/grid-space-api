# GridSpace API (Hosted Copy)

This is a **hosted copy** of the GridSpace API, originally developed by **Kripalshr**. The original source code is available at:
👉 **[GitHub - Kripalshr/grid-space-api](https://github.com/Kripalshr/grid-space-api.git)**

I have copied and hosted this repository to ensure availability and easy deployment. Full credit for the backend development goes to **Kripalshr** ([GitHub Profile](https://github.com/Kripalshr)).

## Repository Links
- **Frontend:** [GitHub - asl1n/GridSpace](https://github.com/asl1n/GridSpace.git)
- **Backend (Hosted Copy by asl1n):** [GitHub - asl1n/grid-space-api](https://github.com/asl1n/grid-space-api.git)
- **Backend (Original by Kripalshr):** [GitHub - Kripalshr/grid-space-api](https://github.com/Kripalshr/grid-space-api.git)

## Backend Setup

### Prerequisites
Ensure you have the following installed:
- PHP (>= 8.0)
- Composer
- MySQL or any compatible database
- Laravel dependencies

### Installation Steps

1. **Clone this repository**:
   ```sh
   git clone https://github.com/asl1n/grid-space-api.git
   ```
2. **Navigate to the project folder**:
   ```sh
   cd grid-space-api
   ```
3. **Install dependencies**:
   ```sh
   composer install
   ```
4. **Create a `.env` file**:
   ```sh
   cp .env.example .env
   ```
5. **Configure the database**:
   - Open `.env` and set `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` according to your database settings.
   - Example:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=grid_space_laravel
     DB_USERNAME=root
     DB_PASSWORD=
     ```
   - Create the database manually in MySQL (or any other database tool).

6. **Generate the application key**:
   ```sh
   php artisan key:generate
   ```
7. **Run migrations and seed the database**:
   ```sh
   php artisan migrate --seed
   ```
8. **Start the server**:
   ```sh
   php artisan serve
   ```

Now, the API should be running at `http://127.0.0.1:8000/api`.

## API Usage

The API is used by the **GridSpace Frontend** to manage user authentication, bookings, and memberships.
Make sure to configure your frontend `.env` file to match your backend URL:

```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

For production deployments, replace `localhost` with the live server URL.

## Additional Notes
- If you face issues, check logs:
  ```sh
  tail -f storage/logs/laravel.log
  ```
- Ensure that all dependencies are installed correctly.
- Always **pull the latest changes** if the original backend repo is updated.

