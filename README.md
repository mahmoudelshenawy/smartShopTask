# 🛒 Simple E-Commerce — Laravel + Livewire

A simple product catalog and cart application built with Laravel, Livewire, Alpine.js, and AI-powered recommendations via the Gemini API.

---

## 🧰 Tech Stack

- **Laravel 13** — Backend framework
- **Livewire 4** — Reactive UI components
- **Alpine.js** — Lightweight frontend interactivity (bundled with Livewire)
- **Tailwind CSS** — Utility-first styling
- **Google Gemini API** — AI-powered product recommendations

---

## ⚙️ Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/your-username/simple-e-commerce.git
cd simple-e-commerce
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Copy the environment file

```bash
cp .env.example .env
```

### 5. Generate the application key

```bash
php artisan key:generate
```

### 6. Configure your database

Open `.env` and update the database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simple_ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Add your Gemini API key

Get a free key at [https://aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey), then add it to `.env`:

```env
GEMINI_API_KEY=your-gemini-api-key-here
```

### 8. Run migrations

```bash
php artisan migrate
```

### 9. Seed the database

```bash
php artisan db:seed
```

This will insert 30 products across 5 categories:

- ⌨️ Mechanical Keyboards (6 products)
- 🖱️ Gaming Mice (6 products)
- 🖥️ Monitors (6 products)
- 🎧 Headphones (6 products)
- 🪑 Desk Accessories & Ergonomics (6 products)

### 10. Build frontend assets

```bash
npm run dev
```

Or for production:

```bash
npm run build
```

### 11. Start the development server

```bash
php artisan serve
```

Visit [http://localhost:8000/products](http://localhost:8000/products)

---

## 📁 Key Project Structure

```
app/
├── Livewire/
│   ├── ProductList.php           # Product listing with search + pagination
│   ├── ProductShow.php           # Product detail page + cart toggle
│   └── Products/
│       └── ProductRecommendations.php  # Lazy-loaded AI recommendations
├── Services/
│   ├── CartService.php           # Session-based cart (add, remove, quantity)
│   └── ViewedProductsService.php # Tracks last 3 viewed products in session
resources/
└── views/
    └── livewire/
        ├── product-list.blade.php
        ├── product-show.blade.php
        ├── product-recommendations.blade.php
        └── cart/
            └── cart-page.blade.php
```

---

## 🤖 AI Recommendation System

### Which API and why?

This project uses the **Google Gemini API** (`gemini-2.0-flash` model) for product recommendations.

**Why Gemini?**

- ✅ **Free tier available** — 15 requests/min and 1,500 requests/day at no cost
- ✅ **No billing required** to get started
- ✅ **Fast** — `gemini-2.0-flash` is optimized for low latency
- ✅ **Simple REST API** — no SDK required, works with Laravel's `Http` facade

### How it works

```
User views a product
        ↓
ViewedProductsService stores the product ID in session
(keeps last 3, newest first, no duplicates)
        ↓
User navigates to another product detail page
        ↓
ProductRecommendations component loads lazily (after page paint)
        ↓
Skeleton placeholder shown while request is in-flight
        ↓
Gemini API called with viewed product titles + full product list
        ↓
Gemini returns 3 recommended product titles as a JSON array
        ↓
Titles matched back to DB records → displayed to user
        ↓
Result cached for 2 hours (keyed by viewed IDs + current product)
        ↓
If API fails or returns bad data → 3 random products shown as fallback
```

### Example prompt sent to the API

```
You are a product recommendation engine.

The user recently viewed these products:
Keychron K2 Pro, Ducky One 3 TKL

From the list below, suggest exactly 3 similar or complementary products.
Return ONLY a valid JSON array of product titles exactly as they appear in the list, nothing else.
Example: ["Product A", "Product B", "Product C"]

Product list:
- Keychron K2 Pro
- Logitech MX Mechanical Mini
- Ducky One 3 TKL
- HHKB Professional Hybrid
- Glorious GMMK Pro
- Anne Pro 2
- Logitech G Pro X Superlight 2
- Razer DeathAdder V3 HyperSpeed
- Zowie EC2-C
- SteelSeries Aerox 5 Wireless
- Glorious Model O Wireless
- Endgame Gear XM1r
- LG 27GP850-B
... (all 30 products)
```

### Example API response

```json
["HHKB Professional Hybrid", "Anne Pro 2", "Glorious GMMK Pro"]
```

These titles are then matched against the database using `whereIn('title', $titles)` to retrieve the full product records.

---

## 🛒 Cart System

The cart is session-based with no database involvement:

| Action      | Behavior                                                       |
| ----------- | -------------------------------------------------------------- |
| Add to cart | Stores `productId => quantity` in session                      |
| Remove      | Removes entry from session array                               |
| Increment   | Increases quantity by 1                                        |
| Decrement   | Decreases quantity by 1 (minimum: 1)                           |
| Clear       | Calls `session()->forget()`                                    |
| Checkout    | Validates fake card details → clears cart → shows confirmation |

The checkout is **simulated** — no real payment gateway is used. Any 16-digit card number, valid MM/YY expiry, and 3-digit CVV will pass validation.

---

## 🔍 Search

The search bar on the products listing page uses both Alpine.js and Livewire:

- **Alpine.js** — manages focus ring color, shows/hides the clear (×) button
- **Livewire** — runs the actual `LIKE` query against `title` and `description` with a 400ms debounce

---

## 📝 Notes

- Recommendation results are cached for 2 hours to avoid hitting Gemini rate limits during development
- The `viewed_products` session key stores up to 3 product IDs, newest first
- The `added_to_cart` session key stores `[productId => quantity]` pairs
- All product images use [placehold.co](https://placehold.co) placeholders with the product title encoded into the URL
